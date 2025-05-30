<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Province;
use App\Models\District;
use App\Models\Ward;

class ImportVietnamAddresses extends Command
{
    protected $signature = 'import:vietnam-addresses';
    protected $description = 'Import provinces, districts and wards data from API';

    public function handle()
    {
        $this->info('Importing Vietnam addresses...');
        
        try {
            // Lấy danh sách tỉnh/thành phố
            $provinces = Http::get('https://provinces.open-api.vn/api/p/')->json();
            
            $this->info('Importing ' . count($provinces) . ' provinces...');
            
            foreach ($provinces as $province) {
                // Lấy chi tiết tỉnh/thành phố kèm quận/huyện
                $provinceDetail = Http::get("https://provinces.open-api.vn/api/p/{$province['code']}?depth=2")->json();
                
                // Lưu tỉnh/thành phố
                Province::updateOrCreate(
                    ['code' => $province['code']],
                    [
                        'name' => $province['name'],
                        'division_type' => $province['division_type'] ?? null,
                        'codename' => $province['codename'] ?? null,
                        'phone_code' => $province['phone_code'] ?? null,
                    ]
                );
                
                // Import quận/huyện
                if (!empty($provinceDetail['districts'])) {
                    foreach ($provinceDetail['districts'] as $district) {
                        $this->info("Importing district: {$district['name']}");
                        
                        // Lưu quận/huyện
                        District::updateOrCreate(
                            ['code' => $district['code']],
                            [
                                'name' => $district['name'],
                                'province_code' => $province['code'],
                                'division_type' => $district['division_type'] ?? null,
                                'codename' => $district['codename'] ?? null,
                            ]
                        );
                        
                        // Lấy chi tiết quận/huyện kèm phường/xã
                        $districtDetail = Http::get("https://provinces.open-api.vn/api/d/{$district['code']}?depth=2")->json();
                        
                        // Import phường/xã
                        if (!empty($districtDetail['wards'])) {
                            foreach ($districtDetail['wards'] as $ward) {
                                // Lưu phường/xã
                                Ward::updateOrCreate(
                                    ['code' => $ward['code']],
                                    [
                                        'name' => $ward['name'],
                                        'district_code' => $district['code'],
                                        'division_type' => $ward['division_type'] ?? null,
                                        'codename' => $ward['codename'] ?? null,
                                    ]
                                );
                            }
                        }
                        
                        // Tránh quá tải API
                        sleep(1);
                    }
                }
            }
            
            $this->info('Import completed successfully.');
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Import failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}