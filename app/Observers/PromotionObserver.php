<?php

namespace App\Observers;

use App\Models\Promotion;
use Illuminate\Support\Facades\Cache;

class PromotionObserver
{
    /**
     * Handle the Promotion "created" event.
     */
    public function created(Promotion $promotion)
    {
        $this->clearPriceRangeCache();
    }

    /**
     * Handle the Promotion "updated" event.
     */
    public function updated(Promotion $promotion)
    {
        // Nếu thay đổi giá trị, loại giảm giá, trạng thái hoặc thời gian, cần xóa cache
        if ($promotion->isDirty('value') || $promotion->isDirty('type') || 
            $promotion->isDirty('status') || $promotion->isDirty('start_date') || 
            $promotion->isDirty('end_date')) {
            $this->clearPriceRangeCache();
        }
    }

    /**
     * Handle the Promotion "deleted" event.
     */
    public function deleted(Promotion $promotion)
    {
        $this->clearPriceRangeCache();
    }

    /**
     * Handle the Promotion "restored" event.
     */
    public function restored(Promotion $promotion)
    {
        $this->clearPriceRangeCache();
    }

    /**
     * Handle the Promotion "force deleted" event.
     */
    public function forceDeleted(Promotion $promotion)
    {
        $this->clearPriceRangeCache();
    }
    
    /**
     * Clear price range cache
     */
    private function clearPriceRangeCache()
    {
        Cache::forget('product_price_range');
    }
}