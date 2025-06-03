<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{

    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'description_long', 'description_short', 'brand_id','avatar','avatar_medium','status'];


    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';


    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }


}
