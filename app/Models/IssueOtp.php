<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueOtp extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_report_id',
        'otp',
        'expires_at',
    ];

    protected $casts = [
        'otp' => 'string',
        'expires_at' => 'datetime',
    ];
}
