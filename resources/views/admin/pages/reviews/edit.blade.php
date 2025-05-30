@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa đánh giá')

@section('main-content')
<div class="category-form-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.reviews.index') }}">Đánh giá</a></li>
            <li class="breadcrumb-item current">Chỉnh sửa</li>
        </ol>
    </div>

    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-edit icon-title"></i>
                <h5>Chỉnh sửa đánh giá</h5>
            </div>
            <div class="review-meta">
                <div class="review-product">
                    <i class="fas fa-coffee"></i>
                    <span>{{ $review->product->name }}</span>
                </div>
                <div class="review-date">
                    <i class="fas fa-calendar-alt"></i>
                    <span>{{ $review->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
        <div class="form-body">
            
            <form action="{{ route('admin.reviews.update', $review) }}" method="POST" class="category-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="product_id" class="form-label-custom">
                        Sản phẩm <span class="required-mark">*</span>
                    </label>
                    <select class="custom-input {{ $errors->has('product_id') ? 'input-error' : '' }}" 
                        id="product_id" name="product_id" required>
                        <option value="">-- Chọn sản phẩm --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id', $review->product_id) == $product->id ? 'selected' : '' }}>
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
                        id="user_name" name="user_name" value="{{ old('user_name', $review->user_name) }}" required>
                    @error('user_name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="avatar" class="form-label-custom">Ảnh đại diện</label>
                    <div class="avatar-upload-container">
                        <div class="avatar-preview" id="avatarPreview" 
                             style="{{ $review->avatar ? 'background-image: url(' . asset('storage/' . $review->avatar) . ')' : '' }}"
                             class="{{ $review->avatar ? 'has-image' : '' }}">
                            @if(!$review->avatar)
                                <i class="fas fa-user upload-icon"></i>
                            @endif
                        </div>
                        <input type="file" name="avatar" id="avatar-upload" 
                            class="avatar-upload-input" accept="image/*">
                    </div>
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        <span>Kích thước đề xuất: 100x100px, tối đa 1MB. Để trống nếu không muốn thay đổi.</span>
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
                                        {{ old('rating', $review->rating) == $i ? 'checked' : '' }}>
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
                        id="comment" name="comment" rows="4" required>{{ old('comment', $review->comment) }}</textarea>
                    @error('comment')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-actions">
                    <a href="{{ route('admin.reviews.index') }}" class="back-button">
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
                    avatarPreview.innerHTML = ''; // Clear any inner content
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