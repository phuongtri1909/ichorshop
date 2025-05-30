@extends('admin.layouts.sidebar')

@section('title', 'Thêm tin tức mới')

@section('main-content')
    <div class="category-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.news.index') }}">Tin tức</a></li>
                <li class="breadcrumb-item current">Thêm mới</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-plus icon-title"></i>
                    <h5>Thêm tin tức mới</h5>
                </div>
            </div>

            <div class="form-body">
               

                <form action="{{ route('admin.news.store') }}" method="POST" class="news-form"
                    enctype="multipart/form-data" id="news-form">
                    @csrf

                    <div class="form-tabs">
                        <div class="form-group">
                            <label for="title" class="form-label-custom">
                                Tiêu đề <span class="required-mark">*</span>
                            </label>
                            <input type="text" class="custom-input {{ $errors->has('title') ? 'input-error' : '' }}"
                                id="title" name="title" value="{{ old('title') }}">
                            <div class="error-message" id="error-title">
                                @error('title')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label-custom">
                                Ảnh đại diện <span class="required-mark">*</span>
                            </label>
                            <div class="image-upload-container news-image-upload-container">
                                <div class="image-preview news-image-preview" id="thumbnailPreview">
                                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                    <span class="upload-text">Chọn ảnh</span>
                                </div>
                                <input type="file" name="avatar" id="thumbnail-upload" class="image-upload-input"
                                    accept="image/*">
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                <span>Kích thước đề xuất: 800x600px.</span>
                            </div>
                            <div class="error-message" id="error-avatar">
                                @error('avatar')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-check-group">
                            <div class="custom-checkbox">
                                <input type="checkbox" id="is_active" name="is_active" value="1"
                                    {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label for="is_active" class="form-label-custom mb-0">
                                    Hiển thị tin tức
                                </label>
                            </div>
                            
                            <div class="custom-checkbox mt-2">
                                <input type="checkbox" id="is_featured" name="is_featured" value="1"
                                    {{ old('is_featured', '0') ? 'checked' : '' }}>
                                <label for="is_featured" class="form-label-custom mb-0">
                                    <i class="fas fa-star text-warning"></i> Bài viết nổi bật
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="content" class="form-label-custom">
                                Nội dung <span class="required-mark">*</span>
                            </label>
                            <textarea id="content" name="content" class="editor">{{ old('content') }}</textarea>
                            <div class="error-message" id="error-content">
                                @error('content')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.news.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Lưu tin tức
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            CKEDITOR.replace('content', {
                on: {
                    change: function(evt) {
                        this.updateElement();
                    }
                },
                height: 200,
                // Enable image resizing
                extraPlugins: 'image2,uploadimage',
                // Don't remove image2 plugin as it's needed for resizing
                removePlugins: 'uploadfile,filebrowser',
                image2_alignClasses: ['image-left', 'image-center', 'image-right'],
                image2_disableResizer: false,
            });

            // Image upload preview
            $('#thumbnail-upload').change(function() {
                previewImage(this);
            });

            function previewImage(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        $('#thumbnailPreview').css('background-image', `url('${e.target.result}')`);
                        $('#thumbnailPreview').addClass('has-image');
                        
                        // Add remove button if not exists
                        if ($('#removeImage').length === 0) {
                            $('#thumbnailPreview').append('<span class="remove-image-btn" id="removeImage" title="Xóa ảnh"><i class="fas fa-times"></i></span>');
                            bindRemoveEvent();
                        }
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }
            
            // Remove image functionality
            function bindRemoveEvent() {
                $('#removeImage').click(function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                    
                    // Clear image
                    $('#thumbnailPreview').css('background-image', '');
                    $('#thumbnailPreview').removeClass('has-image');
                    
                    // Reset file input
                    $('#thumbnail-upload').val('');
                    
                    // Remove the button
                    $(this).remove();
                });
            }
        });
    </script>
@endpush