@extends('admin.layouts.sidebar')

@section('title', 'Quản lý danh mục')

@section('main-content')
<div class="category-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item current">Danh mục</li>
        </ol>
    </div>

    <div class="content-card">
        <div class="card-top">
            <div class="card-title">
                <i class="fas fa-list-alt icon-title"></i>
                <h5>Danh sách danh mục</h5>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="action-button">
                <i class="fas fa-plus"></i> Thêm danh mục
            </a>
        </div>
        
        <div class="card-content">
           
            
            @if($categories->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <h4>Chưa có danh mục nào</h4>
                    <p>Bắt đầu bằng cách thêm danh mục đầu tiên.</p>
                    <a href="{{ route('admin.categories.create') }}" class="action-button">
                        <i class="fas fa-plus"></i> Thêm danh mục mới
                    </a>
                </div>
            @else
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="column-small">STT</th>
                                <th class="column-large">Tên danh mục</th>
                                <th class="column-medium">Slug</th>
                                <th class="column-small">Thứ tự</th>
                                <th class="column-small text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $index => $category)
                                <tr>
                                    <td class="text-center">{{ ($categories->currentPage() - 1) * $categories->perPage() + $index + 1 }}</td>
                                    <td class="item-name">{{ $category->name }}</td>
                                    <td class="item-slug">{{ $category->slug }}</td>
                                    <td class="text-center">
                                        <span class="order-badge">{{ $category->sort_order }}</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons-wrapper">
                                            <a href="{{ route('admin.categories.edit', $category) }}" class="action-icon edit-icon" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @include('components.delete-form', [
                                                'id' => $category->id,
                                                'route' => route('admin.categories.destroy', $category),
                                                'message' => "Bạn có chắc chắn muốn xóa danh mục '{$category->name}'?"
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
                        Hiển thị {{ $categories->firstItem() ?? 0 }} đến {{ $categories->lastItem() ?? 0 }} của {{ $categories->total() }} danh mục
                    </div>
                    <div class="pagination-controls">
                        {{ $categories->links('admin.components.paginate') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection