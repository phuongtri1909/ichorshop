<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class States extends Model
{
    protected $fillable = ['name', 'code', 'country_code'];

    /**
     * Get the country that owns the state.
     */
    public function country()
    {
        return $this->belongsTo(Countries::class, 'country_code', 'code');
    }

    /**
     * Get the cities for the state.
     */
    public function cities()
    {
        return $this->hasMany(Cities::class, 'state_id', 'id');
    }

}
