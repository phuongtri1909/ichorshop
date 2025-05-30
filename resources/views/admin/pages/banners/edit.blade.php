@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa banner')

@section('main-content')
<div class="category-form-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">Banner</a></li>
            <li class="breadcrumb-item current">Chỉnh sửa</li>
        </ol>
    </div>

    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-edit icon-title"></i>
                <h5>Chỉnh sửa banner</h5>
            </div>
            <div class="category-meta">
                <div class="category-created">
                    <i class="fas fa-clock"></i>
                    <span>Ngày tạo: {{ $banner->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
        <div class="form-body">
            
            <form action="{{ route('admin.banners.update', $banner) }}" method="POST" class="category-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="title" class="form-label-custom">
                        Tiêu đề <span class="required-mark">*</span>
                    </label>
                    <input type="text" class="custom-input {{ $errors->has('title') ? 'input-error' : '' }}" 
                        id="title" name="title" value="{{ old('title', $banner->title) }}" required>
                    @error('title')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="description" class="form-label-custom">Mô tả</label>
                    <textarea class="custom-input {{ $errors->has('description') ? 'input-error' : '' }}" 
                        id="description" name="description" rows="3">{{ old('description', $banner->description) }}</textarea>
                    @error('description')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="image" class="form-label-custom">
                        Hình ảnh
                    </label>
                    @if($banner->image)
                        <div class="current-image mb-3">
                            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" style="max-width: 300px; max-height: 200px;">
                        </div>
                    @endif
                    <input type="file" class="custom-input {{ $errors->has('image') ? 'input-error' : '' }}" 
                        id="image" name="image" accept="image/*">
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        <span>Để trống nếu không muốn thay đổi hình ảnh. Chấp nhận định dạng: JPG, PNG, GIF. Tối đa 2MB.</span>
                    </div>
                    @error('image')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="link" class="form-label-custom">Đường dẫn liên kết</label>
                    <input type="text" class="custom-input {{ $errors->has('link') ? 'input-error' : '' }}" 
                        id="link" name="link" value="{{ old('link', $banner->link) }}">
                    @error('link')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="sort_order" class="form-label-custom">Thứ tự hiển thị</label>
                    <input type="number" class="custom-input {{ $errors->has('sort_order') ? 'input-error' : '' }}" 
                        id="sort_order" name="sort_order" value="{{ old('sort_order', $banner->sort_order) }}" min="0">
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        <span>Số nhỏ hơn sẽ hiển thị trước.</span>
                    </div>
                    @error('sort_order')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_active" name="is_active" value="1" 
                            {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                        <label for="is_active">Hiển thị banner</label>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="{{ route('admin.banners.index') }}" class="back-button">
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