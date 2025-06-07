@extends('admin.layouts.sidebar')

@section('title', 'Quản lý thương hiệu')

@section('main-content')
<div class="category-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item current">Thương hiệu</li>
        </ol>
    </div>

    <div class="content-card">
        <div class="card-top">
            <div class="card-title">
                <i class="fas fa-tags icon-title"></i>
                <h5>Danh sách thương hiệu</h5>
            </div>
            <a href="{{ route('admin.brands.create') }}" class="action-button">
                <i class="fas fa-plus"></i> Thêm thương hiệu
            </a>
        </div>
        
        <div class="card-content">
            @if($brands->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h4>Chưa có thương hiệu nào</h4>
                    <p>Bắt đầu bằng cách thêm thương hiệu đầu tiên.</p>
                    <a href="{{ route('admin.brands.create') }}" class="action-button">
                        <i class="fas fa-plus"></i> Thêm thương hiệu mới
                    </a>
                </div>
            @else
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="column-small">STT</th>
                                <th class="column-small">Logo</th>
                                <th class="column-large">Tên thương hiệu</th>
                                <th class="column-large">Mô tả</th>
                                <th class="column-small text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($brands as $index => $brand)
                                <tr>
                                    <td class="text-center">{{ ($brands->currentPage() - 1) * $brands->perPage() + $index + 1 }}</td>
                                    <td class="brand-logo">
                                        @if($brand->logo)
                                            <img src="{{ asset('storage/'.$brand->logo) }}" alt="{{ $brand->name }}" class="thumbnail">
                                        @else
                                            <div class="no-image">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="item-name">{{ $brand->name }}</td>
                                    <td class="item-description">{{ Str::limit($brand->description, 80) }}</td>
                                    <td>
                                        <div class="action-buttons-wrapper">
                                            <a href="{{ route('admin.brands.edit', $brand) }}" class="action-icon edit-icon" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @include('components.delete-form', [
                                                'id' => $brand->id,
                                                'route' => route('admin.brands.destroy', $brand),
                                                'message' => "Bạn có chắc chắn muốn xóa thương hiệu '{$brand->name}'?"
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
                        Hiển thị {{ $brands->firstItem() ?? 0 }} đến {{ $brands->lastItem() ?? 0 }} của {{ $brands->total() }} thương hiệu
                    </div>
                    <div class="pagination-controls">
                        {{ $brands->links('components.paginate') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .thumbnail {
        width: 60px;
        height: 60px;
        object-fit: scale-down;
        border-radius: 6px;
        border: 1px solid #eee;
    }
    
    .no-image {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        border: 1px solid #eee;
        border-radius: 6px;
        color: #999;
    }
</style>
@endpush