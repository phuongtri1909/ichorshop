@extends('admin.layouts.sidebar')

@section('title', 'Quản lý danh mục blog')

@section('main-content')
<div class="category-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item current">Danh mục blog</li>
        </ol>
    </div>

    <div class="content-card">
        <div class="card-top">
            <div class="card-title">
                <i class="fas fa-folder icon-title"></i>
                <h5>Danh sách danh mục blog</h5>
            </div>
            <a href="{{ route('admin.category-blogs.create') }}" class="action-button">
                <i class="fas fa-plus"></i> Thêm danh mục
            </a>
        </div>
        
        <div class="card-content">
            @if($categories->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-folder"></i>
                    </div>
                    <h4>Chưa có danh mục nào</h4>
                    <p>Bắt đầu bằng cách thêm danh mục blog đầu tiên.</p>
                    <a href="{{ route('admin.category-blogs.create') }}" class="action-button">
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
                                <th class="column-large">Mô tả</th>
                                <th class="column-small">Số bài viết</th>
                                <th class="column-small text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $index => $category)
                                <tr>
                                    <td class="text-center">{{ ($categories->currentPage() - 1) * $categories->perPage() + $index + 1 }}</td>
                                    <td class="item-name">{{ $category->name }}</td>
                                    <td class="item-slug">{{ $category->slug }}</td>
                                    <td class="item-description">{{ Str::limit($category->description, 80) }}</td>
                                    <td class="category-blogs">
                                        <span class="blog-count">{{ $category->blogs_count }}</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons-wrapper">
                                            <a href="{{ route('admin.category-blogs.edit', $category) }}" class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @include('components.delete-form', [
                                                'id' => $category->id,
                                                'route' => route('admin.category-blogs.destroy', $category),
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
                        {{ $categories->links('components.paginate') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .blog-count {
        display: inline-block;
        background: #e3f2fd;
        color: #1976d2;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        min-width: 24px;
        text-align: center;
    }
    
    .category-blogs {
        text-align: center;
    }
</style>
@endpush