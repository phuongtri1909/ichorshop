@extends('admin.layouts.sidebar')

@section('title', 'Thêm Feature Section mới')

@section('main-content')
<div class="category-form-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.feature-sections.index') }}">Feature Sections</a></li>
            <li class="breadcrumb-item current">Thêm mới</li>
        </ol>
    </div>

    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-plus icon-title"></i>
                <h5>Thêm Feature Section mới</h5>
            </div>
        </div>
        <div class="form-body"> 
            <form action="{{ route('admin.feature-sections.store') }}" method="POST" class="category-form">
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
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="button_text" class="form-label-custom">
                                Nội dung nút
                            </label>
                            <input type="text" class="custom-input {{ $errors->has('button_text') ? 'input-error' : '' }}" 
                                id="button_text" name="button_text" value="{{ old('button_text') }}">
                            @error('button_text')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="button_link" class="form-label-custom">
                                Đường dẫn nút
                            </label>
                            <input type="text" class="custom-input {{ $errors->has('button_link') ? 'input-error' : '' }}" 
                                id="button_link" name="button_link" value="{{ old('button_link') }}">
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                <span>Nhập URL đầy đủ hoặc đường dẫn tương đối.</span>
                            </div>
                            @error('button_link')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="{{ route('admin.feature-sections.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="save-button">
                        <i class="fas fa-save"></i> Lưu Section
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
