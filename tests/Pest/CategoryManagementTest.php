<?php

use App\Models\User;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Category Management', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
    });

    it('allows authenticated users to view categories', function () {
        Category::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->get('/categories');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Categories/Index'));
    });

    it('allows users to create categories', function () {
        $categoryData = [
            'name' => 'Fleet Maintenance',
            'color' => '#EF4444',
            'description' => 'Maintenance related tasks',
        ];

        $response = $this->actingAs($this->user)->post('/categories', $categoryData);

        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', [
            'name' => 'Fleet Maintenance',
            'color' => '#EF4444',
            'description' => 'Maintenance related tasks',
        ]);
    });

    it('validates required fields when creating categories', function () {
        $response = $this->actingAs($this->user)->post('/categories', []);

        $response->assertSessionHasErrors(['name']);
    });

    it('validates category name uniqueness', function () {
        Category::factory()->create(['name' => 'Existing Category']);

        $response = $this->actingAs($this->user)->post('/categories', [
            'name' => 'Existing Category',
            'color' => '#EF4444',
        ]);

        $response->assertSessionHasErrors(['name']);
    });

    it('allows users to update categories', function () {
        $category = Category::factory()->create();

        $updateData = [
            'name' => 'Updated Category',
            'color' => '#10B981',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->user)->put("/categories/{$category->id}", $updateData);

        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
            'color' => '#10B981',
        ]);
    });

    it('allows users to delete categories', function () {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->delete("/categories/{$category->id}");

        $response->assertRedirect('/categories');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    });

    it('prevents category deletion when tasks are associated', function () {
        $category = Category::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($this->user)->delete("/categories/{$category->id}");

        $response->assertRedirect('/categories');
        $response->assertSessionHas('error', 'Cannot delete category with existing tasks.');
        
        // Category should still exist
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        
        // Task should still exist with category_id
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'category_id' => $category->id,
        ]);
    });

    it('validates color format', function () {
        $response = $this->actingAs($this->user)->post('/categories', [
            'name' => 'Test Category',
            'color' => 'invalid-color',
        ]);

        $response->assertSessionHasErrors(['color']);
    });

    it('accepts valid hex colors', function () {
        $validColors = ['#EF4444', '#F59E0B', '#10B981', '#3B82F6'];

        foreach ($validColors as $color) {
            $response = $this->actingAs($this->user)->post('/categories', [
                'name' => "Category {$color}",
                'color' => $color,
            ]);

            $response->assertRedirect('/categories');
            $this->assertDatabaseHas('categories', [
                'name' => "Category {$color}",
                'color' => $color,
            ]);
        }
    });
});
