@extends('admin.layouts.sidebar')

@section('title', 'Chi Tiết Đơn Hàng')

@section('main-content')
<div class="category-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
            <li class="breadcrumb-item current">Chi tiết đơn hàng</li>
        </ol>
    </div>

    <div class="order-details-container">
        <!-- Thông tin đơn hàng -->
        <div class="content-card">
            <div class="card-top order-info-header">
                <div class="order-card-title">
                    <h5>
                        <i class="fas fa-shopping-cart"></i>
                        Đơn Hàng {{ $order->id }}
                    </h5>
                </div>
                <div class="order-meta">
                    <span class="order-date">
                        <i class="far fa-calendar-alt"></i>
                        {{ $order->created_at->format('d/m/Y H:i') }}
                    </span>
                    <span class="status-badge status-{{ $order->status }}">
                        @switch($order->status)
                            @case('pending')
                                Chờ xác nhận
                                @break
                            @case('processing')
                                Đang xử lý
                                @break
                            @case('shipping')
                                Đang giao hàng
                                @break
                            @case('completed')
                                Hoàn thành
                                @break
                            @case('cancelled')
                                Đã hủy
                                @break
                            @default
                                {{ $order->status }}
                        @endswitch
                    </span>
                </div>
            </div>
            
            <div class="card-content">
                <!-- Status Actions -->
                <div class="order-actions-section">
                    <h6 class="section-title">Cập nhật trạng thái</h6>
                    <div class="status-actions">
                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="status-change-form">
                            @csrf
                            @method('PATCH')
                            <div class="status-buttons">
                                <button type="submit" name="status" value="pending" class="status-btn pending-btn {{ $order->status == 'pending' ? 'active' : '' }}" {{ $order->status == 'cancelled' || $order->status == 'completed' ? 'disabled' : '' }}>
                                    <i class="fas fa-clock"></i> Chờ xác nhận
                                </button>
                                <button type="submit" name="status" value="processing" class="status-btn processing-btn {{ $order->status == 'processing' ? 'active' : '' }}" {{ $order->status == 'cancelled' || $order->status == 'completed' ? 'disabled' : '' }}>
                                    <i class="fas fa-cog"></i> Đang xử lý
                                </button>
                                <button type="submit" name="status" value="shipping" class="status-btn shipping-btn {{ $order->status == 'shipping' ? 'active' : '' }}" {{ $order->status == 'cancelled' || $order->status == 'completed' ? 'disabled' : '' }}>
                                    <i class="fas fa-truck"></i> Đang giao
                                </button>
                                <button type="submit" name="status" value="completed" class="status-btn completed-btn {{ $order->status == 'completed' ? 'active' : '' }}" {{ $order->status == 'cancelled' ? 'disabled' : '' }}>
                                    <i class="fas fa-check-circle"></i> Hoàn thành
                                </button>
                                <button type="submit" name="status" value="cancelled" class="status-btn cancelled-btn {{ $order->status == 'cancelled' ? 'active' : '' }}" {{ $order->status == 'completed' ? 'disabled' : '' }}>
                                    <i class="fas fa-times-circle"></i> Hủy
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Order and Customer Info -->
                <div class="order-details-grid">
                    <!-- Customer Information -->
                    <div class="info-section">
                        <h6 class="section-title">Thông tin khách hàng</h6>
                        <div class="customer-details">
                            <div class="detail-item">
                                <span class="detail-label">Họ tên:</span>
                                <span class="detail-value">{{ $order->customer->first_name }} {{ $order->customer->last_name }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Điện thoại:</span>
                                <span class="detail-value">{{ $order->customer->phone }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Email:</span>
                                <span class="detail-value">{{ $order->customer->email ?: 'Không có' }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Địa chỉ:</span>
                                <span class="detail-value">{{ $order->customer->address }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Phường/Xã:</span>
                                <span class="detail-value">{{ $order->customer->wards_name }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Quận/Huyện:</span>
                                <span class="detail-value">{{ $order->customer->districts_name }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Tỉnh/Thành phố:</span>
                                <span class="detail-value">{{ $order->customer->provinces_name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Order Information -->
                    <div class="info-section">
                        <h6 class="section-title">Thông tin đơn hàng</h6>
                        <div class="order-info">
                            <div class="detail-item">
                                <span class="detail-label">Mã đơn hàng:</span>
                                <span class="detail-value order-code">{{ $order->id }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Ngày đặt:</span>
                                <span class="detail-value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Phương thức thanh toán:</span>
                                <span class="detail-value">
                                    @if($order->payment_method == 'cod')
                                        <span class="payment-method cod">Thanh toán khi nhận hàng (COD)</span>
                                    @elseif($order->payment_method == 'bank_transfer')
                                        <span class="payment-method bank">Chuyển khoản ngân hàng</span>
                                    @else
                                        {{ $order->payment_method }}
                                    @endif
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Trạng thái thanh toán:</span>
                                <span class="detail-value">
                                    @if($order->payment_status == 'paid')
                                        <span class="payment-status paid">Đã thanh toán</span>
                                    @elseif($order->payment_status == 'unpaid')
                                        <span class="payment-status unpaid">Chưa thanh toán</span>
                                    @else
                                        {{ $order->payment_status }}
                                    @endif
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Ghi chú:</span>
                                <span class="detail-value">{{ $order->customer->note ?: 'Không có' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Product Items -->
                <div class="info-section products-section">
                    <h6 class="section-title">Sản phẩm</h6>
                    <div class="order-products">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="product-col">Sản phẩm</th>
                                    <th class="weight-col">Quy cách</th>
                                    <th class="price-col">Giá</th>
                                    <th class="qty-col">Số lượng</th>
                                    <th class="total-col">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="product-col">
                                        <div class="product-info">
                                            @if($order->product)
                                                <img src="{{ asset('storage/' . $order->product->image) }}" alt="{{ $order->product->name }}" class="product-image">
                                                <span class="product-name">{{ $order->product->name }}</span>
                                            @else
                                                <span class="product-name product-not-exist">Sản phẩm không tồn tại</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $order->productWeight ? $order->productWeight->weight : 'N/A' }}</td>
                                    <td class="price-col">{{ $order->productWeight ? number_format($order->productWeight->discounted_price) : number_format($order->price) }} ₫</td>
                                    <td class="qty-col">{{ $order->quantity }}</td>
                                    <td class="total-col">{{ $order->productWeight ? number_format($order->productWeight->discounted_price * $order->quantity) : number_format($order->price * $order->quantity) }} ₫</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="order-summary-section">
                    <div class="order-summary">
                        <div class="summary-row">
                            <span class="summary-label">Tạm tính:</span>
                            <span class="summary-value">{{ number_format($order->total - $order->shipping_fee) }} ₫</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Phí vận chuyển:</span>
                            <span class="summary-value">{{ number_format($order->shipping_fee) }} ₫</span>
                        </div>
                        <div class="summary-row total-row">
                            <span class="summary-label">Tổng cộng:</span>
                            <span class="summary-value order-total">{{ number_format($order->total) }} ₫</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-actions mt-4">
            <a href="{{ route('admin.orders.index') }}" class="back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
            
            <!-- Thêm các nút khác nếu cần -->
            <div class="action-group">
                <a href="#" class="btn-secondary">
                    <i class="fas fa-print"></i> In đơn hàng
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .order-details-container {
        margin-bottom: 30px;
    }
    
    .order-info-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .order-card-title {
        display: flex;
        align-items: center;
    }
    
    .order-card-title h5 {
        margin: 0;
        font-weight: 600;
        display: flex;
        align-items: center;
    }
    
    .order-card-title h5 i {
        margin-right: 10px;
        color: var(--primary-color);
    }
    
    .order-meta {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .order-date {
        font-size: 14px;
        color: #666;
    }
    
    .order-date i {
        margin-right: 5px;
    }
    
    .section-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 1px solid #eee;
        color: var(--primary-color);
    }
    
    .order-details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .info-section {
        background-color: #fff;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    
    .detail-item {
        margin-bottom: 8px;
        display: flex;
    }
    
    .detail-label {
        font-weight: 500;
        color: #666;
        min-width: 160px;
        flex: 0 0 auto;
    }
    
    .detail-value {
        flex: 1;
        color: #333;
    }
    
    .order-code {
        font-weight: 600;
        color: var(--primary-color);
    }
    
    .payment-method, .payment-status {
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
    }
    
    .payment-method.cod {
        background-color: #f8f9fa;
        color: #495057;
    }
    
    .payment-method.bank {
        background-color: #e3f2fd;
        color: #1976d2;
    }
    
    .payment-status.paid {
        background-color: #e8f5e9;
        color: #2e7d32;
    }
    
    .payment-status.unpaid {
        background-color: #fff8e1;
        color: #ff8f00;
    }
    
    .order-products {
        width: 100%;
        overflow-x: auto;
    }
    
    .order-products table {
        width: 100%;
    }
    
    .product-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .product-image {
        
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #eee;
    }
    
    .product-name {
        font-weight: 500;
    }
    
    .price-col, .total-col {
        font-weight: 500;
        color: #d35400;
    }
    
    .qty-col {
        text-align: center;
    }
    
    .order-summary-section {
        margin-top: 20px;
    }
    
    .order-summary {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-left: auto;
        width: 300px;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    
    .summary-label {
        color: #666;
    }
    
    .summary-value {
        font-weight: 500;
    }
    
    .total-row {
        margin-top: 10px;
        border-top: 1px solid #e0e0e0;
        padding-top: 10px;
    }
    
    .order-total {
        font-size: 18px;
        font-weight: 600;
        color: #d35400;
    }
    
    .order-actions-section {
        background-color: #fff;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    
    .status-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .status-btn {
        padding: 8px 15px;
        border-radius: 6px;
        border: none;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        background-color: #f8f9fa;
        color: #4a4a4a;
    }
    
    .status-btn:hover:not(:disabled):not(.active) {
        background-color: #e9ecef;
    }
    
    .status-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .status-btn.active {
        position: relative;
    }
    
    .status-btn.active:after {
        content: "";
        position: absolute;
        bottom: -5px;
        left: 50%;
        transform: translateX(-50%);
        width: 6px;
        height: 6px;
        background-color: currentColor;
        border-radius: 50%;
    }
    
    .pending-btn.active {
        background-color: #fff8ec;
        color: #ff9800;
    }
    
    .processing-btn.active {
        background-color: #e3f2fd;
        color: #2196f3;
    }
    
    .shipping-btn.active {
        background-color: #e8f5e9;
        color: #4caf50;
    }
    
    .completed-btn.active {
        background-color: #e8f5e9;
        color: #00897b;
    }
    
    .cancelled-btn.active {
        background-color: #fef0f0;
        color: #e53935;
    }
    
    .btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background-color: #6c757d;
        color: white;
        border-radius: 6px;
        font-weight: 500;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .btn-secondary:hover {
        background-color: #5a6268;
        color: white;
    }
    
    @media (max-width: 768px) {
        .order-details-grid {
            grid-template-columns: 1fr;
        }
        
        .order-summary {
            width: 100%;
        }
        
        .detail-item {
            flex-direction: column;
        }
        
        .detail-label {
            min-width: 100%;
            margin-bottom: 4px;
        }
    }

    .product-not-exist {
        color: #e53935;
        font-style: italic;
        background-color: #ffebee;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 13px;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xác nhận khi thay đổi trạng thái
        const statusForm = document.querySelector('.status-change-form');
        const statusButtons = document.querySelectorAll('.status-btn');
        
        statusButtons.forEach(button => {
            if (!button.disabled) {
                button.addEventListener('click', function(e) {
                    if (this.classList.contains('active')) {
                        // Nếu nút đã active thì không làm gì
                        e.preventDefault();
                        return;
                    }
                    
                    const newStatus = this.value;
                    let confirmMessage = `Bạn có chắc chắn muốn thay đổi trạng thái đơn hàng thành "${this.innerText.trim()}"?`;
                    
                    if (newStatus === 'cancelled') {
                        confirmMessage = 'Bạn có chắc chắn muốn hủy đơn hàng này? Hành động này không thể hoàn tác.';
                    } else if (newStatus === 'completed') {
                        confirmMessage = 'Xác nhận đơn hàng đã hoàn thành? Điều này sẽ đánh dấu đơn hàng đã giao thành công.';
                    }
                    
                    if (!confirm(confirmMessage)) {
                        e.preventDefault();
                    }
                });
            }
        });
    });
</script>
@endpush