@extends('admin.layouts.sidebar')

@section('title', 'Thêm Feature Item mới')

@section('main-content')
<div class="category-form-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.feature-sections.index') }}">Feature Sections</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.feature-sections.items.index', $featureSection) }}">Feature Items</a></li>
            <li class="breadcrumb-item current">Thêm mới</li>
        </ol>
    </div>

    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-plus icon-title"></i>
                <h5>Thêm Feature Item mới cho: {{ $featureSection->title }}</h5>
            </div>
        </div>
        <div class="form-body"> 
            <form action="{{ route('admin.feature-sections.items.store', $featureSection) }}" method="POST" class="category-form" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group">
                    <label for="title" class="form-label-custom">
                        Tiêu đề <span class="required-mark">*</span>
                    </label>
                    <input type="text" class="custom-input {{ $errors->has('title') ? 'input-error' : '' }}" 
                        id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="description" class="form-label-custom">
                        Mô tả
                    </label>
                    <textarea class="custom-input {{ $errors->has('description') ? 'input-error' : '' }}" 
                        id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="icon" class="form-label-custom">
                        Icon <span class="required-mark">*</span>
                    </label>
                    <div class="custom-file-input-wrapper">
                        <input type="file" class="custom-file-input {{ $errors->has('icon') ? 'input-error' : '' }}" 
                            id="icon" name="icon" accept=".svg,.jpg,.jpeg,.png" required>
                        <label for="icon" class="custom-file-label">
                            <i class="fas fa-upload"></i>
                            <span id="file-name">Chọn file</span>
                        </label>
                    </div>
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        <span>Chấp nhận file SVG, JPG, JPEG, PNG. SVG, kích thước 50x50.</span>
                    </div>
                    <div class="icon-preview">
                        <div id="preview-container" style="display: none;">
                            <h6 class="preview-title">Xem trước:</h6>
                            <div id="icon-preview-box"></div>
                        </div>
                    </div>
                    @error('icon')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="sort_order" class="form-label-custom">
                        Thứ tự hiển thị
                    </label>
                    <input type="number" class="custom-input {{ $errors->has('sort_order') ? 'input-error' : '' }}" 
                        id="sort_order" name="sort_order" value="{{ old('sort_order') }}" min="0">
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        <span>Để trống sẽ tự động đặt thứ tự cuối cùng.</span>
                    </div>
                    @error('sort_order')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-actions">
                    <a href="{{ route('admin.feature-sections.items.index', $featureSection) }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="save-button">
                        <i class="fas fa-save"></i> Lưu Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .custom-file-input-wrapper {
        position: relative;
        display: block;
        width: 100%;
    }
    
    .custom-file-input {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 10;
    }
    
    .custom-file-label {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        border: 1px dashed #ced4da;
        border-radius: 4px;
        background-color: #f8f9fa;
        cursor: pointer;
        width: 100%;
        min-height: 45px;
    }
    
    .custom-file-label i {
        margin-right: 10px;
        color: #6c757d;
    }
    
    .icon-preview {
        margin-top: 15px;
    }
    
    .preview-title {
        margin-bottom: 10px;
        font-size: 14px;
        color: #6c757d;
    }
    
    #icon-preview-box {
        width: 100px;
        height: 100px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }
    
    #icon-preview-box img, 
    #icon-preview-box svg {
        max-width: 60px;
        max-height: 60px;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('icon');
        const fileLabel = document.getElementById('file-name');
        const previewContainer = document.getElementById('preview-container');
        const previewBox = document.getElementById('icon-preview-box');
        
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                fileLabel.textContent = file.name;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.style.display = 'block';
                    previewBox.innerHTML = '';
                    
                    if (file.type === 'image/svg+xml') {
                        // Nếu là SVG
                        previewBox.innerHTML = e.target.result;
                    } else {
                        // Nếu là các loại hình ảnh khác
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        previewBox.appendChild(img);
                    }
                };
                
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endpush