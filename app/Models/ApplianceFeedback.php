<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplianceFeedback extends Model
{
    use HasFactory;
    protected $fillable = [
        'property_id',
        'appliance_id',
        'message',
        'image',
        'video',
    ];

    public function appliance()
    {
        return $this->belongsTo(Appliance::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
