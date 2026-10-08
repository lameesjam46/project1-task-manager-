<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    use HasFactory;
    protected $table = 'tasks';
    protected $guarded=[];
    // protected $fillable = [
    //     'is_critical',
    //     'is_overdue',
    // ];
    protected $casts = [
        'is_critical' => 'boolean',
        'is_overdue' => 'boolean',
        'due_date' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // public function user(){
    //     return $this->belongsTo(User::class);
    // }
    public function categories(){
        return $this->belongsToMany(Category::class,'category_task');
    }

    public function favoriteByUser(){
        return $this->belongsToMany(User::class,'favorites');
    }

    public function project(){
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the users assigned to this task.
     */


    /**
     * Get the notes for the task.
     */
    public function notes()
    {
        return $this->hasMany(TaskNote::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function parentTask()
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    /**
     * المهام التي تسبق هذه المهمة (dependencies حيث هذه المهمة هي اللاحقة)
     */
    public function predecessorDependencies()
    {
        return $this->hasMany(TaskDependency::class, 'successor_task_id');
    }

    /**
     * المهام التي تتبع هذه المهمة (dependencies حيث هذه المهمة هي السابقة)
     */
    public function successorDependencies()
    {
        return $this->hasMany(TaskDependency::class, 'predecessor_task_id');
    }

    /**
     * جميع اعتماديات المهمة (للاستخدام العام)
     */
    public function dependencies()
    {
        return $this->predecessorDependencies();
    }

    /**
     * المهام السابقة التي تعتمد عليها هذه المهمة
     */
    public function predecessorTasks()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'successor_task_id', 'predecessor_task_id')
                    ->withPivot('dependency_type')
                    ->withTimestamps();
    }

    /**
     * المهام اللاحقة التي تعتمد على هذه المهمة
     */
    public function successorTasks()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'predecessor_task_id', 'successor_task_id')
                    ->withPivot('dependency_type')
                    ->withTimestamps();
    }
}
