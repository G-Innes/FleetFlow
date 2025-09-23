<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'color', 'description'];
    
    /**
     * Get all tasks for this category
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
