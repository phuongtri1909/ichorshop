<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $json = file_get_contents(database_path('data/cities_us.json'));
        $cities = json_decode($json, true);

        $usCities = array_filter($cities, fn($city) => $city['country_code'] === 'US');

        foreach ($usCities as $city) {
            // Chỉ import nếu state_id tồn tại
            DB::table('cities')->insert([
                'id' => $city['id'],
                'state_id' => $city['state_id'],
                'name' => $city['name'],
            ]);
        }
    }
}
