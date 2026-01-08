<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class PageContent extends Model
{
    use HasFactory;

    protected $table = 'pagecontents';

    protected $fillable = [
        'title',
        'description',
        'type',
    ];
}
