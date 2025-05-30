@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa danh mục')

@section('main-content')
<div class="category-form-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Danh mục</a></li>
            <li class="breadcrumb-item current">Chỉnh sửa</li>
        </ol>
    </div>

    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-edit icon-title"></i>
                <h5>Chỉnh sửa danh mục</h5>
            </div>
            <div class="category-meta">
                <div class="category-slug">
                    <i class="fas fa-link"></i>
                    <span>{{ $category->slug }}</span>
                </div>
                @if($category->products->count() > 0)
                <div class="category-products">
                    <i class="fas fa-coffee"></i>
                    <span>{{ $category->products->count() }} sản phẩm</span>
                </div>
                @endif
            </div>
        </div>
        <div class="form-body">
            
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="category-form">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="name" class="form-label-custom">
                        Tên danh mục <span class="required-mark">*</span>
                    </label>
                    <input type="text" class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}" 
                        id="name" name="name" value="{{ old('name', $category->name) }}" required>
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="sort_order" class="form-label-custom">Thứ tự hiển thị</label>
                    <input type="number" class="custom-input {{ $errors->has('sort_order') ? 'input-error' : '' }}" 
                        id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0">
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        <span>Số nhỏ hơn sẽ hiển thị trước.</span>
                    </div>
                    @error('sort_order')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-actions">
                    <a href="{{ route('admin.categories.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                   
                    <div class="action-group">
                       
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Cập nhật
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection