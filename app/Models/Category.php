<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'color', 'description'];
    
    protected $casts = [
        'color' => 'string',
    ];
    
    /**
     * Get all tasks for this category
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
    
    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($category) {
            // Validate required fields
            if (empty($category->name)) {
                return false;
            }
            
            // Validate color format if provided
            if (!empty($category->color) && !preg_match('/^#[0-9A-Fa-f]{6}$/', $category->color)) {
                return false;
            }
            
            return true;
        });
    }
}
