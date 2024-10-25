<?php

namespace Database\Seeders;

use App\Models\SharedTask;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Task;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(25)
            ->hasTasks(3)
            ->create();

        User::factory(12)
            ->hasTasks(5)
            ->create();

        User::factory(3)
            ->hasTasks(15)
            ->create();
    }
}
