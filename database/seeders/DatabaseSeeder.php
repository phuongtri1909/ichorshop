<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\AdminSeeder;
use Database\Seeders\StateSeeder;
use Database\Seeders\CountrySeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            //AdminSeeder::class,
            //CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
        ]);
    }
}
