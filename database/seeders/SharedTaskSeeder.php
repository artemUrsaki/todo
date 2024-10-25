<?php

namespace Database\Seeders;

use App\Models\SharedTask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SharedTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all()->random(30);

        Task::all()->random(30)->each(function (Task $task) use ($users) {
            $task->sharedTasks()->attach(
                $users->random()->id
            );
        });
    }
}
