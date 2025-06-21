<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Blog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'is_active', 
        'is_featured',
        'author_id',
        'views'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function categories()
    {
        return $this->belongsToMany(CategoryBlog::class, 'blog_category_blog', 'blog_id', 'category_blog_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }

    public function getExcerptAttribute()
    {
        $excerpt = strip_tags($this->content);
        return strlen($excerpt) > 200 ? substr($excerpt, 0, 200) . '...' : $excerpt;
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function incrementViews()
    {
        $this->increment('views');
    }
}
