<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Providers\FilterProvider;

class RefreshProductFiltersCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:refresh-product-filters {--prices-only : Only refresh price range}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the product filters cache (colors, sizes, and price range)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pricesOnly = $this->option('prices-only');

        if ($pricesOnly) {
            $this->info('Refreshing product price range cache...');
            Cache::forget('product_price_range');
            $priceRange = FilterProvider::calculatePriceRange();
            $this->info('Price range cache refreshed: ' . $priceRange['min'] . ' - ' . $priceRange['max']);
        } else {
            $this->info('Refreshing all product filters cache...');
            Cache::forget('product_colors');
            Cache::forget('product_sizes');
            Cache::forget('product_price_range');
            $this->info('All product filters cache cleared and will be regenerated on next access.');
        }
        
        $this->info('Cache operation completed successfully!');
    }
}