<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = ['street', 'city_id', 'user_id'];

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
