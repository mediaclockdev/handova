<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HousePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_name',
        'storey',
        'floor_details',
        'user_id',
        'pricing',
        'house_area',
        'suburbs',
        'display_location'
    ];

    protected $casts = [
        'floor_details' => 'array',
    ];



    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function appliance()
    {
        return $this->belongsTo(Appliance::class);
    }

    public function appliances()
    {
        return $this->belongsToMany(Appliance::class, 'house_plan_appliances', 'house_plan_id', 'appliance_id');
    }

    // public function getHouseAreaAttribute()
    // {
    //     if (!$this->floor_details) return '-';

    //     $area = 0;

    //     foreach ($this->floor_details as $floor) {
    //         $area += ($floor['bedrooms'] ?? 0) * 120;
    //         $area += ($floor['bathrooms'] ?? 0) * 40;
    //     }

    //     return $area . ' sq.ft';
    // }

    // public function getPricingAttribute()
    // {
    //     if (!$this->floor_details) return '-';

    //     $totalBedrooms = 0;

    //     foreach ($this->floor_details as $floor) {
    //         $totalBedrooms += $floor['bedrooms'] ?? 0;
    //     }

    //     return '₹ ' . number_format($totalBedrooms * 500000);
    // }

    public function getDisplayLocationAttribute()
    {
        return match ($this->storey) {
            'ground_floor' => 'Ground Floor',
            'first_floor'  => 'G + 1',
            'second_floor' => 'G + 2',
            default        => '-',
        };
    }
}
