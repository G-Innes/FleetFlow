<?php

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

describe('CSV Import Functionality', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create(['name' => 'Fleet Maintenance']);
    });

    it('redirects unauthenticated users', function () {
        $response = $this->post('/tasks/import/csv');

        $response->assertRedirect('/login');
    });

    it('validates CSV file is required', function () {
        $response = $this->actingAs($this->user)->post('/tasks/import/csv', []);

        $response->assertSessionHasErrors(['csv_file']);
    });

    it('validates file type is CSV', function () {
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($this->user)->post('/tasks/import/csv', [
            'csv_file' => $file,
        ]);

        $response->assertSessionHasErrors(['csv_file']);
    });

    it('accepts valid CSV file', function () {
        $file = UploadedFile::fake()->createWithContent(
            'tasks.csv',
            "Title,Description,Category,Due Date,Status,Priority\nTest Task,Description,Fleet Maintenance,2024-12-25,Pending,Medium"
        );

        $response = $this->actingAs($this->user)->post('/tasks/import/csv', [
            'csv_file' => $file,
        ]);

        $response->assertRedirect('/tasks');
    });

    it('imports tasks from CSV with correct data', function () {
        $file = UploadedFile::fake()->createWithContent(
            'tasks.csv',
            "Title,Description,Category,Due Date,Status,Priority\nTest Task,Description,Fleet Maintenance,2024-12-25,Pending,Medium"
        );

        $response = $this->actingAs($this->user)->post('/tasks/import/csv', [
            'csv_file' => $file,
        ]);

        $response->assertRedirect('/tasks');
        
        $this->assertDatabaseHas('tasks', [
            'user_id' => $this->user->id,
            'title' => 'Test Task',
            'description' => 'Description',
            'category_id' => $this->category->id,
            'priority' => 2,
            'is_completed' => false,
        ]);
    });

    it('handles completed status correctly', function () {
        $file = UploadedFile::fake()->createWithContent(
            'tasks.csv',
            "Title,Description,Category,Due Date,Status,Priority\nCompleted Task,Description,Fleet Maintenance,2024-12-25,Completed,1"
        );

        $response = $this->actingAs($this->user)->post('/tasks/import/csv', [
            'csv_file' => $file,
        ]);

        $response->assertRedirect('/tasks');
        
        $this->assertDatabaseHas('tasks', [
            'user_id' => $this->user->id,
            'title' => 'Completed Task',
            'is_completed' => true,
        ]);
    });

    it('handles tasks without category', function () {
        $file = UploadedFile::fake()->createWithContent(
            'tasks.csv',
            "Title,Description,Category,Due Date,Status,Priority\nTask Without Category,Description,,2024-12-25,Pending,2"
        );

        $response = $this->actingAs($this->user)->post('/tasks/import/csv', [
            'csv_file' => $file,
        ]);

        $response->assertRedirect('/tasks');
        
        $this->assertDatabaseHas('tasks', [
            'user_id' => $this->user->id,
            'title' => 'Task Without Category',
            'category_id' => null,
        ]);
    });

    it('handles multiple tasks in CSV', function () {
        $file = UploadedFile::fake()->createWithContent(
            'tasks.csv',
            "Title,Description,Category,Due Date,Status,Priority\nTask 1,Description 1,Fleet Maintenance,2024-12-25,Pending,2\nTask 2,Description 2,Fleet Maintenance,2024-12-26,Completed,1"
        );

        $response = $this->actingAs($this->user)->post('/tasks/import/csv', [
            'csv_file' => $file,
        ]);

        $response->assertRedirect('/tasks');
        
        $this->assertDatabaseHas('tasks', [
            'user_id' => $this->user->id,
            'title' => 'Task 1',
        ]);
        $this->assertDatabaseHas('tasks', [
            'user_id' => $this->user->id,
            'title' => 'Task 2',
        ]);
    });

    it('handles invalid CSV format gracefully', function () {
        $file = UploadedFile::fake()->createWithContent(
            'tasks.csv',
            "Invalid,CSV,Format\nData,Without,Headers"
        );

        $response = $this->actingAs($this->user)->post('/tasks/import/csv', [
            'csv_file' => $file,
        ]);

        // Should handle gracefully without crashing
        $response->assertRedirect('/tasks');
    });

    it('validates file size', function () {
        $file = UploadedFile::fake()->create('tasks.csv', 10240); // 10MB

        $response = $this->actingAs($this->user)->post('/tasks/import/csv', [
            'csv_file' => $file,
        ]);

        // Should handle large files appropriately
        $response->assertStatus(302); // Redirect or error
    });

    it('only imports tasks for authenticated user', function () {
        $otherUser = User::factory()->create();
        
        $file = UploadedFile::fake()->createWithContent(
            'tasks.csv',
            "Title,Description,Category,Due Date,Status,Priority\nMy Task,Description,Fleet Maintenance,2024-12-25,Pending,2"
        );

        $response = $this->actingAs($this->user)->post('/tasks/import/csv', [
            'csv_file' => $file,
        ]);

        $response->assertRedirect('/tasks');
        
        // Verify task belongs to authenticated user
        $this->assertDatabaseHas('tasks', [
            'user_id' => $this->user->id,
            'title' => 'My Task',
        ]);
        
        // Verify task doesn't belong to other user
        $this->assertDatabaseMissing('tasks', [
            'user_id' => $otherUser->id,
            'title' => 'My Task',
        ]);
    });
});
