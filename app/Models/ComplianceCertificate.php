<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplianceCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'certification_title',
        'compliance_type',
        'certificate_number',
        'issuing_authority',
        'date_of_issue',
        'expiry_date',
        'property_area',
        'attachments',
        'notes',
    ];

    protected $casts = [
        'attachments' => 'array',
        'date_of_issue' => 'date',
        'expiry_date' => 'date',
    ];

    public function property()
    {
        return $this->belongsTo(\App\Models\Property::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
