<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Learning',
            'Personal',
            'Working',
            'Projects',
            'Education',
        ];

        foreach ($categories as $category) {
            Category::query()->firstOrCreate(['name' => $category]);
        }
    }
}
