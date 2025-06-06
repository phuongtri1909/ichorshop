<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateSeeder extends Seeder
{
    public function run(): void
    {
        $json = file_get_contents(database_path('data/states.json'));
        $states = json_decode($json, true);

        $usStates = array_filter($states, fn($state) => $state['country_code'] === 'US');

        foreach ($usStates as $state) {
            DB::table('states')->insert([
                'id' => $state['id'],
                'country_code' => $state['country_code'],
                'name' => $state['name'],
                'code' => $state['state_code'],
            ]);
        }
    }
}
