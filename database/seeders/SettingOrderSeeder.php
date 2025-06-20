<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'name' => 'Shipping cost',
                'key' => 'shipping_cost',
                'value' => '1',
            ],
            [
                'name' => 'Tax Percentage',
                'key' => 'tax_percentage',
                'value' => '5',
            ]
        ];

        foreach ($settings as $setting) {
            \App\Models\OrderSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
