@extends('client.layouts.information')

@section('info_title', 'My Orders - ' . request()->getHost())
@section('info_description', 'View and manage your orders')
@section('info_keyword', 'Orders, My Orders, ' . request()->getHost())
@section('info_section_title', 'My Orders')
@section('info_section_desc', 'Track and manage your orders')

@push('breadcrumb')
    @include('components.breadcrumb', [
        'title' => 'My Orders',
        'items' => $breadcrumbItems,
    ])
@endpush

@section('info_content')
    <div class="orders-wrapper">
        @if ($orders->isEmpty())
            <div class="empty-orders text-center py-5">
                <div class="empty-orders-icon mb-4">
                    <i class="fas fa-shopping-bag fa-4x text-muted"></i>
                </div>
                <h4>You haven't placed any orders yet</h4>
                <p class="mb-4">Start shopping to place your first order</p>
                <a href="{{ route('home') }}" class="btn btn-pry">Start Shopping</a>
            </div>
        @else
            <div class="order-count mb-3">
                <p>You have placed <strong>{{ $orders->total() }}</strong> orders</p>
            </div>

            <div class="table-responsive">
                <table class="table table-hover order-table">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Payment Status</th>
                            <th>Order Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{ $order->order_code }}</td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td>${{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->status_payment === 'completed' ? 'success' : ($order->status_payment === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($order->status_payment) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : ($order->status === 'processing' ? 'info' : 'danger')) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('user.orders.detail', $order) }}" class="btn btn-sm btn-pry">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-container mt-4">
                {{ $orders->links('components.paginate') }}
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .orders-wrapper {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
        }

        .empty-orders-icon {
            opacity: 0.5;
        }

        .order-table {
            font-size: 0.95rem;
        }

        .order-table th {
            font-weight: 600;
        }

        .pagination-container .pagination {
            justify-content: center;
        }

        @media (max-width: 767px) {
            .order-table {
                font-size: 0.85rem;
            }
            
            .order-table .btn {
                font-size: 0.75rem;
                padding: 0.2rem 0.5rem;
            }
        }
    </style>
@endpush