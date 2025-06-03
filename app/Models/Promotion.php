<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = ['name', 'type','value', 'start_date', 'end_date', 'status'];

    const TYPE_PERCENTAGE = 'percentage';
    const TYPE_FIXED = 'fixed';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
}
