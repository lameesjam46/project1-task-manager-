<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyJoinRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'status',
        'message',
        'response_note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
} 