<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureSection extends Model
{
    protected $fillable = ['title', 'description', 'button_text', 'button_link'];

    public function items()
    {
        return $this->hasMany(FeatureItem::class)->orderBy('sort_order');
    }
}
