@extends('admin.layouts.sidebar')

@section('title', 'Quản lý khuyến mãi')

@section('main-content')
<div class="category-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item current">Khuyến mãi</li>
        </ol>
    </div>

    <div class="content-card">
        <div class="card-top">
            <div class="card-title">
                <i class="fas fa-percent icon-title"></i>
                <h5>Danh sách khuyến mãi</h5>
            </div>
            <a href="{{ route('admin.promotions.create') }}" class="action-button">
                <i class="fas fa-plus"></i> Thêm khuyến mãi
            </a>
        </div>
        
        <div class="card-content">
            @if($promotions->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-percent"></i>
                    </div>
                    <h4>Chưa có khuyến mãi nào</h4>
                    <p>Bắt đầu bằng cách thêm chương trình khuyến mãi đầu tiên.</p>
                    <a href="{{ route('admin.promotions.create') }}" class="action-button">
                        <i class="fas fa-plus"></i> Thêm khuyến mãi mới
                    </a>
                </div>
            @else
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="column-small">STT</th>
                                <th class="column-large">Tên khuyến mãi</th>
                                <th class="column-medium">Loại giảm giá</th>
                                <th class="column-medium">Giá trị</th>
                                <th class="column-medium">Thời gian</th>
                                <th class="column-small">Trạng thái</th>
                                <th class="column-small text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($promotions as $index => $promotion)
                                <tr>
                                    <td class="text-center">{{ ($promotions->currentPage() - 1) * $promotions->perPage() + $index + 1 }}</td>
                                    <td class="item-name">{{ $promotion->name }}</td>
                                    <td class="promotion-type">
                                        <span class="type-badge {{ $promotion->discount_type }}">
                                            {{ $promotion->discount_type == 'percentage' ? 'Phần trăm' : 'Số tiền cố định' }}
                                        </span>
                                    </td>
                                    <td class="promotion-value">
                                        @if($promotion->discount_type == 'percentage')
                                            {{ $promotion->discount_value }}%
                                        @else
                                            {{ number_format($promotion->discount_value, 0, ',', '.') }}đ
                                        @endif
                                    </td>
                                    <td class="promotion-duration">
                                        <div class="date-range">
                                            <span class="start-date">{{ $promotion->start_date->format('d/m/Y') }}</span>
                                            <i class="fas fa-arrow-right"></i>
                                            <span class="end-date">{{ $promotion->end_date->format('d/m/Y') }}</span>
                                        </div>
                                    </td>
                                    <td class="promotion-status">
                                        @php
                                            $now = now();
                                            $status = 'expired';
                                            $statusText = 'Hết hạn';
                                            
                                            if ($promotion->is_active) {
                                                if ($now->between($promotion->start_date, $promotion->end_date)) {
                                                    $status = 'active';
                                                    $statusText = 'Đang hoạt động';
                                                } elseif ($now < $promotion->start_date) {
                                                    $status = 'upcoming';
                                                    $statusText = 'Sắp diễn ra';
                                                }
                                            } else {
                                                $status = 'inactive';
                                                $statusText = 'Tạm dừng';
                                            }
                                        @endphp
                                        <span class="status-badge {{ $status }}">{{ $statusText }}</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons-wrapper">
                                            <a href="{{ route('admin.promotions.edit', $promotion) }}" class="action-icon edit-icon" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @include('components.delete-form', [
                                                'id' => $promotion->id,
                                                'route' => route('admin.promotions.destroy', $promotion),
                                                'message' => "Bạn có chắc chắn muốn xóa khuyến mãi '{$promotion->name}'?"
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
                        Hiển thị {{ $promotions->firstItem() ?? 0 }} đến {{ $promotions->lastItem() ?? 0 }} của {{ $promotions->total() }} khuyến mãi
                    </div>
                    <div class="pagination-controls">
                        {{ $promotions->links('components.paginate') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .type-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .type-badge.percentage {
        background-color: #e3f2fd;
        color: #1976d2;
    }
    
    .type-badge.fixed {
        background-color: #f3e5f5;
        color: #7b1fa2;
    }
    
    .date-range {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
    }
    
    .date-range i {
        color: #999;
        font-size: 10px;
    }
    
    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .status-badge.active {
        background-color: #d4edda;
        color: #155724;
    }
    
    .status-badge.upcoming {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .status-badge.expired {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .status-badge.inactive {
        background-color: #e2e3e5;
        color: #6c757d;
    }
    
    .promotion-value {
        font-weight: 600;
        color: #D1A66E;
    }
</style>
@endpush