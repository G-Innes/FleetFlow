<?php

use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Task Management', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
    });

    it('allows authenticated users to view their tasks', function () {
        Task::factory()->create(['user_id' => $this->user->id]);
        Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get('/tasks');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Tasks/Index'));
    });

    it('prevents users from seeing other users tasks', function () {
        $otherUser = User::factory()->create();
        Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->get('/tasks');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Tasks/Index')
                ->has('tasks.data', 0)
        );
    });

    it('allows users to create tasks', function () {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Task description',
            'due_date' => now()->addDays(7)->format('Y-m-d\TH:i'),
            'priority' => 2,
            'category_id' => $this->category->id,
        ];

        $response = $this->actingAs($this->user)->post('/tasks', $taskData);

        $response->assertRedirect('/tasks');
        $this->assertDatabaseHas('tasks', [
            'user_id' => $this->user->id,
            'title' => 'Test Task',
            'description' => 'Task description',
            'priority' => 2,
            'category_id' => $this->category->id,
        ]);
    });

    it('validates required fields when creating tasks', function () {
        $response = $this->actingAs($this->user)->post('/tasks', []);

        $response->assertSessionHasErrors(['title']);
    });

    it('allows users to update their own tasks', function () {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'title' => 'Updated Task Title',
            'description' => 'Updated description',
            'due_date' => now()->addDays(14)->format('Y-m-d\TH:i'),
            'priority' => 3,
            'category_id' => $this->category->id,
        ];

        $response = $this->actingAs($this->user)->put("/tasks/{$task->id}", $updateData);

        $response->assertRedirect('/tasks');
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task Title',
            'priority' => 3,
        ]);
    });

    it('prevents users from updating other users tasks', function () {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->put("/tasks/{$task->id}", [
            'title' => 'Hacked Task',
        ]);

        $response->assertStatus(403);
    });

    it('allows users to delete their own tasks', function () {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->delete("/tasks/{$task->id}");

        $response->assertRedirect('/tasks');
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    });

    it('prevents users from deleting other users tasks', function () {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->delete("/tasks/{$task->id}");

        $response->assertStatus(403);
    });

    it('allows users to toggle task completion', function () {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'is_completed' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->withHeaders(['X-Inertia' => 'true'])
            ->patch("/tasks/{$task->id}/toggle");

        $response->assertStatus(303);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'is_completed' => true,
        ]);
    });

    it('filters tasks by category', function () {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category1->id,
        ]);
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category2->id,
        ]);

        $response = $this->actingAs($this->user)->get("/tasks?category={$category1->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Tasks/Index')
                ->has('tasks.data', 1)
        );
    });

    it('filters tasks by completion status', function () {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'is_completed' => true,
        ]);
        Task::factory()->create([
            'user_id' => $this->user->id,
            'is_completed' => false,
        ]);

        $response = $this->actingAs($this->user)->get('/tasks?status=completed');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Tasks/Index')
                ->has('tasks.data', 1)
        );
    });

    it('filters tasks by due soon', function () {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(2),
            'is_completed' => false,
        ]);
        Task::factory()->create([
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(10),
            'is_completed' => false,
        ]);

        $response = $this->actingAs($this->user)->get('/tasks?due_soon=1');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Tasks/Index')
                ->has('tasks.data', 1)
        );
    });

    it('paginates tasks correctly', function () {
        Task::factory()->count(15)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get('/tasks');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Tasks/Index')
                ->has('tasks.data', 10)
                ->has('tasks.links')
        );
    });
});
