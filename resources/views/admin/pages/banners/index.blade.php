@extends('admin.layouts.sidebar')

@section('title', 'Quản lý banner')

@section('main-content')
<div class="category-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item current">Banner</li>
        </ol>
    </div>

    <div class="content-card">
        <div class="card-top">
            <div class="card-title">
                <i class="fas fa-images icon-title"></i>
                <h5>Danh sách banner</h5>
            </div>
            <a href="{{ route('admin.banners.create') }}" class="action-button">
                <i class="fas fa-plus"></i> Thêm banner
            </a>
        </div>
        
        <div class="card-content">
           
            
            @if($banners->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-images"></i>
                    </div>
                    <h4>Chưa có banner nào</h4>
                    <p>Bắt đầu bằng cách thêm banner đầu tiên.</p>
                    <a href="{{ route('admin.banners.create') }}" class="action-button">
                        <i class="fas fa-plus"></i> Thêm banner mới
                    </a>
                </div>
            @else
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="column-small">STT</th>
                                <th class="column-medium">Hình ảnh</th>
                                <th class="column-large">Tiêu đề</th>
                                <th class="column-small">Thứ tự</th>
                                <th class="column-small">Trạng thái</th>
                                <th class="column-small text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($banners as $index => $banner)
                                <tr>
                                    <td class="text-center">{{ ($banners->currentPage() - 1) * $banners->perPage() + $index + 1 }}</td>
                                    <td>
                                        <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="thumbnail-image" style="max-width: 100px; max-height: 60px;">
                                    </td>
                                    <td class="item-name">
                                        {{ $banner->title }}
                                        @if($banner->link)
                                            <div class="item-link text-muted">
                                                <small><i class="fas fa-external-link-alt"></i> {{ $banner->link }}</small>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="order-badge">{{ $banner->sort_order }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($banner->is_active)
                                            <span class="status-badge active">Hiển thị</span>
                                        @else
                                            <span class="status-badge inactive">Ẩn</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons-wrapper">
                                            <a href="{{ route('admin.banners.edit', $banner) }}" class="action-icon edit-icon" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @include('components.delete-form', [
                                                'id' => $banner->id,
                                                'route' => route('admin.banners.destroy', $banner),
                                                'message' => "Bạn có chắc chắn muốn xóa banner '{$banner->title}'?"
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
                        Hiển thị {{ $banners->firstItem() ?? 0 }} đến {{ $banners->lastItem() ?? 0 }} của {{ $banners->total() }} banner
                    </div>
                    <div class="pagination-controls">
                        {{ $banners->links('components.paginate') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection