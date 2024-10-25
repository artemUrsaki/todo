<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'content',
        'user_id',
        'is_completed'
    ];

    public function users() {
        return $this->belongsTo(User::class);
    }

    public function sharedTasks() {
        return $this->belongsToMany(User::class, 'shared_tasks', 'task_id', 'shared_with_user_id');
    }

    public function categories() {
        return $this->belongsToMany(Category::class, 'task_categories');
    }
}
