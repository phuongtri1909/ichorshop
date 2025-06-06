<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'street', 
        'city_id', 
        'user_id', 
        'postal_code', 
        'label', 
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean'
    ];

    /**
     * Get the user that owns the address.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    /**
     * Get the city that owns the address.
     */
    public function city()
    {
        return $this->belongsTo(Cities::class, 'city_id');
    }

}
