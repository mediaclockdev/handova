<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueReport extends Model
{
    use HasFactory;
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_DECLINED = 'declined';
    protected $fillable = [
        'issue_number',
        'properties_id',
        'appliance_id',
        'issue_title',
        'issue_category',
        'issue_location',
        'customer_contact',
        'issue_details',
        'reported_by',
        'reported_date',
        'assigned_to_service_provider',
        'service_provider',
        'issue_status',
        'issue_urgency_level',
        'image',
        'status'
    ];

    protected $casts = [
        'reported_date' => 'date',
        'image' => 'array',
    ];

    protected $attributes = [
        'status' => 'declined',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'properties_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignedServiceProvider()
    {
        return $this->belongsTo(User::class, 'service_provider');
    }

    public function appliance()
    {
        return $this->belongsTo(Appliance::class, 'appliance_id');
    }
}
