<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder {
    public function run(): void {
        DB::table('countries')->insert([
            'code' => 'US',
            'name' => 'United States',
        ]);
    }
}
