@extends('admin.layouts.sidebar')

@section('title', 'Thêm đánh giá mới')

@section('main-content')
<div class="category-form-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.reviews.index') }}">Đánh giá</a></li>
            <li class="breadcrumb-item current">Thêm mới</li>
        </ol>
    </div>

    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-plus icon-title"></i>
                <h5>Thêm đánh giá mới</h5>
            </div>
        </div>
        <div class="form-body"> 
            <form action="{{ route('admin.reviews.store') }}" method="POST" class="category-form" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group">
                    <label for="product_id" class="form-label-custom">
                        Sản phẩm <span class="required-mark">*</span>
                    </label>
                    <select class="custom-input {{ $errors->has('product_id') ? 'input-error' : '' }}" 
                        id="product_id" name="product_id" required>
                        <option value="">-- Chọn sản phẩm --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="user_name" class="form-label-custom">
                        Tên người đánh giá <span class="required-mark">*</span>
                    </label>
                    <input type="text" class="custom-input {{ $errors->has('user_name') ? 'input-error' : '' }}" 
                        id="user_name" name="user_name" value="{{ old('user_name') }}" required>
                    @error('user_name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="avatar" class="form-label-custom">Ảnh đại diện</label>
                    <div class="avatar-upload-container">
                        <div class="avatar-preview" id="avatarPreview">
                            <i class="fas fa-user upload-icon"></i>
                        </div>
                        <input type="file" name="avatar" id="avatar-upload" 
                            class="avatar-upload-input" accept="image/*">
                    </div>
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        <span>Kích thước đề xuất: 100x100px, tối đa 1MB.</span>
                    </div>
                    @error('avatar')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label-custom">
                        Đánh giá <span class="required-mark">*</span>
                    </label>
                    <div class="rating-input-container">
                        <div class="rating-stars">
                            @for($i = 5; $i >= 1; $i--)
                                <div class="rating-option">
                                    <input type="radio" id="rating-{{ $i }}" name="rating" value="{{ $i }}" 
                                        {{ old('rating', 5) == $i ? 'checked' : '' }}>
                                    <label for="rating-{{ $i }}">
                                        <i class="fas fa-star"></i>
                                        <span>{{ $i }}</span>
                                    </label>
                                </div>
                            @endfor
                        </div>
                    </div>
                    @error('rating')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="comment" class="form-label-custom">
                        Nội dung đánh giá <span class="required-mark">*</span>
                    </label>
                    <textarea class="custom-input {{ $errors->has('comment') ? 'input-error' : '' }}" 
                        id="comment" name="comment" rows="4" required>{{ old('comment') }}</textarea>
                    @error('comment')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-actions">
                    <a href="{{ route('admin.reviews.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="save-button">
                        <i class="fas fa-save"></i> Lưu đánh giá
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Avatar preview
        const avatarUpload = document.getElementById('avatar-upload');
        const avatarPreview = document.getElementById('avatarPreview');
        
        avatarUpload.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    avatarPreview.style.backgroundImage = `url('${e.target.result}')`;
                    avatarPreview.classList.add('has-image');
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
        
        // Click on preview to trigger file input
        avatarPreview.addEventListener('click', function() {
            avatarUpload.click();
        });
    });
</script>
@endpush