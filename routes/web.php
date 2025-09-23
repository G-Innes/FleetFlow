<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TaskExportController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    
    // Get real task statistics
    $totalTasks = $user->tasks()->count();
    $completedTasks = $user->tasks()->where('is_completed', true)->count();
    $dueSoon = $user->tasks()->dueSoon()->count();
    $overdue = $user->tasks()->where('due_date', '<', now())->where('is_completed', false)->count();
    
    // Get recent tasks
    $recentTasks = $user->tasks()
        ->with('category')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get()
        ->map(function ($task) {
            return [
                '∏id' => $task->id,
                'title' => $task->title,
                'category' => $task->category?->name ?? 'No Category',
                'due' => $task->due_date ? $task->due_date->diffForHumans() : 'No due date',
                'priority' => $task->priority_label,
                'completed' => $task->is_completed,
                'is_due_soon' => $task->is_due_soon
            ];
        });
    
    return Inertia::render('Dashboard', [
        'stats' => [
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'dueSoon' => $dueSoon,
            'overdue' => $overdue
        ],
        'recentTasks' => $recentTasks
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Task management routes
    Route::resource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    
    // Category management routes
    Route::resource('categories', CategoryController::class);
    
    // CSV import/export routes
    Route::get('/tasks/export/csv', [TaskExportController::class, 'export'])->name('tasks.export');
    Route::post('/tasks/import/csv', [TaskExportController::class, 'import'])->name('tasks.import');
});

require __DIR__.'/auth.php';
