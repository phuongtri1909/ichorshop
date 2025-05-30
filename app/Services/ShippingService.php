<?php

namespace App\Services;

use App\Models\Province;
use App\Models\ProductWeight;

class ShippingService
{
    /**
     * Tính phí vận chuyển dựa trên địa chỉ và thông tin sản phẩm
     *
     * @param string $provinceCode Mã tỉnh/thành phố
     * @param string $districtCode Mã quận/huyện
     * @param string $wardCode Mã phường/xã
     * @param int $productWeightId ID của trọng lượng sản phẩm
     * @param int $quantity Số lượng sản phẩm
     * @return array Thông tin phí vận chuyển và thời gian giao hàng
     */
    public function calculate($provinceCode, $districtCode, $wardCode, $productWeightId, $quantity)
    {
        // Lấy thông tin trọng lượng sản phẩm
        $productWeight = ProductWeight::findOrFail($productWeightId);
        
        // Lấy thông tin về tỉnh/thành phố để tính phí vận chuyển theo khu vực
        $province = Province::where('code', $provinceCode)->first();
        
        if (!$province) {
            throw new \Exception("Không tìm thấy thông tin tỉnh/thành phố.");
        }
        
        // Phí vận chuyển cơ bản theo khu vực
        $baseShippingFee = $this->getBaseShippingFeeByRegion($province);
        
        // Tính phí vận chuyển dựa trên trọng lượng và số lượng
        $weightFactor = $this->parseWeight($productWeight->weight);
        $totalWeight = $weightFactor * $quantity;
        
        // Tính phụ phí cho trọng lượng lớn
        $weightSurcharge = $this->calculateWeightSurcharge($totalWeight);
        
        // Tính tổng phí vận chuyển
        $shippingFee = $baseShippingFee + $weightSurcharge;
        
        // Làm tròn phí vận chuyển (làm tròn đến 1000đ gần nhất)
        $shippingFee = round($shippingFee / 1000) * 1000;
        
        // Tạo ID xác thực để đảm bảo phí vận chuyển không bị sửa đổi
        $verificationHash = $this->generateVerificationHash($provinceCode, $districtCode, $wardCode, $productWeightId, $quantity, $shippingFee);
        
        return [
            'success' => true,
            'shipping_fee' => $shippingFee,
            'currency' => 'VND',
            'estimated_delivery_time' => $this->getEstimatedDeliveryTime($province),
            'verification' => $verificationHash
        ];
    }
    
    /**
     * Xác thực phí vận chuyển từ frontend
     * 
     * @param string $provinceCode Mã tỉnh/thành phố
     * @param string $districtCode Mã quận/huyện
     * @param string $wardCode Mã phường/xã
     * @param int $productWeightId ID của trọng lượng sản phẩm
     * @param int $quantity Số lượng sản phẩm
     * @param float $shippingFee Phí vận chuyển cần xác thực
     * @param string $verification Mã xác thực từ frontend
     * @return boolean Kết quả xác thực
     */
    public function verify($provinceCode, $districtCode, $wardCode, $productWeightId, $quantity, $shippingFee, $verification)
    {
        $calculatedHash = $this->generateVerificationHash($provinceCode, $districtCode, $wardCode, $productWeightId, $quantity, $shippingFee);
        return $calculatedHash === $verification;
    }

    /**
     * Tạo một mã hash để xác thực phí vận chuyển
     */
    private function generateVerificationHash($provinceCode, $districtCode, $wardCode, $productWeightId, $quantity, $shippingFee)
    {
        $data = implode('|', [$provinceCode, $districtCode, $wardCode, $productWeightId, $quantity, $shippingFee]);
        return hash_hmac('sha256', $data, config('app.key'));
    }

    /**
     * Phí vận chuyển cơ bản theo khu vực
     */
    private function getBaseShippingFeeByRegion($province)
    {
        // Phân loại theo tỉnh thành
        $hanoiHcm = ['01', '79']; // Mã Hà Nội và TP. HCM
        
        // Danh sách các tỉnh thành lớn 
        $majorCities = ['48', '92', '31', '75', '96']; // Đà Nẵng, Cần Thơ, Hải Phòng, Đồng Nai, Bình Dương...
        
        if (in_array($province->code, $hanoiHcm)) {
            // Hà Nội, TP.HCM (15,000 - 25,000 VNĐ)
            return rand(15000, 25000);
        } elseif (in_array($province->code, $majorCities)) {
            // Các thành phố lớn khác (25,000 - 35,000 VNĐ)
            return rand(25000, 35000);
        } else {
            // Các tỉnh còn lại (30,000 - 45,000 VNĐ)
            return rand(30000, 45000);
        }
    }
    
    /**
     * Chuyển đổi trọng lượng từ chuỗi sang số (đơn vị gram)
     */
    private function parseWeight($weightString)
    {
        // Mặc định là 500g
        $weight = 500;
        
        // Phân tích chuỗi để lấy giá trị số
        if (preg_match('/(\d+)\s*g/i', $weightString, $matches)) {
            // Nếu đơn vị là g
            $weight = (int) $matches[1];
        } elseif (preg_match('/(\d+)\s*kg/i', $weightString, $matches)) {
            // Nếu đơn vị là kg
            $weight = (int) $matches[1] * 1000;
        }
        
        return $weight;
    }
    
    /**
     * Tính phụ phí cho hàng nặng
     */
    private function calculateWeightSurcharge($totalWeight)
    {
        if ($totalWeight <= 500) {
            return 0;
        } elseif ($totalWeight <= 1000) {
            return 5000;
        } elseif ($totalWeight <= 2000) {
            return 10000;
        } else {
            // Trên 2kg, mỗi 1kg tăng thêm 5,000 VNĐ
            $extraKg = ceil(($totalWeight - 2000) / 1000);
            return 10000 + ($extraKg * 5000);
        }
    }
    
    /**
     * Tính thời gian giao hàng dự kiến
     */
    private function getEstimatedDeliveryTime($province)
    {
        $hanoiHcm = ['01', '79']; // Mã Hà Nội và TP. HCM
        $majorCities = ['48', '92', '31', '75', '96']; // Đà Nẵng, Cần Thơ, Hải Phòng...
        
        if (in_array($province->code, $hanoiHcm)) {
            return '1-2 ngày';
        } elseif (in_array($province->code, $majorCities)) {
            return '2-3 ngày';
        } else {
            return '3-5 ngày';
        }
    }
}