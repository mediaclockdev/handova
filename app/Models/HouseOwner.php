<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HouseOwner extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'house_owner_id',
        'properties_id',
        'first_name',
        'last_name',
        'email_address',
        'phone_number',
        'address_of_property',
        'house_plan_name',
        'build_completion_date',
        'assigned_builder_site_manager',
        'number_of_bedrooms',
        'number_of_bathrooms',
        'parking',
        'handover_documents',
        'floor_plan_upload',
        'property_status',
        'tags',
        'internal_notes',
    ];

    protected $casts = [
        'handover_documents' => 'array',
        'floor_plan_upload' => 'array',
        'properties_id'     => 'array',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'properties_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reportedIssues()
    {
        return $this->hasMany(IssueReport::class, 'reported_by');
    }
}
