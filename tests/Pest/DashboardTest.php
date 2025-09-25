<?php

use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Dashboard Functionality', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
    });

    it('displays dashboard for authenticated users', function () {
        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Dashboard'));
    });

    it('redirects unauthenticated users to login', function () {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    });

    it('displays correct task statistics', function () {
        // Create tasks with different statuses
        Task::factory()->create([
            'user_id' => $this->user->id,
            'is_completed' => false,
        ]);
        Task::factory()->create([
            'user_id' => $this->user->id,
            'is_completed' => true,
        ]);
        Task::factory()->create([
            'user_id' => $this->user->id,
            'is_completed' => true,
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Dashboard')
                ->where('stats.totalTasks', 3)
                ->where('stats.completedTasks', 2)
        );
    });

    it('displays due soon tasks correctly', function () {
        // Create tasks due soon
        Task::factory()->create([
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(2),
            'is_completed' => false,
        ]);
        Task::factory()->create([
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(1),
            'is_completed' => false,
        ]);
        
        // Create task not due soon
        Task::factory()->create([
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(10),
            'is_completed' => false,
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Dashboard')
                ->where('stats.dueSoon', 2)
        );
    });

    it('displays overdue tasks correctly', function () {
        // Create overdue task
        Task::factory()->create([
            'user_id' => $this->user->id,
            'due_date' => now()->subDays(1),
            'is_completed' => false,
        ]);
        
        // Create completed task (should not count as overdue)
        Task::factory()->create([
            'user_id' => $this->user->id,
            'due_date' => now()->subDays(1),
            'is_completed' => true,
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Dashboard')
                ->where('stats.overdue', 1)
        );
    });

    it('displays recent tasks', function () {
        $category = Category::factory()->create();
        
        $task1 = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
            'title' => 'Recent Task 1',
        ]);
        $task2 = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
            'title' => 'Recent Task 2',
        ]);
        
        // Ensure task2 is created after task1
        $task2->update(['created_at' => now()->addMinute()]);

        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Dashboard')
                ->has('recentTasks', 2)
                ->where('recentTasks.0.title', 'Recent Task 1') // Most recent first
        );
    });

    it('displays due soon tasks with category information', function () {
        $category = Category::factory()->create(['name' => 'Fleet Maintenance']);
        
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
            'due_date' => now()->addDays(2),
            'is_completed' => false,
            'title' => 'Due Soon Task',
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Dashboard')
                ->has('dueSoonTasks', 1)
                ->where('dueSoonTasks.0.title', 'Due Soon Task')
                ->where('dueSoonTasks.0.category', 'Fleet Maintenance')
        );
    });

    it('limits due soon tasks to 3', function () {
        // Create 5 tasks due soon
        for ($i = 1; $i <= 5; $i++) {
            Task::factory()->create([
                'user_id' => $this->user->id,
                'due_date' => now()->addDays($i),
                'is_completed' => false,
                'title' => "Due Soon Task {$i}",
            ]);
        }

        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Dashboard')
                ->has('dueSoonTasks', 3) // Should be limited to 3
        );
    });

    it('displays categories for quick actions', function () {
        Category::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Dashboard')
                ->has('categories', 3)
        );
    });

    it('shows zero stats for new users', function () {
        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Dashboard')
                ->where('stats.totalTasks', 0)
                ->where('stats.completedTasks', 0)
                ->where('stats.dueSoon', 0)
                ->where('stats.overdue', 0)
        );
    });
});
