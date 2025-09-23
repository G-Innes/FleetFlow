<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Fleet Maintenance', 'color' => '#EF4444', 'description' => 'Vehicle maintenance and repairs'],
            ['name' => 'Vehicle Inspections', 'color' => '#F59E0B', 'description' => 'Regular safety and compliance inspections'],
            ['name' => 'Driver Tasks', 'color' => '#10B981', 'description' => 'Driver assignments and responsibilities'],
            ['name' => 'General', 'color' => '#3B82F6', 'description' => 'General administrative tasks'],
        ];
        
        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
