<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomEncryptable;

class PaypalSetting extends Model
{
    use CustomEncryptable;

    protected $table = 'paypal_settings';

    protected $fillable = [
        'mode',
        'sandbox_username',
        'sandbox_password',
        'sandbox_secret',
        'sandbox_certificate',
        'sandbox_app_id',
        'live_username',
        'live_password',
        'live_secret',
        'live_certificate',
        'live_app_id',
        'payment_action',
        'currency',
        'billing_type',
        'notify_url',
        'locale',
        'validate_ssl',
    ];

    protected $casts = [
        'validate_ssl' => 'boolean',
    ];

    protected $encryptable = [
        'sandbox_password',
        'sandbox_secret',
        'sandbox_certificate',
        'live_password',
        'live_secret',
        'live_certificate',
    ];
}
