<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'order_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'provinces_code',
        'provinces_name',
        'districts_code',
        'districts_name',
        'wards_code',
        'wards_name',
        'note',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'provinces_code', 'code');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'districts_code', 'code');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'wards_code', 'code');
    }
}
