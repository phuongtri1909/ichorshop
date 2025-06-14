@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa câu hỏi')

@section('main-content')
<div class="category-form-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.faqs.index') }}">Câu hỏi thường gặp</a></li>
            <li class="breadcrumb-item current">Chỉnh sửa</li>
        </ol>
    </div>

    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-edit icon-title"></i>
                <h5>Chỉnh sửa câu hỏi</h5>
            </div>
            <div class="faq-meta">
                <div class="faq-date">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Tạo: {{ $faq->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="faq-updated">
                    <i class="fas fa-clock"></i>
                    <span>Cập nhật: {{ $faq->updated_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
        <div class="form-body">
            
            <form action="{{ route('admin.faqs.update', $faq) }}" method="POST" class="category-form">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="question" class="form-label-custom">
                        Câu hỏi <span class="required-mark">*</span>
                    </label>
                    <input type="text" class="custom-input {{ $errors->has('question') ? 'input-error' : '' }}" 
                        id="question" name="question" value="{{ old('question', $faq->question) }}" required>
                    @error('question')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="answer" class="form-label-custom">
                        Câu trả lời <span class="required-mark">*</span>
                    </label>
                    <textarea class="custom-input {{ $errors->has('answer') ? 'input-error' : '' }}" 
                        id="answer" name="answer" rows="6" required>{{ old('answer', $faq->answer) }}</textarea>
                    @error('answer')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="order" class="form-label-custom">
                        Thứ tự hiển thị
                    </label>
                    <input type="number" class="custom-input {{ $errors->has('order') ? 'input-error' : '' }}" 
                        id="order" name="order" value="{{ old('order', $faq->order) }}" min="0">
                    @error('order')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-actions">
                    <a href="{{ route('admin.faqs.index') }}" class="back-button">
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

@push('styles')
<style>
    .faq-meta {
        display: flex;
        gap: 20px;
    }
    
    .faq-date, .faq-updated {
        display: flex;
        align-items: center;
        color: #666;
        font-size: 14px;
    }
    
    .faq-date i, .faq-updated i {
        margin-right: 5px;
        color: #888;
    }
</style>
@endpush
