<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shared_tasks', function (Blueprint $table) {
            $table->id('shared_tasks_id');
            $table->foreignId('task_id')->constrained();
            $table->foreignId('shared_with_user_id')->constrained(
                'users', 'id'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_tasks');
    }
};
