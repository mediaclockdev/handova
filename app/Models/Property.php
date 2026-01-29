<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'property_title',
        'property_type',
        'address',
        'house_plan_name',
        'build_completion_date',
        'assigned_builder_site_manager',
        'number_of_bedrooms',
        'number_of_bathrooms',
        'parking',
        'swimming_pool',
        'floor_plan_upload',
        'property_status',
        'appliance_id',
        'tags',
        'internal_notes',
        'compliance_certificate',
        'house_plan_id',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'floor_plan_upload' => 'array',
        'tags' => 'array',
        'swimming_pool' => 'boolean',
        'build_completion_date' => 'date',
        'appliance_id' => 'array'

    ];

    public function floorPlans()
    {
        return $this->hasMany(FloorPlan::class);
    }

    public function houseOwner()
    {
        return $this->hasOne(HouseOwner::class, 'properties_id');
    }

    public function appliance()
    {
        return $this->belongsTo(Appliance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function housePlan()
    {
        return $this->belongsTo(HousePlan::class);
    }

    public function feedback()
    {
        return $this->hasMany(ApplianceFeedback::class, 'property_id');
    }

    public function issueReports()
    {
        return $this->hasMany(IssueReport::class, 'property_id');
    }
}
