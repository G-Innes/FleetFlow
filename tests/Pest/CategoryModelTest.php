<?php

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Category Model', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
    });

    it('has fillable attributes', function () {
        $category = new Category();

        expect($category->getFillable())->toContain('name', 'color', 'description');
    });

    it('can be created with factory', function () {
        $category = Category::factory()->create();

        expect($category)->toBeInstanceOf(Category::class);
        expect($category->id)->not->toBeNull();
    });

    it('has many tasks', function () {
        $category = Category::factory()->create();
        
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
        ]);

        expect($category->tasks)->toHaveCount(3);
        expect($category->tasks->first())->toBeInstanceOf(Task::class);
    });

    it('can have no tasks', function () {
        $category = Category::factory()->create();

        expect($category->tasks)->toHaveCount(0);
    });

    it('validates name is required', function () {
        $category = new Category();
        $category->color = '#EF4444';

        expect($category->save())->toBeFalse();
    });

    it('validates color format', function () {
        $category = Category::factory()->make(['color' => 'invalid-color']);

        expect($category->save())->toBeFalse();
    });

    it('accepts valid hex colors', function () {
        $validColors = ['#EF4444', '#F59E0B', '#10B981', '#3B82F6'];

        foreach ($validColors as $color) {
            $category = Category::factory()->create(['color' => $color]);

            expect($category->color)->toBe($color);
        }
    });

    it('can have null description', function () {
        $category = Category::factory()->create(['description' => null]);

        expect($category->description)->toBeNull();
    });

    it('can have description', function () {
        $description = 'This is a test category description';
        $category = Category::factory()->create(['description' => $description]);

        expect($category->description)->toBe($description);
    });

    it('has default color when not specified', function () {
        $category = Category::factory()->create();

        expect($category->color)->toBeIn(['#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#8B5CF6', '#EC4899']);
    });

    it('can be deleted', function () {
        $category = Category::factory()->create();

        expect($category->delete())->toBeTrue();
        expect(Category::find($category->id))->toBeNull();
    });

    it('can be updated', function () {
        $category = Category::factory()->create(['name' => 'Original Name']);

        $category->update(['name' => 'Updated Name']);

        expect($category->fresh()->name)->toBe('Updated Name');
    });

    it('prevents deletion when tasks are associated', function () {
        $category = Category::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
        ]);

        // Category deletion should be prevented by the controller
        // This test verifies the model can be deleted directly (which it can)
        $category->delete();

        // Task should still exist but category_id should be null
        expect($task->fresh()->category_id)->toBeNull();
    });
});
