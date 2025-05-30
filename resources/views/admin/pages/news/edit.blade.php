@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa tin tức')

@section('main-content')
    <div class="category-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.news.index') }}">Tin tức</a></li>
                <li class="breadcrumb-item current">Chỉnh sửa</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-edit icon-title"></i>
                    <h5>Chỉnh sửa tin tức</h5>
                </div>
                <div class="news-meta">
                    <div class="news-badge slug">
                        <i class="fas fa-link"></i>
                        <span>{{ $news->slug }}</span>
                    </div>
                    <div class="news-badge status {{ $news->is_active ? 'active' : 'inactive' }}">
                        <i class="fas fa-{{ $news->is_active ? 'check-circle' : 'times-circle' }}"></i>
                        <span>{{ $news->is_active ? 'Đang hiển thị' : 'Đã ẩn' }}</span>
                    </div>
                    <div class="news-badge created">
                        <i class="fas fa-calendar"></i>
                        <span>Ngày tạo: {{ $news->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if ($news->is_featured)
                    <div class="news-badge featured">
                        <i class="fas fa-star"></i>
                        <span>Nổi bật</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="form-body">
             

                <form action="{{ route('admin.news.update', $news) }}" method="POST" class="news-form"
                    enctype="multipart/form-data" id="news-form">
                    @csrf
                    @method('PUT')

                    <div class="form-tabs">
                        <div class="form-group">
                            <label for="title" class="form-label-custom">
                                Tiêu đề <span class="required-mark">*</span>
                            </label>
                            <input type="text" class="custom-input {{ $errors->has('title') ? 'input-error' : '' }}"
                                id="title" name="title" value="{{ old('title', $news->title) }}">
                            <div class="error-message" id="error-title">
                                @error('title')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label-custom">
                                Ảnh đại diện
                            </label>
                            <div class="image-upload-container news-image-upload-container">
                                <div class="image-preview news-image-preview {{ $news->avatar ? 'has-image' : '' }}" id="thumbnailPreview"
                                    style="background-image: url('{{ $news->avatar ? asset('storage/' . $news->avatar) : '' }}');">
                                    @if($news->avatar)
                                    <span class="remove-image-btn" id="removeImage" title="Xóa ảnh">
                                        <i class="fas fa-times"></i>
                                    </span>
                                    @endif
                                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                    <span class="upload-text">Chọn ảnh</span>
                                </div>
                                <input type="file" name="avatar" id="thumbnail-upload" class="image-upload-input"
                                    accept="image/*">
                                <input type="hidden" name="remove_avatar" id="removeAvatarInput" value="0">
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                <span>Kích thước đề xuất: 800x600px. Để trống để giữ ảnh hiện tại.</span>
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
                                    {{ old('is_active', $news->is_active) ? 'checked' : '' }}>
                                <label for="is_active" class="form-label-custom mb-0">
                                    Hiển thị tin tức
                                </label>
                            </div>
                            
                            <div class="custom-checkbox mt-2">
                                <input type="checkbox" id="is_featured" name="is_featured" value="1"
                                    {{ old('is_featured', $news->is_featured) ? 'checked' : '' }}>
                                <label for="is_featured" class="form-label-custom mb-0">
                                    <i class="fas fa-star text-warning"></i> Bài viết nổi bật
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="content" class="form-label-custom">
                                Nội dung <span class="required-mark">*</span>
                            </label>
                            <textarea id="content" name="content" class="ckeditor">{{ old('content', $news->content) }}</textarea>
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
                            <i class="fas fa-save"></i> Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script>
        $(document).ready(function() {
            // CKEditor
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
                        
                        // Reset remove avatar flag
                        $('#removeAvatarInput').val(0);
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
                    
                    // Set flag to remove current avatar
                    $('#removeAvatarInput').val(1);
                    
                    // Remove the button
                    $(this).remove();
                });
            }
            
            // Bind remove event if button exists
            bindRemoveEvent();

            // Form submission
            $('#news-form').submit(function(e) {
                for (instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }
            });
        });
    </script>
@endpush