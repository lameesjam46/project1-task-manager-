<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'note',
        'attachment',
    ];

    /**
     * Get the task that owns the note.
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user who created the note.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAttachmentUrlAttribute()
    {
        return $this->attachment ? asset('storage/' . $this->attachment) : null;
    }
} 