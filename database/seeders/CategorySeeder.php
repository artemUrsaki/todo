<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory(10)->create();

        $categories = Category::all();

        Task::all()->random(40)->each(function (Task $task) use ($categories) {
            $task->categories()->attach($categories->random()->category_id);
        });
    }
}
