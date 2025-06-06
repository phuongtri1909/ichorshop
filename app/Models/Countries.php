<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{
    protected $fillable = ['name', 'code'];

        public function states()
    {
        return $this->hasMany(States::class, 'country_code', 'code');
    }
}
