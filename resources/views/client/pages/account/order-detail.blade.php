@extends('client.layouts.information')

@section('info_title', 'Order Detail - ' . request()->getHost())
@section('info_description', 'View details of your order')
@section('info_keyword', 'Order Detail, ' . request()->getHost())
@section('info_section_title', 'Order #' . $order->order_code)
@section('info_section_desc', 'Placed on ' . $order->created_at->format('F d, Y'))

@push('breadcrumb')
    @include('components.breadcrumb', [
        'title' => 'Order Detail',
        'items' => $breadcrumbItems,
    ])
@endpush

@section('info_content')
    <div class="order-detail-wrapper">
        <div class="order-detail-header mb-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Order Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <p class="mb-1"><strong>Order Number:</strong></p>
                                    <p class="mb-3">{{ $order->order_code }}</p>

                                    <p class="mb-1"><strong>Date Placed:</strong></p>
                                    <p class="mb-3">{{ $order->created_at->format('F d, Y g:i A') }}</p>

                                    <p class="mb-1"><strong>Payment Method:</strong></p>
                                    <p class="mb-0">{{ ucfirst($order->payment_method) }}</p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-1"><strong>Order Status:</strong></p>
                                    <p class="mb-3">
                                        <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : ($order->status === 'processing' ? 'info' : 'danger')) }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </p>

                                    <p class="mb-1"><strong>Payment Status:</strong></p>
                                    <p class="mb-3">
                                        <span class="badge bg-{{ $order->status_payment === 'completed' ? 'success' : ($order->status_payment === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($order->status_payment) }}
                                        </span>
                                    </p>

                                    @if ($order->status !== 'cancelled' && $order->status !== 'completed')
                                        <p class="mb-0">
                                            <a href="#" class="btn btn-sm btn-outline-danger">Cancel Order</a>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Shipping Address</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-1"><strong>{{ $order->first_name }} {{ $order->last_name }}</strong></p>
                            <p class="mb-1">{{ $order->address }}</p>
                            @if ($order->apt)
                                <p class="mb-1">{{ $order->apt }}</p>
                            @endif
                            <p class="mb-1">{{ $order->city }}, {{ $order->state }} {{ $order->postal_code }}</p>
                            <p class="mb-1">{{ $order->country }}</p>
                            <p class="mb-1">{{ $order->phone }}</p>
                            <p class="mb-0">{{ $order->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="order-items-container">
            <h5>Order Items</h5>
            <div class="table-responsive mt-3">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="product-image me-3">
                                            <img src="{{ $item->product->avatar_url }}" alt="{{ $item->product->name }}" 
                                                class="img-fluid rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $item->product->name }}</h6>
                                            <small class="text-muted">
                                                @if ($item->productVariant->color_name)
                                                    Color: {{ $item->productVariant->color_name }}
                                                @endif
                                                @if ($item->productVariant->size)
                                                    | Size: {{ $item->productVariant->size }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>${{ number_format($item->price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td class="text-end">${{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="order-summary mt-4">
            <div class="row">
                <div class="col-md-6 offset-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>${{ number_format($order->items->sum(function($item) { return $item->price * $item->quantity; }), 2) }}</span>
                            </div>
                            
                            @if ($order->coupon)
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Discount ({{ $order->coupon->code }}):</span>
                                    <span>-${{ number_format($order->coupon->calculateDiscount($order->items->sum(function($item) { return $item->price * $item->quantity; })), 2) }}</span>
                                </div>
                            @endif
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span>${{ number_format($order->total_amount - $order->items->sum(function($item) { return $item->price * $item->quantity; }), 2) }}</span>
                            </div>
                            
                            <div class="d-flex justify-content-between pt-2 border-top mt-2">
                                <span class="fw-bold">Total:</span>
                                <span class="fw-bold">${{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    @if ($order->user_notes)
                        <div class="card mt-3">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">Order Notes</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $order->user_notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="order-actions mt-4 text-center">
            <a href="{{ route('user.orders') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Orders
            </a>
            
            @if ($order->status === 'completed')
                {{-- Nút để đánh giá sản phẩm --}}
                @if ($order->getReviewableProducts(Auth::id())->count() > 0)
                    <a href="{{ route('user.reviews.create', $order) }}" class="btn btn-primary ms-2">
                        <i class="fas fa-star me-2"></i>Write a Review
                    </a>
                @endif
                
                <a href="#" class="btn btn-pry ms-2">
                    <i class="fas fa-redo me-2"></i>Buy Again
                </a>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .order-detail-wrapper {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
        }
        
        .card {
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            border-top-left-radius: 8px !important;
            border-top-right-radius: 8px !important;
        }
        
        .product-image img {
            transition: transform 0.3s;
        }
        
        .product-image:hover img {
            transform: scale(1.1);
        }
        
        @media (max-width: 767px) {
            .order-actions .btn {
                display: block;
                width: 100%;
                margin-bottom: 10px;
            }
            
            .order-actions .btn:last-child {
                margin-left: 0 !important;
            }
        }
    </style>
@endpush