<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

class FilterCitiesForUS extends Command
{
    protected $signature = 'filter:cities-us';
    protected $description = 'Lọc các thành phố thuộc Mỹ từ cities.json';

    public function handle()
    {
        $inputPath = database_path('data/cities.json');
        $outputPath = database_path('data/cities_us.json');

        if (!file_exists($inputPath)) {
            $this->error("Không tìm thấy file: $inputPath");
            return 1;
        }

        $json = file_get_contents($inputPath);
        $cities = json_decode($json, true);

        if (!is_array($cities)) {
            $this->error("Lỗi JSON.");
            return 1;
        }

        $usCities = array_filter($cities, fn($city) => $city['country_code'] === 'US');

        file_put_contents($outputPath, json_encode(array_values($usCities), JSON_PRETTY_PRINT));
        $this->info("Đã tạo: $outputPath (".count($usCities)." thành phố)");

        return 0;
    }
}
