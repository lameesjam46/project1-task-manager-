<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'company_id',
        'project_manager_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company that owns the project.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the team members of the project.
     */
    public function teamMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withTimestamps();
    }

    /**
     * Get the manager of the project.
     */
    public function projectManager()
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    /**
     * Alias for projectManager relationship.
     */
    public function manager()
    {
        return $this->projectManager();
    }

    /**
     * Scope a query to only include projects with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include projects for a specific company.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Get all chat messages for the project.
     */
    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Get all tasks for the project.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Update project status based on tasks status
     */
    public function updateStatusBasedOnTasks()
    {
        $tasks = $this->tasks;
        
        if ($tasks->isEmpty()) {
            // إذا لم تكن هناك مهام، اجعل المشروع معلق
            if ($this->status !== 'Pending') {
                $this->status = 'Pending';
                $this->save();
            }
            return;
        }

        $allCompleted = $tasks->every(fn($task) => $task->status === 'Completed');
        $anyInProgress = $tasks->contains(fn($task) => $task->status === 'In Progress');

        if ($allCompleted && $this->status !== 'Finished') {
            $this->status = 'Finished';
            $this->save();
        } elseif ($anyInProgress && $this->status !== 'Abuilding') {
            $this->status = 'Abuilding';
            $this->save();
        } elseif (!$anyInProgress && !$allCompleted && $this->status !== 'Pending') {
            $this->status = 'Pending';
            $this->save();
        }
    }
} 