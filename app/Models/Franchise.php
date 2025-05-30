<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Franchise extends Model
{
    protected $fillable = [
        'name',
        'name_package',
        'slug',
        'description',
        'sort_order',
        'code',
        'details',
    ];
}
