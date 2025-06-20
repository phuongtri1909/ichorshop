@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa đánh giá')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.reviews.index') }}">Đánh giá</a></li>
                <li class="breadcrumb-item current">Chỉnh sửa đánh giá</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-edit icon-title"></i>
                    <h5>Chỉnh sửa đánh giá</h5>
                </div>
                <a href="{{ route('admin.reviews.index') }}" class="btn-link">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>

            <div class="card-content">
                <!-- Review Info -->
                <div class="review-info mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="info-label">Người đánh giá:</label>
                                <div class="info-value">{{ $review->user->first_name }} {{ $review->user->last_name }}</div>
                            </div>
                            <div class="info-group">
                                <label class="info-label">Email:</label>
                                <div class="info-value">{{ $review->user->email }}</div>
                            </div>
                            <div class="info-group">
                                <label class="info-label">Đơn hàng:</label>
                                <div class="info-value">#{{ $review->order->order_code }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="info-label">Ngày đánh giá:</label>
                                <div class="info-value">{{ $review->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="info-group">
                                <label class="info-label">Cập nhật gần đây:</label>
                                <div class="info-value">{{ $review->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('admin.reviews.update', $review) }}" method="POST" class="form">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="product_id" class="form-label required">Sản phẩm</label>
                        <select id="product_id" name="product_id" class="form-control @error('product_id') is-invalid @enderror" required>
                            <option value="">-- Chọn sản phẩm --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id', $review->product_id) == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Đánh giá</label>
                        <div class="rating-selector">
                            @for ($i = 5; $i >= 1; $i--)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="rating" id="rating{{ $i }}"
                                        value="{{ $i }}" {{ old('rating', $review->rating) == $i ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="rating{{ $i }}">
                                        {{ $i }} <i class="fas fa-star text-warning"></i>
                                    </label>
                                </div>
                            @endfor
                        </div>
                        @error('rating')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="content" class="form-label required">Nội dung đánh giá</label>
                        <textarea id="content" name="content" rows="5"
                            class="form-control @error('content') is-invalid @enderror"
                            required>{{ old('content', $review->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label required">Trạng thái</label>
                        <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="published" {{ old('status', $review->status) == 'published' ? 'selected' : '' }}>
                                Đã xuất bản
                            </option>
                            <option value="pending" {{ old('status', $review->status) == 'pending' ? 'selected' : '' }}>
                                Chờ duyệt
                            </option>
                            <option value="rejected" {{ old('status', $review->status) == 'rejected' ? 'selected' : '' }}>
                                Đã từ chối
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-pry">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .review-info {
        background-color: #f9f9f9;
        border-radius: 8px;
        padding: 15px;
    }
    
    .info-group {
        margin-bottom: 10px;
    }
    
    .info-label {
        font-weight: 600;
        color: #555;
        display: inline-block;
        margin-right: 5px;
    }
    
    .info-value {
        display: inline-block;
        color: #333;
    }
    
    .rating-selector {
        display: flex;
        gap: 15px;
        margin-bottom: 10px;
    }
    
    .form-check-label {
        cursor: pointer;
    }
</style>
@endpush