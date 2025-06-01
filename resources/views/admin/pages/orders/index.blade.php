@extends('admin.layouts.sidebar')

@section('title', 'Quản lý đơn hàng')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Đơn hàng</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-shopping-cart icon-title"></i>
                    <h5>Danh sách đơn hàng</h5>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.orders.index') }}" method="GET" class="filter-form">
                    <div class="filter-group">
                        <div class="filter-item">
                            <label for="order_code">Mã đơn hàng</label>
                            <input type="text" id="order_code" name="order_code" class="filter-input"
                                placeholder="Nhập mã đơn hàng" value="{{ request('order_code') }}">
                        </div>
                        <div class="filter-item">
                            <label for="customer_filter">Khách hàng</label>
                            <input type="text" id="customer_filter" name="customer" class="filter-input"
                                placeholder="Tên hoặc SĐT khách hàng" value="{{ request('customer') }}">
                        </div>
                        <div class="filter-item">
                            <label for="status_filter">Trạng thái</label>
                            <select id="status_filter" name="status" class="filter-input">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận
                                </option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử
                                    lý</option>
                                <option value="shipping" {{ request('status') == 'shipping' ? 'selected' : '' }}>Đang giao
                                    hàng</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn
                                    thành</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy
                                </option>
                            </select>
                        </div>
                        <div class="filter-item">
                            <label for="date_from">Từ ngày</label>
                            <input type="date" id="date_from" name="date_from" class="filter-input"
                                value="{{ request('date_from') }}">
                        </div>
                        <div class="filter-item">
                            <label for="date_to">Đến ngày</label>
                            <input type="date" id="date_to" name="date_to" class="filter-input"
                                value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.orders.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('order_code') || request('customer') || request('status') || request('date_from') || request('date_to'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('order_code'))
                            <span class="filter-tag">
                                <span>Mã đơn hàng: {{ request('order_code') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('order_code')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('customer'))
                            <span class="filter-tag">
                                <span>Khách hàng: {{ request('customer') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('customer')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('status'))
                            <span class="filter-tag">
                                <span>Trạng thái:
                                    @switch(request('status'))
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
                                            {{ request('status') }}
                                    @endswitch
                                </span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('status')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('date_from'))
                            <span class="filter-tag">
                                <span>Từ ngày: {{ date('d/m/Y', strtotime(request('date_from'))) }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('date_from')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('date_to'))
                            <span class="filter-tag">
                                <span>Đến ngày: {{ date('d/m/Y', strtotime(request('date_to'))) }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('date_to')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                    </div>
                @endif

                @if ($orders->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        @if (request('order_code') || request('customer') || request('status') || request('date_from') || request('date_to'))
                            <h4>Không tìm thấy đơn hàng nào</h4>
                            <p>Không có đơn hàng nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.orders.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có đơn hàng nào</h4>
                            <p>Hiện chưa có đơn hàng nào trong hệ thống.</p>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">Mã đơn</th>
                                    <th class="column-medium">Khách hàng</th>
                                    <th class="column-small">Số điện thoại</th>
                                    <th class="column-medium">Tổng tiền</th>
                                    <th class="column-small">Ngày đặt</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td><span class="order-code">{{ $order->id }}</span></td>
                                        <td>
                                            <div class="customer-info">
                                                <span class="customer-name">{{ $order->customer->first_name }}
                                                    {{ $order->customer->last_name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $order->customer->phone }}</td>
                                        <td class="order-total">{{ number_format($order->total) }} ₫</td>
                                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                        <td class="text-center">
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
                                        </td>
                                        <td class="text-center">
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                                    class="action-icon view-icon text-decoration-none"
                                                    title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>

                                            @include('components.delete-form', [
                                                'id' => $order->id,
                                                'route' => route('admin.orders.destroy', $order),
                                                'message' => "Bạn có chắc chắn muốn xóa đơn hàng '{$order->id}'?",
                                            ])
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Hiển thị {{ $orders->firstItem() ?? 0 }} đến {{ $orders->lastItem() ?? 0 }} của
                            {{ $orders->total() }} đơn hàng
                        </div>
                        <div class="pagination-controls">
                            {{ $orders->appends(request()->query())->links('admin.components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Order Specific Styles */
        .order-code {
            font-weight: 600;
            color: #5B3C25;
        }

        .customer-info {
            display: flex;
            flex-direction: column;
        }

        .customer-name {
            font-weight: 500;
        }

        .customer-email {
            font-size: 12px;
            color: #6c757d;
        }

        .order-total {
            font-weight: 600;
            color: #d35400;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-pending {
            background-color: #fff8ec;
            color: #ff9800;
            border: 1px solid #ffe0b2;
        }

        .status-processing {
            background-color: #e3f2fd;
            color: #2196f3;
            border: 1px solid #bbdefb;
        }

        .status-shipping {
            background-color: #e8f5e9;
            color: #4caf50;
            border: 1px solid #c8e6c9;
        }

        .status-completed {
            background-color: #e8f5e9;
            color: #00897b;
            border: 1px solid #b2dfdb;
        }

        .status-cancelled {
            background-color: #fef0f0;
            color: #e53935;
            border: 1px solid #ffcdd2;
        }

        .action-icon.view-icon {
            background-color: #5B3C25;
        }

        .action-icon.view-icon:hover {
            background-color: #4A3020;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Khi thay đổi bộ lọc, tự động submit form
        document.getElementById('status_filter').addEventListener('change', function() {
            document.querySelector('.filter-form').submit();
        });

        // Tự động submit khi chọn ngày
        document.getElementById('date_from').addEventListener('change', function() {
            // Nếu date_to chưa có giá trị và date_from có giá trị mới
            if (!document.getElementById('date_to').value && this.value) {
                // Đặt date_to thành ngày hiện tại
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                document.getElementById('date_to').value = `${year}-${month}-${day}`;
            }
            document.querySelector('.filter-form').submit();
        });

        document.getElementById('date_to').addEventListener('change', function() {
            document.querySelector('.filter-form').submit();
        });
    </script>
@endpush
