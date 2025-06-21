<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{

    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'description_long', 'description_short', 'brand_id', 'avatar', 'avatar_medium', 'status'];


    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';


    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_products');
    }

    public function dressStyles()
    {
        return $this->belongsToMany(DressStyle::class, 'dress_style_products');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return Storage::url($this->avatar);
        }
        return asset('assets/images/no-image.png');
    }

    public function getAvatarMediumUrlAttribute()
    {
        if ($this->avatar_medium) {
            return Storage::url($this->avatar_medium);
        }
        return asset('assets/images/no-image.png');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($product) {
            // Delete images when product is deleted
            if ($product->avatar) {
                Storage::disk('public')->delete($product->avatar);
                Storage::disk('public')->delete($product->avatar_medium);
            }

            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                Storage::disk('public')->delete($image->image_path_medium);
            }
        });
    }

    // Lấy biến thể có giá thấp nhất
    public function getCheapestVariant()
    {
        return $this->variants()
            ->where('status', ProductVariant::STATUS_ACTIVE)
            ->orderBy('price', 'asc')
            ->first();
    }

    // Lấy giá thấp nhất
    public function getMinPrice()
    {
        $cheapestVariant = $this->getCheapestVariant();
        return $cheapestVariant ? $cheapestVariant->price : 0;
    }

    // Lấy giá đã giảm thấp nhất (nếu có khuyến mãi)
    public function getMinDiscountedPrice()
    {
        $cheapestVariant = $this->getCheapestVariant();
        return $cheapestVariant ? $cheapestVariant->getDiscountedPrice() : 0;
    }

    // Kiểm tra xem có khuyến mãi không
    public function hasDiscount()
    {
        $cheapestVariant = $this->getCheapestVariant();

        if (!$cheapestVariant) return false;

        return $cheapestVariant->getDiscountedPrice() < $cheapestVariant->price;
    }

    // Tính phần trăm giảm giá
    public function getDiscountPercentage()
    {
        $cheapestVariant = $this->getCheapestVariant();
        if (!$cheapestVariant || !$this->hasDiscount()) return 0;

        $original = $cheapestVariant->price;
        $discounted = $cheapestVariant->getDiscountedPrice();

        return round((($original - $discounted) / $original) * 100);
    }

    /**
     * Mối quan hệ với lượt xem sản phẩm
     */
    public function productViews()
    {
        return $this->hasMany(ProductView::class);
    }

    /**
     * Lấy tổng số lượt xem của sản phẩm
     */
    public function getTotalViewsAttribute()
    {
        return $this->productViews()->sum('view_count');
    }

    /**
     * Lấy số lượt xem trong ngày hôm nay
     */
    public function getTodayViewsAttribute()
    {
        return $this->productViews()->today()->sum('view_count');
    }

    /**
     * Lấy số lượt xem của người dùng cụ thể
     */
    public function getViewsByUserAttribute($userId)
    {
        return $this->productViews()->where('user_id', $userId)->sum('view_count');
    }

    /**
     * Scope sắp xếp sản phẩm theo lượt xem
     */
    public function scopeMostViewed($query, $limit = null)
    {
        $query->withCount(['productViews as views_count' => function ($query) {
            $query->select(\DB::raw('SUM(view_count)'));
        }])->orderByDesc('views_count');

        if ($limit) {
            $query->limit($limit);
        }

        return $query;
    }

    /**
     * Ghi lại lượt xem sản phẩm (1 lần/ngày cho mỗi người dùng)
     */
    public function recordView($userId = null, $ipAddress = null)
    {
        $today = now()->startOfDay();
        $ipAddress = $ipAddress ?: request()->ip();
        $sessionId = session()->getId();

        $sessionKey = "product_viewed_{$this->id}";
        if (session()->has($sessionKey)) {
            return null;
        }
        session()->put($sessionKey, now()->timestamp);

        $view = $this->productViews()
            ->where(function ($query) use ($userId, $ipAddress) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('ip_address', $ipAddress);
                }
            })
            ->whereDate('viewed_at', $today)
            ->first();

        if ($view) {
            $view->increment('view_count');
            return $view;
        } else {
            return $this->productViews()->create([
                'user_id' => $userId,
                'ip_address' => $ipAddress,
                'viewed_at' => now(),
                'view_count' => 1
            ]);
        }
    }

    /**
     * Lấy các đánh giá của sản phẩm
     */
    public function reviews()
    {
        return $this->hasMany(ReviewRating::class);
    }

    /**
     * Lấy điểm đánh giá trung bình
     */
    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->published()->avg('rating') ?: 0, 1);
    }

    /**
     * Lấy tổng số đánh giá
     */
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->published()->count();
    }

    /**
     * Kiểm tra xem người dùng có thể đánh giá sản phẩm này không
     */
    public function canBeReviewedBy($userId)
    {
        // Kiểm tra xem user đã mua sản phẩm này trong đơn hàng completed
        $completedOrders = Order::where('user_id', $userId)
            ->where('status', 'completed')
            ->pluck('id');

        // Kiểm tra xem sản phẩm có trong đơn hàng
        $productOrderItems = OrderItem::whereIn('order_id', $completedOrders)
            ->whereHas('product', function ($query) {
                $query->where('product_id', $this->id);
            })
            ->exists();

        // Kiểm tra xem người dùng đã đánh giá sản phẩm này chưa
        $alreadyReviewed = $this->reviews()
            ->where('user_id', $userId)
            ->exists();

        return $productOrderItems && !$alreadyReviewed;
    }

    
}
