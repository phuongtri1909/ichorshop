@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa bài viết')

@section('main-content')
    <div class="category-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">Bài viết</a></li>
                <li class="breadcrumb-item current">Chỉnh sửa</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-edit icon-title"></i>
                    <h5>Chỉnh sửa bài viết</h5>
                </div>
                <div class="blog-meta">
                    <div class="blog-badge slug">
                        <i class="fas fa-link"></i>
                        <span>{{ $blog->slug }}</span>
                    </div>
                    <div class="blog-badge status {{ $blog->is_active ? 'active' : 'inactive' }}">
                        <i class="fas fa-{{ $blog->is_active ? 'check-circle' : 'times-circle' }}"></i>
                        <span>{{ $blog->is_active ? 'Đang hiển thị' : 'Đã ẩn' }}</span>
                    </div>
                    <div class="blog-badge created">
                        <i class="fas fa-calendar"></i>
                        <span>Ngày tạo: {{ $blog->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if ($blog->is_featured)
                    <div class="blog-badge featured">
                        <i class="fas fa-star"></i>
                        <span>Nổi bật</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.blogs.update', $blog) }}" method="POST" class="blog-form"
                    enctype="multipart/form-data" id="blog-form">
                    @csrf
                    @method('PUT')

                    <div class="form-tabs">
                        <div class="form-group">
                            <label for="title" class="form-label-custom">
                                Tiêu đề <span class="required-mark">*</span>
                            </label>
                            <input type="text" class="custom-input {{ $errors->has('title') ? 'input-error' : '' }}"
                                id="title" name="title" value="{{ old('title', $blog->title) }}">
                            <div class="error-message" id="error-title">
                                @error('title')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="categories" class="form-label-custom">
                                Danh mục <span class="required-mark">*</span>
                            </label>
                            <select id="categories" name="categories[]" class="custom-select {{ $errors->has('categories') ? 'input-error' : '' }}" multiple>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ in_array($category->id, old('categories', $selectedCategories)) ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="error-message" id="error-categories">
                                @error('categories')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label-custom">
                                Ảnh đại diện
                            </label>
                            <div class=" blog-image-upload-container">
                                <div class="image-preview-blog blog-image-preview {{ $blog->image ? 'has-image' : '' }}" id="thumbnailPreview"
                                    style="background-image: url('{{ $blog->image ? asset('storage/' . $blog->image) : '' }}');">
                                    @if($blog->image)
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
                                    {{ old('is_active', $blog->is_active) ? 'checked' : '' }}>
                                <label for="is_active" class="form-label-custom mb-0">
                                    Hiển thị bài viết
                                </label>
                            </div>
                            
                            <div class="custom-checkbox mt-2">
                                <input type="checkbox" id="is_featured" name="is_featured" value="1"
                                    {{ old('is_featured', $blog->is_featured) ? 'checked' : '' }}>
                                <label for="is_featured" class="form-label-custom mb-0">
                                    <i class="fas fa-star text-warning"></i> Bài viết nổi bật
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="content" class="form-label-custom">
                                Nội dung <span class="required-mark">*</span>
                            </label>
                            <textarea id="content" name="content" class="ckeditor">{{ old('content', $blog->content) }}</textarea>
                            <div class="error-message" id="error-content">
                                @error('content')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.blogs.index') }}" class="back-button">
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

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container {
        width: 100% !important;
    }
    
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #ced4da;
        min-height: 38px;
        border-radius: 4px;
    }
    
    .blog-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        margin-right: 10px;
        background-color: #f5f5f5;
    }
    
    .blog-badge i {
        margin-right: 5px;
    }
    
    .blog-badge.status.active {
        background-color: #e8f5e9;
        color: #2e7d32;
    }
    
    .blog-badge.status.inactive {
        background-color: #ffebee;
        color: #c62828;
    }
    
    .blog-badge.featured {
        background-color: #fff8e1;
        color: #f57c00;
    }
    
    .image-upload-container {
        margin-top: 10px;
    }
    
    .image-preview-blog {
        width: 400px;
        height: 300px;
        border: 2px dashed #ddd;
        border-radius: 4px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        position: relative;
    }
    
    .image-preview-blog.has-image i,
    .image-preview-blog.has-image span {
        display: none;
    }
    
    .image-upload-input {
        display: none;
    }
    
    .upload-icon {
        font-size: 2rem;
        color: #6c757d;
        margin-bottom: 10px;
    }
    
    .upload-text {
        color: #6c757d;
        font-size: 1rem;
    }
    
    .remove-image-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(255, 255, 255, 0.8);
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #dc3545;
        cursor: pointer;
        z-index: 10;
    }
    
    .blog-meta {
        margin-top: 10px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('#categories').select2({
            placeholder: 'Chọn danh mục',
            allowClear: true,
            width: '100%'
        });
        
        // Initialize CKEditor
            CKEDITOR.replace('content', {
                filebrowserUploadUrl: "{{ route('admin.blogs.upload.image', ['_token' => csrf_token()]) }}",
                filebrowserUploadMethod: 'form',
                height: 400,
                toolbarGroups: [{
                        name: 'document',
                        groups: ['mode', 'document', 'doctools']
                    },
                    {
                        name: 'clipboard',
                        groups: ['clipboard', 'undo']
                    },
                    {
                        name: 'editing',
                        groups: ['find', 'selection', 'spellchecker', 'editing']
                    },
                    {
                        name: 'forms',
                        groups: ['forms']
                    },
                    {
                        name: 'basicstyles',
                        groups: ['basicstyles', 'cleanup']
                    },
                    {
                        name: 'paragraph',
                        groups: ['list', 'indent', 'blocks', 'align', 'paragraph']
                    },
                    {
                        name: 'links',
                        groups: ['links']
                    },
                    {
                        name: 'insert',
                        groups: ['insert']
                    },
                    {
                        name: 'styles',
                        groups: ['styles']
                    },
                    {
                        name: 'colors',
                        groups: ['colors']
                    },
                    {
                        name: 'tools',
                        groups: ['tools']
                    },
                    {
                        name: 'others',
                        groups: ['others']
                    }
                ],
                // Thêm tùy chọn cho kích thước, màu sắc và định dạng
                fontSize_sizes: '8/8px;9/9px;10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px;26/26px;28/28px;36/36px;48/48px;72/72px',
                font_names: 'Arial/Arial, Helvetica, sans-serif;Times New Roman/Times New Roman, Times, serif;Verdana/Verdana, Geneva, sans-serif;Roboto/Roboto, sans-serif;Open Sans/Open Sans, sans-serif;Lato/Lato, sans-serif;Montserrat/Montserrat, sans-serif;',
                colorButton_colors: '000,800000,8B4513,2F4F4F,008080,000080,4B0082,696969,B22222,A52A2A,DAA520,006400,40E0D0,0000CD,800080,808080,F00,FF8C00,FFD700,008000,0FF,00F,EE82EE,A9A9A9,FFA07A,FFA500,FFFF00,00FF00,AFEEEE,ADD8E6,DDA0DD,D3D3D3,FFF0F5,FAEBD7,FFFFE0,F0FFF0,F0FFFF,F0F8FF,E6E6FA,FFF',
                colorButton_enableMore: true,
                colorButton_foreStyle: {
                    element: 'span',
                    styles: {
                        'color': '#(color)'
                    },
                    overrides: [{
                        element: 'font',
                        attributes: {
                            'color': null
                        }
                    }]
                },
                colorButton_backStyle: {
                    element: 'span',
                    styles: {
                        'background-color': '#(color)'
                    }
                },
                // Cấu hình để thêm các plugin chèn ảnh nâng cao
                extraPlugins: 'uploadimage,clipboard,pastetext,font,colorbutton,justify,image2',
                uploadUrl: "{{ route('admin.blogs.upload.image', ['_token' => csrf_token()]) }}",

                // Hỗ trợ xử lý clipboard và paste ảnh
                clipboard_handleImages: true,
                pasteFilter: null,
                pasteUploadFileApi: "{{ route('admin.blogs.upload.image', ['_token' => csrf_token()]) }}",
                allowedContent: true,

                // Cấu hình xử lý hình ảnh
                image_previewText: ' ',
                image2_alignClasses: ['image-align-left', 'image-align-center', 'image-align-right'],
                image2_disableResizer: false,

                // Danh sách nút sẽ loại bỏ
                removeButtons: 'About,Scayt,Anchor',

                // Cấu hình chỉnh sửa hình ảnh nâng cao
                image2_prefillDimensions: true,
                image2_captionedClass: 'image-captioned',

                // Kích thước mặc định cho ảnh đặt vào
                imageUploadUrl: "{{ route('admin.blogs.upload.image', ['_token' => csrf_token()]) }}",
                imageUploadMethod: 'form',
                filebrowserImageUploadUrl: "{{ route('admin.blogs.upload.image', ['_token' => csrf_token()]) }}"
            });

            // CKEditor events để cập nhật dữ liệu khi submit form
            CKEDITOR.instances.content.on('change', function() {
                this.updateElement();
            });

        // Image upload preview
        $('#thumbnail-upload').change(function() {
            previewImage(this);
        });

        $('#thumbnailPreview').click(function() {
            $('#thumbnail-upload').click();
        });

        // Remove image functionality
        $('#removeImage').click(function(e) {
            e.stopPropagation();
            $('#thumbnailPreview').css('background-image', '');
            $('#thumbnailPreview').removeClass('has-image');
            $('#removeAvatarInput').val(1);
            $(this).remove();
        });

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    $('#thumbnailPreview').css('background-image', `url('${e.target.result}')`);
                    $('#thumbnailPreview').addClass('has-image');
                    $('#removeAvatarInput').val(0);
                    
                    // Add remove button if not exists
                    if ($('#removeImage').length === 0) {
                        $('#thumbnailPreview').append('<span class="remove-image-btn" id="removeImage" title="Xóa ảnh"><i class="fas fa-times"></i></span>');
                        
                        // Bind click event for newly created button
                        $('#removeImage').click(function(e) {
                            e.stopPropagation();
                            $('#thumbnailPreview').css('background-image', '');
                            $('#thumbnailPreview').removeClass('has-image');
                            $('#removeAvatarInput').val(1);
                            $(this).remove();
                            $('#thumbnail-upload').val('');
                        });
                    }
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        // Form validation and submission
        $('#blog-form').submit(function(e) {
            e.preventDefault();
            
            // Update CKEditor instance
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
            
            // Clear previous errors
            $('.error-message').empty();
            $('.input-error').removeClass('input-error');
            
            const formData = new FormData(this);
            const submitBtn = $('.save-button');
            const originalBtnText = submitBtn.html();
            
            // Disable button and show loading state
            submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');
            submitBtn.prop('disabled', true);
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    // Reset button
                    submitBtn.html(originalBtnText);
                    submitBtn.prop('disabled', false);
                    
                    // Show success message using SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: response.message || 'Bài viết đã được cập nhật thành công!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect after success
                        window.location.href = response.redirect || "{{ route('admin.blogs.index') }}";
                    });
                },
                error: function(xhr) {
                    // Reset button
                    submitBtn.html(originalBtnText);
                    submitBtn.prop('disabled', false);
                    
                    // Handle validation errors
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        
                        $.each(errors, function(field, messages) {
                            const fieldElement = $(`[name="${field}"]`);
                            const errorElement = $(`#error-${field}`);
                            
                            fieldElement.addClass('input-error');
                            errorElement.text(messages[0]);
                        });
                        
                        // Special handling for categories array
                        if (errors['categories'] || errors['categories.0']) {
                            $('#categories').next('.select2-container').find('.select2-selection').addClass('input-error');
                            $('#error-categories').text(errors['categories'] || errors['categories.0']);
                        }
                        
                        // Scroll to first error
                        $('html, body').animate({
                            scrollTop: $('.input-error').first().offset().top - 100
                        }, 500);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Có lỗi xảy ra, vui lòng thử lại sau.'
                        });
                    }
                }
            });
        });
    });
</script>
@endpush