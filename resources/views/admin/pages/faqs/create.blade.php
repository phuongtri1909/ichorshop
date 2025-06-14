@extends('admin.layouts.sidebar')

@section('title', 'Thêm câu hỏi mới')

@section('main-content')
<div class="category-form-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.faqs.index') }}">Câu hỏi thường gặp</a></li>
            <li class="breadcrumb-item current">Thêm mới</li>
        </ol>
    </div>

    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-plus icon-title"></i>
                <h5>Thêm câu hỏi mới</h5>
            </div>
        </div>
        <div class="form-body"> 
            <form action="{{ route('admin.faqs.store') }}" method="POST" class="category-form">
                @csrf
                
                <div class="form-group">
                    <label for="question" class="form-label-custom">
                        Câu hỏi <span class="required-mark">*</span>
                    </label>
                    <input type="text" class="custom-input {{ $errors->has('question') ? 'input-error' : '' }}" 
                        id="question" name="question" value="{{ old('question') }}" required>
                    @error('question')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="answer" class="form-label-custom">
                        Câu trả lời <span class="required-mark">*</span>
                    </label>
                    <textarea class="custom-input {{ $errors->has('answer') ? 'input-error' : '' }}" 
                        id="answer" name="answer" rows="6" required>{{ old('answer') }}</textarea>
                    @error('answer')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="order" class="form-label-custom">
                        Thứ tự hiển thị
                    </label>
                    <input type="number" class="custom-input {{ $errors->has('order') ? 'input-error' : '' }}" 
                        id="order" name="order" value="{{ old('order') }}" min="0">
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        <span>Để trống sẽ tự động đặt thứ tự cuối cùng.</span>
                    </div>
                    @error('order')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-actions">
                    <a href="{{ route('admin.faqs.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="save-button">
                        <i class="fas fa-save"></i> Lưu câu hỏi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
