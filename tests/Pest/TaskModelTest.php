<?php

use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Task Model', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
    });

    it('belongs to a user', function () {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        expect($task->user)->toBeInstanceOf(User::class);
        expect($task->user->id)->toBe($this->user->id);
    });

    it('belongs to a category', function () {
        $task = Task::factory()->create(['category_id' => $this->category->id]);

        expect($task->category)->toBeInstanceOf(Category::class);
        expect($task->category->id)->toBe($this->category->id);
    });

    it('can have null category', function () {
        $task = Task::factory()->create(['category_id' => null]);

        expect($task->category)->toBeNull();
    });

    it('casts due_date to datetime', function () {
        $task = Task::factory()->create([
            'due_date' => '2024-12-25 10:30:00',
        ]);

        expect($task->due_date)->toBeInstanceOf(\Carbon\Carbon::class);
    });

    it('casts is_completed to boolean', function () {
        $task = Task::factory()->create(['is_completed' => 1]);

        expect($task->is_completed)->toBeTrue();
    });

    it('has due soon scope', function () {
        // Task due in 2 days (should be included)
        Task::factory()->create([
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(2),
            'is_completed' => false,
        ]);

        // Task due in 5 days (should not be included)
        Task::factory()->create([
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(5),
            'is_completed' => false,
        ]);

        // Completed task due in 2 days (should not be included)
        Task::factory()->create([
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(2),
            'is_completed' => true,
        ]);

        $dueSoonTasks = Task::dueSoon()->get();

        expect($dueSoonTasks)->toHaveCount(1);
    });

    it('has category scope', function () {
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

        $tasksInCategory1 = Task::byCategory($category1->id)->get();
        $tasksInCategory2 = Task::byCategory($category2->id)->get();

        expect($tasksInCategory1)->toHaveCount(1);
        expect($tasksInCategory2)->toHaveCount(1);
    });

    it('returns null for category scope when no category provided', function () {
        Task::factory()->count(3)->create(['user_id' => $this->user->id]);

        $allTasks = Task::byCategory(null)->get();

        expect($allTasks)->toHaveCount(3);
    });

    it('has is_due_soon accessor', function () {
        $dueSoonTask = Task::factory()->create([
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(2),
            'is_completed' => false,
        ]);

        $notDueSoonTask = Task::factory()->create([
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(10),
            'is_completed' => false,
        ]);

        // Debug: Check the actual values
        $dueSoonTask->refresh();
        $notDueSoonTask->refresh();
        
        expect($dueSoonTask->is_due_soon)->toBeTrue();
        expect($notDueSoonTask->is_due_soon)->toBeFalse();
    });

    it('has priority_label accessor', function () {
        $highPriorityTask = Task::factory()->create(['priority' => 3]);
        $mediumPriorityTask = Task::factory()->create(['priority' => 2]);
        $lowPriorityTask = Task::factory()->create(['priority' => 1]);

        expect($highPriorityTask->priority_label)->toBe('High');
        expect($mediumPriorityTask->priority_label)->toBe('Medium');
        expect($lowPriorityTask->priority_label)->toBe('Low');
    });

    it('has fillable attributes', function () {
        $task = new Task();

        expect($task->getFillable())->toContain('user_id', 'category_id', 'title', 'description', 'due_date', 'is_completed', 'priority');
    });

    it('can be created with factory', function () {
        $task = Task::factory()->create();

        expect($task)->toBeInstanceOf(Task::class);
        expect($task->id)->not->toBeNull();
    });

    it('respects user isolation', function () {
        $otherUser = User::factory()->create();
        
        Task::factory()->create(['user_id' => $this->user->id]);
        Task::factory()->create(['user_id' => $otherUser->id]);

        $userTasks = Task::where('user_id', $this->user->id)->get();

        expect($userTasks)->toHaveCount(1);
    });
});
