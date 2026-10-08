<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id'
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the employees that belong to the company.
     */
    public function employees()
    {
        return $this->belongsToMany(User::class, 'employee_company')
            ->withTimestamps();
    }

    public function joinRequests()
    {
        return $this->hasMany(\App\Models\CompanyJoinRequest::class);
    }
}
