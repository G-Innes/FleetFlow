<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     * Prioritizing tasks by due date then priority - mirrors Fleet Fox's 
     * SLA-driven task assignment where urgent washes/relocations take precedence
     */
    public function index(Request $request)
    {
        $dueSoon = filter_var($request->input('due_soon', false), FILTER_VALIDATE_BOOLEAN);

        $tasks = Task::with('category')
            ->where('user_id', Auth::id())
            ->byCategory($request->category)
            ->when($request->status === 'completed', fn($q) => $q->where('is_completed', true))
            ->when($request->status === 'pending', fn($q) => $q->where('is_completed', false))
            ->when($dueSoon, fn($q) => $q->dueSoon())
            ->when($request->filled('priority'), function ($q) use ($request) {
                $priority = (int) $request->priority;
                if (in_array($priority, [1,2,3], true)) {
                    $q->where('priority', $priority);
                }
            })
            ->orderBy('due_date', 'asc')
            ->orderBy('priority', 'desc')
            ->paginate(10);
            
        $categories = Category::all();
        
        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'categories' => $categories,
            'filters' => $request->only(['category', 'status', 'priority'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        
        return Inertia::render('Tasks/Create', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'due_date' => 'nullable|date|after:now',
            'priority' => 'required|integer|between:1,3'
        ]);

        Task::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'due_date' => $request->due_date,
            'priority' => $request->priority,
        ]);

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        // Ensure user can only edit their own tasks
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $categories = Category::all();
        
        return Inertia::render('Tasks/Edit', [
            'task' => $task,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        // Ensure user can only update their own tasks
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'due_date' => 'nullable|date',
            'priority' => 'required|integer|between:1,3',
            'is_completed' => 'boolean'
        ]);

        $task->update($request->all());

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        // Ensure user can only delete their own tasks
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    /**
     * Toggle task completion status
     */
    public function toggle(Request $request, Task $task)
    {
        // Ensure user can only toggle their own tasks
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->update(['is_completed' => !$task->is_completed]);

        // For Inertia visits, return a redirect (not JSON)
        if ($request->header('X-Inertia')) {
            return redirect()->back(303);
        }

        // Fallback for plain API/XHR usage
        return response()->json([
            'success' => true,
            'is_completed' => $task->is_completed,
        ]);
    }
}
