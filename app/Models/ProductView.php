<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ProductView extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'ip_address',
        'viewed_at',
        'view_count'
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    // Mối quan hệ với Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Mối quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Lọc theo ID người dùng
     */
    public function scopeByUser(Builder $query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    
    /**
     * Lọc theo địa chỉ IP (cho người dùng chưa đăng nhập)
     */
    public function scopeByIp(Builder $query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }
    
    /**
     * Lọc theo khoảng thời gian
     */
    public function scopeWithinPeriod(Builder $query, $startDate, $endDate = null)
    {
        $query->where('viewed_at', '>=', $startDate);
        
        if ($endDate) {
            $query->where('viewed_at', '<=', $endDate);
        }
        
        return $query;
    }
    
    /**
     * Lọc theo ngày hôm nay
     */
    public function scopeToday(Builder $query)
    {
        return $query->whereDate('viewed_at', now()->toDateString());
    }
}