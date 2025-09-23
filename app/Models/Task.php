<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Task extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'title', 'description', 
        'due_date', 'is_completed', 'priority'
    ];
    
    protected $casts = [
        'due_date' => 'datetime',
        'is_completed' => 'boolean'
    ];
    
    /**
     * Get the user that owns the task
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the category that owns the task
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    /**
     * Scope to filter tasks due soon (within 3 days)
     * Similar to how Fleet Fox would flag service requests approaching SLA deadlines
     */
    public function scopeDueSoon(Builder $query): Builder
    {
        return $query->where('due_date', '<=', now()->addDays(3))
                    ->where('due_date', '>=', now())
                    ->where('is_completed', false);
    }
    
    /**
     * Scope to filter tasks by category
     */
    public function scopeByCategory(Builder $query, ?int $categoryId): Builder
    {
        return $categoryId ? $query->where('category_id', $categoryId) : $query;
    }
    
    /**
     * Check if task is due soon
     * Mirrors Fleet Fox's SLA-driven task assignment where urgent tasks take precedence
     */
    public function getIsDueSoonAttribute(): bool
    {
        if (!$this->due_date || $this->is_completed) {
            return false;
        }
        
        return $this->due_date->isPast() || 
               $this->due_date->diffInDays(now()) <= 3;
    }
    
    /**
     * Get priority label for display
     */
    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            1 => 'Low',
            2 => 'Medium', 
            3 => 'High',
            default => 'Low'
        };
    }
}
