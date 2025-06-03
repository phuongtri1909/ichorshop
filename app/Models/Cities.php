<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    protected $fillable = ['name', 'state_id'];

    /**
     * Get the state that owns the city.
     */
    public function state()
    {
        return $this->belongsTo(States::class, 'state_id');
    }
}
