@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa kiểu dáng')

@section('main-content')
    <div class="category-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.dress-styles.index') }}">Kiểu dáng</a></li>
                <li class="breadcrumb-item current">Chỉnh sửa</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-edit icon-title"></i>
                    <h5>Chỉnh sửa kiểu dáng</h5>
                </div>
            </div>
            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.dress-styles.update', $dressStyle) }}" method="POST" class="dress-style-form" id="dress-style-form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-section">
                        <h6 class="section-title">Thông tin kiểu dáng</h6>
                        
                        <div class="form-group">
                            <label for="name" class="form-label required">Tên kiểu dáng</label>
                            <input type="text" id="name" name="name" class="custom-input" 
                                   placeholder="Nhập tên kiểu dáng" value="{{ old('name', $dressStyle->name) }}" required>
                            <div class="error-message" id="name-error"></div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea id="description" name="description" class="custom-input" rows="4" 
                                      placeholder="Nhập mô tả về kiểu dáng">{{ old('description', $dressStyle->description) }}</textarea>
                            <div class="error-message" id="description-error"></div>
                        </div>
                        
                        <div class="form-group d-flex flex-column">
                            <label for="banner" class="form-label">Banner kiểu dáng</label>
                            <div class="image-upload-container">
                                <div class="image-preview banner-preview" id="bannerPreview" 
                                   @if($dressStyle->banner) 
                                     style="background-image: url('{{ Storage::url($dressStyle->banner) }}');"
                                     class="has-image" 
                                   @endif
                                >
                                    @if(!$dressStyle->banner)
                                        <i class="fas fa-image"></i>
                                        <span>Chọn banner</span>
                                    @endif
                                </div>
                                <input type="file" id="banner" name="banner" accept="image/*" class="image-input" style="display: none;">
                            </div>
                            <div class="error-message" id="banner-error"></div>
                            <small class="form-text">
                                @if($dressStyle->banner)
                                    Banner hiện tại: {{ basename($dressStyle->banner) }}<br>
                                    Chọn ảnh mới để thay thế hoặc giữ nguyên banner hiện tại.
                                @else
                                    Định dạng: JPG, PNG, JPEG.
                                @endif
                            </small>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.dress-styles.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Cập nhật kiểu dáng
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
        // Image preview handling - click to open file dialog
        $('#bannerPreview').click(function() {
            $('#banner').click();
        });
        
        // Handle file selection
        $('#banner').change(function() {
            previewImage(this);
        });

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                
              
                
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    $('#bannerPreview').css('background-image', `url('${e.target.result}')`);
                    $('#bannerPreview').addClass('has-image');
                    $('#bannerPreview').find('i, span').hide();
                    
                    // Show file info
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    $('.form-text').html(`
                        Định dạng: JPG, PNG, JPEG.<br>
                        <small class="text-success">Đã chọn: ${file.name} (${fileSize}MB)</small>
                    `);
                }
                
                reader.readAsDataURL(file);
            }
        }

        // AJAX form submission
        $('#dress-style-form').submit(function(e) {
            e.preventDefault();
            submitForm();
        });

        function submitForm() {
            // Clear previous errors
            $('.error-message').empty();
            $('.input-error').removeClass('input-error');

            const formData = new FormData(document.getElementById('dress-style-form'));
            const submitBtn = $('.save-button');
            const originalBtnText = submitBtn.html();

            // Disable button and show loading state
            submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');
            submitBtn.prop('disabled', true);

            $.ajax({
                url: $('#dress-style-form').attr('action'),
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
                        text: response.message || 'Kiểu dáng đã được cập nhật thành công',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect after success
                        window.location.href = response.redirect || "{{ route('admin.dress-styles.index') }}";
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
                            const errorElement = $(`#${field}-error`);
                            
                            fieldElement.addClass('input-error');
                            errorElement.text(messages[0]);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Có lỗi xảy ra, vui lòng thử lại sau.'
                        });
                    }
                }
            });
        }
    });
</script>
@endpush

@push('styles')
<style>
    /* Styling for the image upload */
    .image-upload-container {
        margin-top: 5px;
    }
    
    .image-preview {
        width: 100%;
        height: 150px;
        background-color: #f8f9fa;
        border: 2px dashed #ddd;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        transition: all 0.3s ease;
    }
    
    .image-preview:hover {
        border-color: #D1A66E;
    }
    
    .image-preview i {
        font-size: 2rem;
        color: #aaa;
        margin-bottom: 10px;
    }
    
    .image-preview span {
        color: #777;
    }
    
    .image-preview.has-image {
        border-style: solid;
        border-color: #D1A66E;
    }
    
    .form-text {
        margin-top: 5px;
        font-size: 0.85rem;
        color: #666;
    }
</style>
@endpush