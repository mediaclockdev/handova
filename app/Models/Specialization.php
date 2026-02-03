<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    use HasFactory;

    protected $fillable = [
        'specialization',
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'service_specialisation');
    }
}
