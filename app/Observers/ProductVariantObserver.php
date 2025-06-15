<?php

namespace App\Observers;

use App\Models\ProductVariant;
use Illuminate\Support\Facades\Cache;

class ProductVariantObserver
{
    public function created(ProductVariant $variant)
    {
        $this->clearColorAndSizeCache();
        $this->clearPriceRangeCache();
    }

    public function updated(ProductVariant $variant)
    {
        // Kiểm tra xem các trường color, color_name hoặc size có được cập nhật không
        if ($variant->isDirty('color') || $variant->isDirty('color_name') || $variant->isDirty('size') || $variant->isDirty('status')) {
            $this->clearColorAndSizeCache();
        }
        
        // Nếu giá thay đổi, cập nhật cache phạm vi giá
        if ($variant->isDirty('price') || $variant->isDirty('status')) {
            $this->clearPriceRangeCache();
        }
    }
    
    public function deleted(ProductVariant $variant)
    {
        $this->clearColorAndSizeCache();
        $this->clearPriceRangeCache();
    }

    public function restored(ProductVariant $variant)
    {
        $this->clearColorAndSizeCache();
        $this->clearPriceRangeCache();
    }

    public function forceDeleted(ProductVariant $variant)
    {
        $this->clearColorAndSizeCache();
        $this->clearPriceRangeCache();
    }
    
    private function clearColorAndSizeCache()
    {
        Cache::forget('product_colors');
        Cache::forget('product_sizes');
    }
    
    private function clearPriceRangeCache()
    {
        Cache::forget('product_price_range');
    }
}