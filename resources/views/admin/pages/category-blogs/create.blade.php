@extends('admin.layouts.sidebar')

@section('title', 'Thêm danh mục bài viết')

@section('main-content')
    <div class="category-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.category-blogs.index') }}">Danh mục bài viết</a></li>
                <li class="breadcrumb-item current">Thêm mới</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-folder-plus icon-title"></i>
                    <h5>Thêm danh mục bài viết mới</h5>
                </div>
            </div>

            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.category-blogs.store') }}" method="POST" class="category-form">
                    @csrf

                    <div class="form-group">
                        <label for="name" class="form-label-custom">
                            Tên danh mục <span class="required-mark">*</span>
                        </label>
                        <input type="text" class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}"
                            id="name" name="name" value="{{ old('name') }}">
                        @error('name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="slug" class="form-label-custom">
                            Slug <span class="form-hint">(Để trống để tự động tạo)</span>
                        </label>
                        <input type="text" class="custom-input {{ $errors->has('slug') ? 'input-error' : '' }}"
                            id="slug" name="slug" value="{{ old('slug') }}">
                        @error('slug')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label-custom">
                            Mô tả
                        </label>
                        <textarea class="form-control {{ $errors->has('description') ? 'input-error' : '' }}"
                            id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.category-blogs.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Lưu danh mục
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Auto-generate slug from name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    nameInput.addEventListener('keyup', function() {
        if (!slugInput.value) {
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