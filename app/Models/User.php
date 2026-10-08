<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'role',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // 'email_verified_at' => 'datetime',
            // 'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    public function profile(){
        return $this->hasOne(Profile::class);
    }
    public function tasks(){
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function favoriteTasks(){
        return $this->belongsToMany(Task::class,'favorites');
    }

//    public function company(){
//        return $this->belongsTo(Company::class);
//    }

    public function projects(){
        // Projects where this user is the project manager
        return $this->hasMany(Project::class, 'project_manager_id');
    }

    /**
     * Alias: projects managed by the user.
     */
    public function managedProjects(){
        return $this->hasMany(Project::class, 'project_manager_id');
    }

    /**
     * Get the tasks assigned to this user.
     */
    public function assignedTasks()
    {
        return $this->belongsToMany(Task::class, 'tasks')
            ->withTimestamps();
    }

    /**
     * Get the companies that the user belongs to.
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'employee_company')
            ->withTimestamps();
    }

    /**
     * Get all chat messages sent by the user.
     */
    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }
}
