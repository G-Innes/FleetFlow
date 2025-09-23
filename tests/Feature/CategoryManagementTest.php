<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_create_category()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/categories', [
            'name' => 'Fleet Maintenance',
            'color' => '#EF4444',
            'description' => 'Vehicle maintenance and repairs'
        ]);

        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', [
            'name' => 'Fleet Maintenance',
            'color' => '#EF4444',
            'description' => 'Vehicle maintenance and repairs'
        ]);
    }

    /** @test */
    public function authenticated_user_can_view_categories()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Test Category']);

        $response = $this->actingAs($user)->get('/categories');

        $response->assertStatus(200);
        $response->assertSee('Test Category');
    }

    /** @test */
    public function authenticated_user_can_update_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Original Name']);

        $response = $this->actingAs($user)->patch("/categories/{$category->id}", [
            'name' => 'Updated Name',
            'color' => '#10B981',
            'description' => 'Updated description'
        ]);

        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
            'color' => '#10B981'
        ]);
    }

    /** @test */
    public function authenticated_user_can_delete_category_without_tasks()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->delete("/categories/{$category->id}");

        $response->assertRedirect('/categories');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /** @test */
    public function user_cannot_delete_category_with_tasks()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $task = Task::factory()->create(['category_id' => $category->id, 'user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/categories/{$category->id}");

        $response->assertRedirect('/categories');
        $response->assertSessionHas('error', 'Cannot delete category with existing tasks.');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    /** @test */
    public function category_creation_requires_authentication()
    {
        $response = $this->post('/categories', [
            'name' => 'Unauthorized Category',
            'color' => '#3B82F6'
        ]);

        $response->assertRedirect('/login');
    }

    /** @test */
    public function category_validation_works_correctly()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/categories', [
            'name' => '', // Empty name should fail
            'color' => 'invalid-color' // Invalid color format should fail
        ]);

        $response->assertSessionHasErrors(['name', 'color']);
    }

    /** @test */
    public function category_name_must_be_unique()
    {
        $user = User::factory()->create();
        Category::factory()->create(['name' => 'Existing Category']);

        $response = $this->actingAs($user)->post('/categories', [
            'name' => 'Existing Category',
            'color' => '#3B82F6'
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function category_color_must_be_valid_hex()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/categories', [
            'name' => 'Test Category',
            'color' => 'not-a-hex-color'
        ]);

        $response->assertSessionHasErrors(['color']);
    }

    /** @test */
    public function category_has_tasks_relationship()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $task = Task::factory()->create(['category_id' => $category->id, 'user_id' => $user->id]);

        $this->assertTrue($category->tasks->contains($task));
        $this->assertEquals(1, $category->tasks->count());
    }

    /** @test */
    public function category_can_have_multiple_tasks()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        
        Task::factory()->create(['category_id' => $category->id, 'user_id' => $user->id]);
        Task::factory()->create(['category_id' => $category->id, 'user_id' => $user->id]);

        $this->assertEquals(2, $category->tasks->count());
    }
}
