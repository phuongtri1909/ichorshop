@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa Feature Section')

@section('main-content')
<div class="category-form-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.feature-sections.index') }}">Feature Sections</a></li>
            <li class="breadcrumb-item current">Chỉnh sửa</li>
        </ol>
    </div>

    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-edit icon-title"></i>
                <h5>Chỉnh sửa Feature Section</h5>
            </div>
            <div class="section-meta">
                <div class="d-flex">
                    <a href="{{ route('admin.feature-sections.items.index', $featureSection) }}" class="btn btn-sm btn-primary me-2">
                        <i class="fas fa-list"></i> Quản lý Items ({{ $featureSection->items->count() }})
                    </a>
                </div>
            </div>
        </div>
        <div class="form-body">
            <form action="{{ route('admin.feature-sections.update', $featureSection) }}" method="POST" class="category-form">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="title" class="form-label-custom">
                        Tiêu đề <span class="required-mark">*</span>
                    </label>
                    <input type="text" class="custom-input {{ $errors->has('title') ? 'input-error' : '' }}" 
                        id="title" name="title" value="{{ old('title', $featureSection->title) }}" required>
                    @error('title')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="description" class="form-label-custom">
                        Mô tả
                    </label>
                    <textarea class="custom-input {{ $errors->has('description') ? 'input-error' : '' }}" 
                        id="description" name="description" rows="3">{{ old('description', $featureSection->description) }}</textarea>
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
                                id="button_text" name="button_text" value="{{ old('button_text', $featureSection->button_text) }}">
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
                                id="button_link" name="button_link" value="{{ old('button_link', $featureSection->button_link) }}">
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
                    <div class="action-group">
                        <a href="{{ route('admin.feature-sections.items.index', $featureSection) }}" class="btn btn-info">
                            <i class="fas fa-list"></i> Quản lý Items
                        </a>
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
