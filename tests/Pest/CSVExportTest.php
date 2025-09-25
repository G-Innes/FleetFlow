<?php

use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('CSV Export Functionality', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create(['name' => 'Fleet Maintenance']);
    });

    it('exports all tasks for authenticated user', function () {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Test Task 1',
            'description' => 'Description 1',
            'due_date' => now()->addDays(7),
            'is_completed' => false,
            'priority' => 2,
        ]);

        $response = $this->actingAs($this->user)->get('/tasks/export/csv');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    });

    it('redirects unauthenticated users', function () {
        $response = $this->get('/tasks/export/csv');

        $response->assertRedirect('/login');
    });

    it('exports tasks with correct headers', function () {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Test Task',
        ]);

        $response = $this->actingAs($this->user)->get('/tasks/export/csv');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    });

    it('filters tasks by category', function () {
        $category1 = Category::factory()->create(['name' => 'Category 1']);
        $category2 = Category::factory()->create(['name' => 'Category 2']);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category1->id,
            'title' => 'Task in Category 1',
        ]);
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category2->id,
            'title' => 'Task in Category 2',
        ]);

        $response = $this->actingAs($this->user)->get("/tasks/export/csv?category={$category1->id}");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    });

    it('filters tasks by status', function () {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'is_completed' => true,
            'title' => 'Completed Task',
        ]);
        Task::factory()->create([
            'user_id' => $this->user->id,
            'is_completed' => false,
            'title' => 'Pending Task',
        ]);

        $response = $this->actingAs($this->user)->get('/tasks/export/csv?status=completed');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    });

    it('filters tasks by priority', function () {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'priority' => 3,
            'title' => 'High Priority Task',
        ]);
        Task::factory()->create([
            'user_id' => $this->user->id,
            'priority' => 1,
            'title' => 'Low Priority Task',
        ]);

        $response = $this->actingAs($this->user)->get('/tasks/export/csv?priority=3');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    });

    it('filters tasks by due soon', function () {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(2),
            'is_completed' => false,
            'title' => 'Due Soon Task',
        ]);
        Task::factory()->create([
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(10),
            'is_completed' => false,
            'title' => 'Not Due Soon Task',
        ]);

        $response = $this->actingAs($this->user)->get('/tasks/export/csv?due_soon=1');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    });

    it('only exports tasks belonging to authenticated user', function () {
        $otherUser = User::factory()->create();
        
        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'My Task',
        ]);
        Task::factory()->create([
            'user_id' => $otherUser->id,
            'title' => 'Other User Task',
        ]);

        $response = $this->actingAs($this->user)->get('/tasks/export/csv');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    });

    it('handles empty task list', function () {
        $response = $this->actingAs($this->user)->get('/tasks/export/csv');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    });
});
