<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;
    protected $fillable = [
        'plan_name',
        'plan_type',
        'plan_price',
        'plan_duration',
        'plan_duration_unit',
        'plan_allowed_listing',
        'plan_video_upload_limit',
        'plan_additional_feature',
        'plan_description',
        'plan_featured_properties',
        'plan_photo_upload_limit',
    ];

    protected $casts = [
        'plan_additional_feature' => 'array',
    ];
}
