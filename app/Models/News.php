<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'slug',
        'thumbnail',
        'avatar',
        'content',
        'is_active',
        'is_featured'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        // Tạo slug tự động khi tạo mới
        static::creating(function ($news) {
            if (empty($news->slug)) {
                $news->slug = Str::slug($news->title);
            }
        });
        
        // Cập nhật slug khi cập nhật title
        static::updating(function ($news) {
            if ($news->isDirty('title') && !$news->isDirty('slug')) {
                $news->slug = Str::slug($news->title);
            }
        });
    }
}