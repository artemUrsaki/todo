<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'category'
    ];

    protected $primaryKey = 'category_id';

    public $timestamps = false;

    public function tasks() {
        return $this->belongsToMany(Task::class, 'task_categories', 'category_id', 'task_id');
    }
}
