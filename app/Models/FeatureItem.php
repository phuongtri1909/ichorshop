<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureItem extends Model
{
    protected $fillable = ['feature_section_id', 'icon', 'title', 'description', 'sort_order'];

    public function section()
    {
        return $this->belongsTo(FeatureSection::class);
    }
}
