
@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa danh mục bài viết')

@section('main-content')
    <div class="category-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.category-blogs.index') }}">Danh mục bài viết</a></li>
                <li class="breadcrumb-item current">Chỉnh sửa</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-edit icon-title"></i>
                    <h5>Chỉnh sửa danh mục bài viết</h5>
                </div>
                <div class="category-meta">
                    <div class="category-badge">
                        <i class="fas fa-file-alt"></i>
                        <span>Số bài viết: {{ $categoryBlog->blogs()->count() }}</span>
                    </div>
                    <div class="category-badge">
                        <i class="fas fa-link"></i>
                        <span>{{ $categoryBlog->slug }}</span>
                    </div>
                </div>
            </div>

            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.category-blogs.update', $categoryBlog) }}" method="POST" class="category-form">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name" class="form-label-custom">
                            Tên danh mục <span class="required-mark">*</span>
                        </label>
                        <input type="text" class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}"
                            id="name" name="name" value="{{ old('name', $categoryBlog->name) }}">
                        @error('name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="slug" class="form-label-custom">
                            Slug <span class="form-hint">(Để trống để tự động tạo)</span>
                        </label>
                        <input type="text" class="custom-input {{ $errors->has('slug') ? 'input-error' : '' }}"
                            id="slug" name="slug" value="{{ old('slug', $categoryBlog->slug) }}">
                        @error('slug')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label-custom">
                            Mô tả
                        </label>
                        <textarea class="custom-textarea {{ $errors->has('description') ? 'input-error' : '' }}"
                            id="description" name="description" rows="3">{{ old('description', $categoryBlog->description) }}</textarea>
                        @error('description')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.category-blogs.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        @if($categoryBlog->blogs()->count() > 0)
            <div class="form-card mt-4">
                <div class="form-header">
                    <div class="form-title">
                        <i class="fas fa-file-alt icon-title"></i>
                        <h5>Bài viết trong danh mục này</h5>
                    </div>
                </div>
                
                <div class="form-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tiêu đề</th>
                                    <th>Ngày đăng</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categoryBlog->blogs()->latest()->take(5)->get() as $blog)
                                    <tr>
                                        <td>{{ $blog->title }}</td>
                                        <td>{{ $blog->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            @if($blog->is_active)
                                                <span class="badge bg-success">Hiển thị</span>
                                            @else
                                                <span class="badge bg-secondary">Ẩn</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($categoryBlog->blogs()->count() > 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.blogs.index', ['category_id' => $categoryBlog->id]) }}" class="btn btn-outline-primary">
                                Xem tất cả bài viết
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
    .category-meta {
        margin-top: 10px;
        display: flex;
        flex-wrap: wrap;
    }
    
    .category-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        margin-right: 10px;
        margin-bottom: 5px;
        background-color: #f5f5f5;
    }
    
    .category-badge i {
        margin-right: 5px;
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-generate slug from name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    const originalSlug = "{{ $categoryBlog->slug }}";
    
    // Only auto-generate slug if it's unchanged from the original
    nameInput.addEventListener('keyup', function() {
        if (slugInput.value === originalSlug || !slugInput.value) {
            slugInput.value = createSlug(this.value);
        }
    });

    // Function to create slug
    function createSlug(text) {
        return text
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim()
            .replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-');
    }
</script>
@endpush