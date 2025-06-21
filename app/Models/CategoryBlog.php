<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryBlog extends Model
{
    
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function blogs()
    {
        return $this->belongsToMany(Blog::class, 'blog_category_blog', 'category_blog_id', 'blog_id');
    }
    
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
