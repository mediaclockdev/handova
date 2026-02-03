<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationList extends Model
{
    use HasFactory;
     protected $table = 'notifications';

    protected $fillable = [
        'properties_id',
        'house_owner_id',
        'title',
        'body',
        'is_read'
    ];
}
