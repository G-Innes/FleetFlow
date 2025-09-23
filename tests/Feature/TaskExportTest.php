<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskExportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_export_tasks_as_csv()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Fleet Maintenance']);
        
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Test Task',
            'description' => 'Test Description',
            'due_date' => now()->addDays(3),
            'is_completed' => false,
            'priority' => 2
        ]);

        $response = $this->actingAs($user)->get('/tasks/export/csv');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv');
        $response->assertHeader('Content-Disposition', 'attachment; filename="tasks_export_' . now()->format('Y-m-d_H-i-s') . '.csv"');
        
        $csvContent = $response->getContent();
        $this->assertStringContainsString('Test Task', $csvContent);
        $this->assertStringContainsString('Fleet Maintenance', $csvContent);
        $this->assertStringContainsString('Medium', $csvContent);
    }

    /** @test */
    public function export_only_includes_user_own_tasks()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $user1Task = Task::factory()->create([
            'user_id' => $user1->id,
            'title' => 'User 1 Task'
        ]);
        
        $user2Task = Task::factory()->create([
            'user_id' => $user2->id,
            'title' => 'User 2 Task'
        ]);

        $response = $this->actingAs($user1)->get('/tasks/export/csv');

        $response->assertStatus(200);
        $csvContent = $response->getContent();
        
        $this->assertStringContainsString('User 1 Task', $csvContent);
        $this->assertStringNotContainsString('User 2 Task', $csvContent);
    }

    /** @test */
    public function export_includes_all_required_columns()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Test Category']);
        
        Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Test Task',
            'description' => 'Test Description',
            'due_date' => now()->addDays(1),
            'is_completed' => true,
            'priority' => 3
        ]);

        $response = $this->actingAs($user)->get('/tasks/export/csv');

        $response->assertStatus(200);
        $csvContent = $response->getContent();
        
        // Check for CSV headers
        $this->assertStringContainsString('Title', $csvContent);
        $this->assertStringContainsString('Description', $csvContent);
        $this->assertStringContainsString('Category', $csvContent);
        $this->assertStringContainsString('Due Date', $csvContent);
        $this->assertStringContainsString('Status', $csvContent);
        $this->assertStringContainsString('Priority', $csvContent);
        $this->assertStringContainsString('Created', $csvContent);
    }

    /** @test */
    public function authenticated_user_can_import_tasks_from_csv()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Fleet Maintenance']);

        $csvContent = "Title,Description,Category,Due Date,Status,Priority\n";
        $csvContent .= "Imported Task,Imported Description,Fleet Maintenance," . now()->addDays(2)->format('Y-m-d H:i') . ",Pending,High\n";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('tasks.csv', $csvContent);

        $response = $this->actingAs($user)->post('/tasks/import/csv', [
            'csv_file' => $file
        ]);

        $response->assertRedirect('/tasks');
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Imported Task',
            'description' => 'Imported Description',
            'category_id' => $category->id,
            'priority' => 3,
            'is_completed' => false
        ]);
    }

    /** @test */
    public function import_creates_category_if_not_exists()
    {
        $user = User::factory()->create();

        $csvContent = "Title,Description,Category,Due Date,Status,Priority\n";
        $csvContent .= "New Task,New Description,New Category," . now()->addDays(2)->format('Y-m-d H:i') . ",Pending,Medium\n";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('tasks.csv', $csvContent);

        $response = $this->actingAs($user)->post('/tasks/import/csv', [
            'csv_file' => $file
        ]);

        $response->assertRedirect('/tasks');
        
        // Check that task was created
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'New Task'
        ]);
        
        // Check that category was created
        $this->assertDatabaseHas('categories', [
            'name' => 'New Category'
        ]);
    }

    /** @test */
    public function import_handles_missing_category_gracefully()
    {
        $user = User::factory()->create();

        $csvContent = "Title,Description,Category,Due Date,Status,Priority\n";
        $csvContent .= "Task Without Category,Description,No Category," . now()->addDays(2)->format('Y-m-d H:i') . ",Pending,Low\n";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('tasks.csv', $csvContent);

        $response = $this->actingAs($user)->post('/tasks/import/csv', [
            'csv_file' => $file
        ]);

        $response->assertRedirect('/tasks');
        
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Task Without Category',
            'category_id' => null
        ]);
    }

    /** @test */
    public function import_validates_file_type()
    {
        $user = User::factory()->create();
        $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->post('/tasks/import/csv', [
            'csv_file' => $file
        ]);

        $response->assertSessionHasErrors(['csv_file']);
    }

    /** @test */
    public function import_handles_invalid_csv_data()
    {
        $user = User::factory()->create();

        $csvContent = "Invalid,CSV,Format\n";
        $csvContent .= "Missing,Required,Columns\n";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('tasks.csv', $csvContent);

        $response = $this->actingAs($user)->post('/tasks/import/csv', [
            'csv_file' => $file
        ]);

        $response->assertRedirect('/tasks');
        // Should still redirect but with error handling
    }

    /** @test */
    public function export_requires_authentication()
    {
        $response = $this->get('/tasks/export/csv');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function import_requires_authentication()
    {
        $file = \Illuminate\Http\UploadedFile::fake()->create('tasks.csv', 100, 'text/csv');
        
        $response = $this->post('/tasks/import/csv', [
            'csv_file' => $file
        ]);
        
        $response->assertRedirect('/login');
    }

    /** @test */
    public function priority_mapping_works_correctly()
    {
        $user = User::factory()->create();

        $csvContent = "Title,Description,Category,Due Date,Status,Priority\n";
        $csvContent .= "High Priority Task,Description,General," . now()->addDays(2)->format('Y-m-d H:i') . ",Pending,High\n";
        $csvContent .= "Medium Priority Task,Description,General," . now()->addDays(2)->format('Y-m-d H:i') . ",Pending,Medium\n";
        $csvContent .= "Low Priority Task,Description,General," . now()->addDays(2)->format('Y-m-d H:i') . ",Pending,Low\n";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('tasks.csv', $csvContent);

        $response = $this->actingAs($user)->post('/tasks/import/csv', [
            'csv_file' => $file
        ]);

        $response->assertRedirect('/tasks');
        
        $this->assertDatabaseHas('tasks', [
            'title' => 'High Priority Task',
            'priority' => 3
        ]);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Medium Priority Task',
            'priority' => 2
        ]);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Low Priority Task',
            'priority' => 1
        ]);
    }
}
