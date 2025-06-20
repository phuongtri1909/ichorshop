@extends('client.layouts.information')

@section('info_title', 'Order Confirmation - ' . request()->getHost())
@section('info_description', 'Your order has been placed successfully')
@section('info_keyword', 'Order Confirmation, Thank You, ' . request()->getHost())
@section('info_section_title', 'Order Confirmation')
@section('info_section_desc', 'Thank you for your purchase!')

@push('breadcrumb')
    @include('components.breadcrumb', [
        'title' => 'Order Confirmation',
        'items' => [
            ['title' => 'Home', 'url' => route('home')],
            ['title' => 'My Cart', 'url' => route('user.cart.index')],
            ['title' => 'Order Confirmation', 'url' => '#'],
        ],
    ])
@endpush

@section('info_content')
    <div class="success-container text-center">
        <div class="success-icon mb-4">
            <i class="fas fa-check-circle fa-4x text-success"></i>
        </div>
        
        <h2 class="mb-3">Your Order Has Been Placed!</h2>
        <p class="lead mb-5">Thank you for your purchase. Your order number is <strong>{{ $order->order_code }}</strong></p>
        
        <div class="order-details-card mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h4 class="mb-0">Order Details</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 text-start">
                            <h5 class="text-muted">Shipping Information</h5>
                            <p>
                                {{ $order->first_name }} {{ $order->last_name }}<br>
                                {{ $order->address }}<br>
                                @if($order->apt)
                                    {{ $order->apt }}<br>
                                @endif
                                {{ $order->city }}, {{ $order->state }}<br>
                                {{ $order->country }}, {{ $order->postal_code }}<br>
                                <strong>Email:</strong> {{ $order->email }}<br>
                                <strong>Phone:</strong> {{ $order->phone }}
                            </p>
                        </div>
                        <div class="col-md-6 text-start">
                            <h5 class="text-muted">Order Summary</h5>
                            <p>
                                <strong>Order Number:</strong> {{ $order->order_code }}<br>
                                <strong>Order Date:</strong> {{ $order->created_at->format('F d, Y') }}<br>
                                <strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}<br>
                                <strong>Order Status:</strong> <span class="badge bg-info">{{ ucfirst($order->status) }}</span><br>
                                <strong>Payment Status:</strong> <span class="badge bg-warning">{{ ucfirst($order->status_payment) }}</span>
                            </p>
                        </div>
                    </div>
                    
                    <table class="table table-hover mt-4">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th class="text-end">Price</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $item->product->avatar_url }}" alt="{{ $item->product->name }}" class="me-3" width="50" height="50">
                                            <div>
                                                {{ $item->product->name }}
                                                <div class="small text-muted">
                                                    @if($item->productVariant)
                                                        @if($item->productVariant->color_name)
                                                            {{ $item->productVariant->color_name }}
                                                        @endif
                                                        @if($item->productVariant->size)
                                                            / Size: {{ $item->productVariant->size }}
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">${{ number_format($item->price, 2) }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">${{ number_format($item->price * $item->quantity, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end"><strong>Subtotal</strong></td>
                                <td class="text-end">${{ number_format($order->total_amount - 5.00 - ($order->total_amount * 0.1), 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end">Shipping</td>
                                <td class="text-end">$5.00</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end">Tax (10%)</td>
                                <td class="text-end">${{ number_format($order->total_amount * 0.1, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total</strong></td>
                                <td class="text-end"><strong>${{ number_format($order->total_amount, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="next-steps mb-5">
            <p class="mb-4">We'll send you a confirmation email with your order details and tracking information.</p>
            <div class="row justify-content-center">
                <div class="col-md-4 mb-3">
                    <a href="{{ route('user.orders') }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-clipboard-list me-2"></i>View My Orders
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ route('home') }}" class="btn btn-pry w-100">
                        <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>
        </div>
        
        <div class="customer-support text-muted">
            <p>Need help with your order? <a href="#" class="color-primary">Contact our support team</a>.</p>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .success-container {
            background-color: #fff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .success-icon {
            color: #28a745;
        }
        
        .order-details-card .card {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table img {
            border-radius: 4px;
        }
    </style>
@endpush