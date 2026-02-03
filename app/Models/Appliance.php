<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appliance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'appliance_name',
        'product_details',
        'appliance_id',
        'brand_name',
        'model',
        'warranty_information',
        'manuals',
        'appliances_images',
    ];
    protected $casts = [
        'manuals' => 'array',
        'appliances_images' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function issueReports()
    {
        return $this->hasMany(IssueReport::class, 'appliance_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($appliance) {
            if (empty($appliance->appliance_id)) {
                $last = self::latest('id')->first();
                $number = $last ? ((int) filter_var($last->appliance_id, FILTER_SANITIZE_NUMBER_INT) + 1) : 1;
                $appliance->appliance_id = 'APL' . str_pad($number, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
