<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = [
        'name',
        'code',
        'province_code',
        'division_type',
        'codename',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class, 'provinces_code', 'code');
    }

    public function wards()
    {
        return $this->hasMany(Ward::class, 'district_code', 'code');
    }


}
