<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseContact extends Model
{
    protected $fillable = [
        'franchise_code',
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
        'status',
    ];

    public function franchise()
    {
        return $this->belongsTo(Franchise::class, 'franchise_code', 'code');
    }
}
