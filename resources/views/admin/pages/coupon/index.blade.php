@extends('admin.layouts.sidebar')

@section('title', 'Quản lý mã giảm giá')

@section('main-content')
    <div class="coupon-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Mã giảm giá</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-tag icon-title"></i>
                    <h5>Danh sách mã giảm giá</h5>
                </div>
                <a href="{{ route('admin.coupons.create') }}" class="action-button">
                    <i class="fas fa-plus"></i> Thêm mã giảm giá
                </a>
            </div>

            <div class="card-content">
                @if ($coupons->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-tag"></i>
                        </div>
                        <h4>Chưa có mã giảm giá nào</h4>
                        <p>Bắt đầu bằng cách thêm mã giảm giá đầu tiên.</p>
                        <a href="{{ route('admin.coupons.create') }}" class="action-button">
                            <i class="fas fa-plus"></i> Thêm mã giảm giá mới
                        </a>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-medium">Mã giảm giá</th>
                                    <th class="column-small">Loại</th>
                                    <th class="column-small">Giá trị</th>
                                    <th class="column-small">Hiệu lực</th>
                                    <th class="column-small">Đã dùng</th>
                                    <th class="column-small">Giới hạn</th>
                                    <th class="column-small">Trạng thái</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($coupons as $index => $coupon)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($coupons->currentPage() - 1) * $coupons->perPage() + $index + 1 }}</td>
                                        <td class="coupon-code">
                                            <span class="code-badge">{{ $coupon->code }}</span>
                                            <small
                                                class="d-block text-muted mt-1">{{ Str::limit($coupon->description, 40) }}</small>
                                        </td>
                                        <td>
                                            @if ($coupon->type == 'percentage')
                                                <span class="badge-type badge-percentage">%</span>
                                            @else
                                                <span class="badge-type badge-fixed">Cố định</span>
                                            @endif
                                        </td>
                                        <td class="coupon-value">
                                            <span class="discount-value">{{ $coupon->display_value }}</span>
                                        </td>
                                        <td class="coupon-validity">
                                            @if ($coupon->start_date && $coupon->end_date)
                                                <span class="date-badge">
                                                    {{ $coupon->start_date->format('d/m/Y') }} -
                                                    {{ $coupon->end_date->format('d/m/Y') }}
                                                </span>
                                            @elseif($coupon->start_date)
                                                <span class="date-badge">
                                                    Từ {{ $coupon->start_date->format('d/m/Y') }}
                                                </span>
                                            @elseif($coupon->end_date)
                                                <span class="date-badge">
                                                    Đến {{ $coupon->end_date->format('d/m/Y') }}
                                                </span>
                                            @else
                                                <span class="date-badge date-badge-unlimited">Không giới hạn</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="usage-count">{{ $coupon->usage_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if ($coupon->usage_limit)
                                                <span
                                                    class="usage-limit">{{ $coupon->usage_count }}/{{ $coupon->usage_limit }}</span>
                                            @else
                                                <span class="usage-limit">∞</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($coupon->isValid())
                                                <span class="status-badge status-active">Hoạt động</span>
                                            @else
                                                <span class="status-badge status-inactive">Hết hạn</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">

                                                <a href="{{ route('admin.coupons.send.form', $coupon) }}"
                                                    class="action-icon send-icon text-decoration-none bg-primary"
                                                    title="Gửi mã giảm giá">
                                                    <i class="fas fa-paper-plane"></i>
                                                </a>

                                                <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                                    class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @include('components.delete-form', [
                                                    'id' => $coupon->id,
                                                    'route' => route('admin.coupons.destroy', $coupon),
                                                    'message' => "Bạn có chắc chắn muốn xóa mã giảm giá '{$coupon->code}'?",
                                                ])
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Hiển thị {{ $coupons->firstItem() ?? 0 }} đến {{ $coupons->lastItem() ?? 0 }} của
                            {{ $coupons->total() }} mã giảm giá
                        </div>
                        <div class="pagination-controls">
                            {{ $coupons->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .code-badge {
            display: inline-block;
            background-color: #eeeef7;
            padding: 6px 10px;
            border-radius: 4px;
            font-family: monospace;
            font-weight: 600;
            font-size: 14px;
        }

        .badge-type {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-percentage {
            background-color: #e1f5fe;
            color: #0288d1;
        }

        .badge-fixed {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }

        .discount-value {
            font-weight: 600;
            font-size: 15px;
            color: #D1A66E;
        }

        .date-badge {
            display: inline-block;
            background-color: #f8f9fa;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 12px;
            color: #666;
        }

        .date-badge-unlimited {
            background-color: #e8f5e9;
            color: #388e3c;
        }

        .usage-count,
        .usage-limit {
            font-weight: 500;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-active {
            background-color: #e8f5e9;
            color: #388e3c;
        }

        .status-inactive {
            background-color: #ffebee;
            color: #d32f2f;
        }
    </style>
@endpush
