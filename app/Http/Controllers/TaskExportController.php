<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TaskExportController extends Controller
{
    /**
     * Export tasks to CSV
     */
    public function export(Request $request)
    {
        $tasks = Task::with('category')
            ->where('user_id', Auth::id())
            ->get()
            ->map(function ($task) {
                return [
                    'Title' => $task->title,
                    'Description' => $task->description,
                    'Category' => $task->category?->name ?? 'No Category',
                    'Due Date' => $task->due_date?->format('Y-m-d H:i') ?? 'No Due Date',
                    'Status' => $task->is_completed ? 'Completed' : 'Pending',
                    'Priority' => $task->priority_label,
                    'Created' => $task->created_at->format('Y-m-d H:i'),
                ];
            });

        $filename = 'tasks_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($tasks) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            if ($tasks->isNotEmpty()) {
                fputcsv($file, array_keys($tasks->first()));
            }
            
            // Add data rows
            foreach ($tasks as $task) {
                fputcsv($file, $task);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import tasks from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('csv_file');
        $csvData = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($csvData);

        $imported = 0;
        $errors = [];

        foreach ($csvData as $row) {
            try {
                $data = array_combine($header, $row);
                
                // Map CSV columns to our task fields
                $taskData = [
                    'user_id' => Auth::id(),
                    'title' => $data['Title'] ?? '',
                    'description' => $data['Description'] ?? null,
                    'due_date' => !empty($data['Due Date']) && $data['Due Date'] !== 'No Due Date' 
                        ? \Carbon\Carbon::parse($data['Due Date']) 
                        : null,
                    'is_completed' => ($data['Status'] ?? '') === 'Completed',
                    'priority' => $this->mapPriority($data['Priority'] ?? 'Low'),
                ];

                // Find category by name
                if (!empty($data['Category']) && $data['Category'] !== 'No Category') {
                    $category = \App\Models\Category::where('name', $data['Category'])->first();
                    if ($category) {
                        $taskData['category_id'] = $category->id;
                    }
                }

                Task::create($taskData);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($imported + count($errors) + 1) . ": " . $e->getMessage();
            }
        }

        $message = "Imported {$imported} tasks successfully.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', $errors);
        }

        return redirect()->route('tasks.index')
            ->with('success', $message);
    }

    /**
     * Map priority string to integer
     */
    private function mapPriority(string $priority): int
    {
        return match(strtolower($priority)) {
            'high' => 3,
            'medium' => 2,
            'low' => 1,
            default => 1
        };
    }
}
