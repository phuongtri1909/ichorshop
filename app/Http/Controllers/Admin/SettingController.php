<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\OrderSetting;
use App\Models\SMTPSetting;
use App\Models\GoogleSetting;
use App\Models\PaypalSetting;

class SettingController extends Controller
{
    public function index()
    {
        $orderSettings = OrderSetting::all();
        $smtpSetting = SMTPSetting::first() ?? new SMTPSetting();
        $googleSetting = GoogleSetting::first() ?? new GoogleSetting();
        $paypalSetting = PaypalSetting::first() ?? new PaypalSetting();
        
        return view('admin.pages.settings.index', compact(
            'orderSettings', 
            'smtpSetting', 
            'googleSetting', 
            'paypalSetting'
        ));
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.name' => 'required|string|max:255',
            'settings.*.value' => 'nullable|string',
        ]);

        $settings = $request->input('settings');

        foreach ($settings as $key => $setting) {
            OrderSetting::where('key', $key)
                ->update([
                    'name' => $setting['name'],
                    'value' => $setting['value'],
                ]);
        }

        return redirect()->route('admin.setting.index', ['tab' => 'order'])
            ->with('success', 'Cài đặt đơn hàng đã được cập nhật thành công.');
    }

    public function updateSMTP(Request $request)
    {
        $request->validate([
            'mailer' => 'required|string',
            'host' => 'required|string',
            'port' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string',
            'encryption' => 'nullable|string',
            'from_address' => 'required|email',
            'from_name' => 'nullable|string',
        ]);

        $smtpSetting = SMTPSetting::first();
        if (!$smtpSetting) {
            $smtpSetting = new SMTPSetting();
        }

        $smtpSetting->fill($request->all());
        $smtpSetting->save();

        return redirect()->route('admin.setting.index', ['tab' => 'smtp'])
            ->with('success', 'Cài đặt SMTP đã được cập nhật thành công.');
    }

    public function updateGoogle(Request $request)
    {
        $request->validate([
            'google_client_id' => 'required|string',
            'google_client_secret' => 'required|string',
            'google_redirect' => 'required|string',
        ]);

        $googleSetting = GoogleSetting::first();
        if (!$googleSetting) {
            $googleSetting = new GoogleSetting();
        }

        $googleSetting->fill($request->all());
        $googleSetting->save();

        return redirect()->route('admin.setting.index', ['tab' => 'google'])
            ->with('success', 'Cài đặt Google đã được cập nhật thành công.');
    }

    public function updatePaypal(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:sandbox,live',
            'sandbox_username' => 'nullable|string',
            'sandbox_password' => 'nullable|string',
            'sandbox_secret' => 'nullable|string',
            'sandbox_app_id' => 'nullable|string',
            'live_username' => 'nullable|string',
            'live_password' => 'nullable|string',
            'live_secret' => 'nullable|string',
            'live_app_id' => 'nullable|string',
            'payment_action' => 'required|string',
            'currency' => 'required|string',
            'validate_ssl' => 'boolean',
        ]);

        $paypalSetting = PaypalSetting::first();
        if (!$paypalSetting) {
            $paypalSetting = new PaypalSetting();
        }

        // Handle checkbox field
        $request->merge([
            'validate_ssl' => $request->has('validate_ssl')
        ]);

        $paypalSetting->fill($request->all());
        $paypalSetting->save();

        return redirect()->route('admin.setting.index', ['tab' => 'paypal'])
            ->with('success', 'Cài đặt PayPal đã được cập nhật thành công.');
    }
}
