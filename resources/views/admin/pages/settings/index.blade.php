@extends('admin.layouts.sidebar')

@section('title', 'Cài đặt hệ thống')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Cài đặt hệ thống</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-cog icon-title"></i>
                    <h5>Cài đặt hệ thống</h5>
                </div>
            </div>

            <div class="card-content">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="settings-tabs">
                    <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request('tab') == 'order' || !request('tab') ? 'active' : '' }}" 
                               id="order-tab" data-toggle="tab" href="#order" role="tab">
                                <i class="fas fa-shopping-cart"></i> Đơn hàng
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request('tab') == 'smtp' ? 'active' : '' }}" 
                               id="smtp-tab" data-toggle="tab" href="#smtp" role="tab">
                                <i class="fas fa-envelope"></i> SMTP
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request('tab') == 'google' ? 'active' : '' }}" 
                               id="google-tab" data-toggle="tab" href="#google" role="tab">
                                <i class="fab fa-google"></i> Google
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request('tab') == 'paypal' ? 'active' : '' }}" 
                               id="paypal-tab" data-toggle="tab" href="#paypal" role="tab">
                                <i class="fab fa-paypal"></i> PayPal
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-4" id="settingsTabContent">
                        <!-- Order Settings Tab -->
                        <div class="tab-pane fade {{ request('tab') == 'order' || !request('tab') ? 'show active' : '' }}" 
                             id="order" role="tabpanel">
                            @if ($orderSettings->isEmpty())
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                    <h4>Chưa có cài đặt nào</h4>
                                    <p>Vui lòng chạy seed để tạo cài đặt đơn hàng.</p>
                                </div>
                            @else
                                <form action="{{ route('admin.setting.update.order') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="data-table-container">
                                        <table class="data-table">
                                            <thead>
                                                <tr>
                                                    <th class="column-medium">Key</th>
                                                    <th class="column-large">Tên cài đặt</th>
                                                    <th class="column-large">Giá trị</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($orderSettings as $setting)
                                                    <tr>
                                                        <td>
                                                            <span class="setting-key">{{ $setting->key }}</span>
                                                            <input type="hidden" name="settings[{{ $setting->key }}][key]" value="{{ $setting->key }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" 
                                                                name="settings[{{ $setting->key }}][name]" 
                                                                value="{{ $setting->name }}" required>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" 
                                                                name="settings[{{ $setting->key }}][value]" 
                                                                value="{{ $setting->value }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="form-actions">
                                        <button type="submit" class="action-button">
                                            <i class="fas fa-save"></i> Lưu cài đặt
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>

                        <!-- SMTP Settings Tab -->
                        <div class="tab-pane fade {{ request('tab') == 'smtp' ? 'show active' : '' }}" 
                             id="smtp" role="tabpanel">
                            <form action="{{ route('admin.setting.update.smtp') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="form-group">
                                    <label for="mailer">Mailer</label>
                                    <input type="text" id="mailer" name="mailer" class="form-control" 
                                           value="{{ $smtpSetting->mailer ?? 'smtp' }}" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="host">Host</label>
                                    <input type="text" id="host" name="host" class="form-control" 
                                           value="{{ $smtpSetting->host ?? '' }}" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="port">Port</label>
                                    <input type="text" id="port" name="port" class="form-control" 
                                           value="{{ $smtpSetting->port ?? '' }}" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" id="username" name="username" class="form-control" 
                                           value="{{ $smtpSetting->username ?? '' }}" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" id="password" name="password" class="form-control" 
                                           value="{{ $smtpSetting->password ?? '' }}" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="encryption">Encryption</label>
                                    <select id="encryption" name="encryption" class="form-control">
                                        <option value="">None</option>
                                        <option value="tls" {{ ($smtpSetting->encryption ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ ($smtpSetting->encryption ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="from_address">From Address</label>
                                    <input type="email" id="from_address" name="from_address" class="form-control" 
                                           value="{{ $smtpSetting->from_address ?? '' }}" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="from_name">From Name</label>
                                    <input type="text" id="from_name" name="from_name" class="form-control" 
                                           value="{{ $smtpSetting->from_name ?? '' }}">
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="action-button">
                                        <i class="fas fa-save"></i> Lưu cài đặt SMTP
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Google Settings Tab -->
                        <div class="tab-pane fade {{ request('tab') == 'google' ? 'show active' : '' }}" 
                             id="google" role="tabpanel">
                            <form action="{{ route('admin.setting.update.google') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="form-group">
                                    <label for="google_client_id">Client ID</label>
                                    <input type="text" id="google_client_id" name="google_client_id" class="form-control" 
                                           value="{{ $googleSetting->google_client_id ?? '' }}" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="google_client_secret">Client Secret</label>
                                    <input type="password" id="google_client_secret" name="google_client_secret" class="form-control" 
                                           value="{{ $googleSetting->google_client_secret ?? '' }}" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="google_redirect">Redirect URL</label>
                                    <input type="text" id="google_redirect" name="google_redirect" class="form-control" 
                                           value="{{ $googleSetting->google_redirect ?? 'auth/google/callback' }}" required>
                                    <small class="form-text text-muted">
                                        URL relative to your site (e.g., auth/google/callback)
                                    </small>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="action-button">
                                        <i class="fas fa-save"></i> Lưu cài đặt Google
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- PayPal Settings Tab -->
                        <div class="tab-pane fade {{ request('tab') == 'paypal' ? 'show active' : '' }}" 
                             id="paypal" role="tabpanel">
                            <form action="{{ route('admin.setting.update.paypal') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="form-group">
                                    <label for="mode">Mode</label>
                                    <select id="mode" name="mode" class="form-control" required>
                                        <option value="sandbox" {{ ($paypalSetting->mode ?? 'sandbox') == 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                                        <option value="live" {{ ($paypalSetting->mode ?? '') == 'live' ? 'selected' : '' }}>Live</option>
                                    </select>
                                </div>
                                
                                <div class="settings-section">
                                    <h4>Sandbox Credentials</h4>
                                    
                                    <div class="form-group">
                                        <label for="sandbox_username">Username</label>
                                        <input type="text" id="sandbox_username" name="sandbox_username" class="form-control" 
                                               value="{{ $paypalSetting->sandbox_username ?? '' }}">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="sandbox_password">Password</label>
                                        <input type="password" id="sandbox_password" name="sandbox_password" class="form-control" 
                                               value="{{ $paypalSetting->sandbox_password ?? '' }}">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="sandbox_secret">Secret</label>
                                        <input type="password" id="sandbox_secret" name="sandbox_secret" class="form-control" 
                                               value="{{ $paypalSetting->sandbox_secret ?? '' }}">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="sandbox_app_id">App ID</label>
                                        <input type="text" id="sandbox_app_id" name="sandbox_app_id" class="form-control" 
                                               value="{{ $paypalSetting->sandbox_app_id ?? 'APP-80W284485P519543T' }}">
                                    </div>
                                </div>
                                
                                <div class="settings-section">
                                    <h4>Live Credentials</h4>
                                    
                                    <div class="form-group">
                                        <label for="live_username">Username</label>
                                        <input type="text" id="live_username" name="live_username" class="form-control" 
                                               value="{{ $paypalSetting->live_username ?? '' }}">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="live_password">Password</label>
                                        <input type="password" id="live_password" name="live_password" class="form-control" 
                                               value="{{ $paypalSetting->live_password ?? '' }}">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="live_secret">Secret</label>
                                        <input type="password" id="live_secret" name="live_secret" class="form-control" 
                                               value="{{ $paypalSetting->live_secret ?? '' }}">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="live_app_id">App ID</label>
                                        <input type="text" id="live_app_id" name="live_app_id" class="form-control" 
                                               value="{{ $paypalSetting->live_app_id ?? '' }}">
                                    </div>
                                </div>
                                
                                <div class="settings-section">
                                    <h4>Common Configuration</h4>
                                    
                                    <div class="form-group">
                                        <label for="payment_action">Payment Action</label>
                                        <select id="payment_action" name="payment_action" class="form-control" required>
                                            <option value="Sale" {{ ($paypalSetting->payment_action ?? 'Sale') == 'Sale' ? 'selected' : '' }}>Sale</option>
                                            <option value="Authorization" {{ ($paypalSetting->payment_action ?? '') == 'Authorization' ? 'selected' : '' }}>Authorization</option>
                                            <option value="Order" {{ ($paypalSetting->payment_action ?? '') == 'Order' ? 'selected' : '' }}>Order</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="currency">Currency</label>
                                        <input type="text" id="currency" name="currency" class="form-control" 
                                               value="{{ $paypalSetting->currency ?? 'USD' }}" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="validate_ssl" name="validate_ssl"
                                                   {{ ($paypalSetting->validate_ssl ?? false) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="validate_ssl">Validate SSL when connecting to PayPal</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="action-button">
                                        <i class="fas fa-save"></i> Lưu cài đặt PayPal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Tabs styling */
    .settings-tabs .nav-tabs {
        border-bottom: 1px solid #dee2e6;
    }
    
    .settings-tabs .nav-link {
        color: #495057;
        background-color: #fff;
        border: 1px solid transparent;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
        padding: 0.5rem 1rem;
        margin-right: 0.25rem;
    }
    
    .settings-tabs .nav-link.active {
        color: #007bff;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
        font-weight: bold;
    }
    
    .settings-tabs .nav-link i {
        margin-right: 5px;
    }
    
    /* Settings sections */
    .settings-section {
        margin-bottom: 2rem;
        padding: 1rem;
        background-color: #f9f9f9;
        border-radius: 0.25rem;
    }
    
    .settings-section h4 {
        margin-bottom: 1rem;
        color: #333;
        border-bottom: 1px solid #ddd;
        padding-bottom: 0.5rem;
    }
    

    
    .form-text {
        margin-top: 0.25rem;
        font-size: 0.85em;
        color: #6c757d;
    }
    
    /* Order settings specific styles */
    .setting-key {
        font-family: monospace;
        background-color: #f5f5f5;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.9em;
        color: #555;
    }
    
    .form-actions {
        margin-top: 20px;
        display: flex;
        justify-content: flex-end;
    }
    
    .action-button {
        display: inline-flex;
        align-items: center;
        padding: 8px 16px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }
    
    .action-button i {
        margin-right: 5px;
    }
    
    .action-button:hover {
        background-color: #0069d9;
    }
    
    .custom-control {
        position: relative;
        display: block;
        min-height: 1.5rem;
        padding-left: 1.5rem;
    }
    
    .custom-control-input {
        position: absolute;
        z-index: -1;
        opacity: 0;
    }
    
    .custom-control-label {
        position: relative;
        margin-bottom: 0;
        vertical-align: top;
        cursor: pointer;
    }
    
    .custom-control-label::before {
        position: absolute;
        top: 0.25rem;
        left: -1.5rem;
        display: block;
        width: 1rem;
        height: 1rem;
        content: "";
        background-color: #fff;
        border: 1px solid #adb5bd;
        border-radius: 0.25rem;
    }
    
    .custom-control-input:checked ~ .custom-control-label::before {
        color: #fff;
        border-color: #007bff;
        background-color: #007bff;
    }
    
    .alert {
        padding: 12px 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }
    
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Manual tab activation function
        function activateTab(tabId) {
            // Hide all tab contents
            $('.tab-pane').removeClass('show active');
            
            // Show the selected tab content
            $('#' + tabId).addClass('show active');
            
            // Update tab buttons
            $('.nav-tabs .nav-link').removeClass('active');
            $('.nav-tabs a[href="#' + tabId + '"]').addClass('active');
        }
        
        // Set initial active tab based on URL
        var activeTabParam = window.location.search.match(/tab=([^&]*)/);
        if (activeTabParam) {
            activateTab(activeTabParam[1]);
        }
        
        // Handle tab clicks
        $('.nav-tabs a').on('click', function(e) {
            e.preventDefault();
            var tabId = $(this).attr('href').substr(1);
            
            // Activate the tab
            activateTab(tabId);
            
            // Update URL
            history.replaceState(null, null, '?tab=' + tabId);
        });
    });
</script>
@endpush