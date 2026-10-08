<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskDependency extends Model
{
    use HasFactory;

    protected $fillable = [
        'predecessor_task_id',
        'successor_task_id',
        'dependency_type',
    ];

    public function predecessorTask()
    {
        return $this->belongsTo(Task::class, 'predecessor_task_id');
    }

    public function successorTask()
    {
        return $this->belongsTo(Task::class, 'successor_task_id');
    }
} 