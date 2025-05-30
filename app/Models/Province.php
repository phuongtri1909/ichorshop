<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = [
        'name',
        'code',
        'division_type',
        'codename',
        'phone_code',
    ];

    public function districts()
    {
        return $this->hasMany(District::class, 'province_code', 'code');
    }

    
}
