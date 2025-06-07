@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa thương hiệu')

@section('main-content')
    <div class="category-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.brands.index') }}">Thương hiệu</a></li>
                <li class="breadcrumb-item current">Chỉnh sửa</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-edit icon-title"></i>
                    <h5>Chỉnh sửa thương hiệu</h5>
                </div>
            </div>
            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.brands.update', $brand) }}" method="POST" class="brand-form" enctype="multipart/form-data" id="brand-form">
                    @csrf
                    @method('PUT')

                    <div class="form-section">
                        <h6 class="section-title">Thông tin thương hiệu</h6>
                        
                        <div class="form-group">
                            <label for="name" class="form-label required">
                                Tên thương hiệu <span class="required-asterisk">*</span>
                            </label>
                            <input type="text" id="name" name="name" class="custom-input" 
                                   placeholder="Nhập tên thương hiệu" value="{{ old('name', $brand->name) }}" required>
                            <div class="error-message" id="name-error"></div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea id="description" name="description" class="custom-input" rows="4" 
                                      placeholder="Nhập mô tả về thương hiệu">{{ old('description', $brand->description) }}</textarea>
                            <div class="error-message" id="description-error"></div>
                        </div>

                        <div class="form-group d-flex flex-column">
                            <label for="logo" class="form-label">Logo thương hiệu</label>
                            <div class="image-upload-container">
                                <div class="image-preview {{ $brand->logo ? 'has-image' : '' }}" id="logoPreview" 
                                     @if($brand->logo) style="background-image: url('{{ asset('storage/'.$brand->logo) }}')" @endif>
                                    @if(!$brand->logo)
                                        <i class="fas fa-image"></i>
                                        <span>Chọn logo</span>
                                    @endif
                                </div>
                                <input type="file" id="logo" name="logo" accept="image/*" class="image-input" style="display: none;">
                            </div>
                            <div class="error-message" id="logo-error"></div>
                            <small class="form-text">Định dạng: JPG, PNG, GIF. Kích thước tối đa: 2MB</small>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.brands.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Cập nhật thương hiệu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>

    
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Image preview handling - click to open file dialog
        $('#logoPreview').click(function() {
            $('#logo').click();
        });
        
        // Handle file selection
        $('#logo').change(function() {
            previewImage(this);
        });

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    $('#logoPreview').css('background-image', `url('${e.target.result}')`);
                    $('#logoPreview').addClass('has-image');
                    $('#logoPreview').find('i, span').hide();
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // AJAX form submission
        $('#brand-form').submit(function(e) {
            e.preventDefault();
            submitForm();
        });

        function submitForm() {
            // Clear previous errors
            $('.error-message').empty();
            $('.input-error').removeClass('input-error');

            // Create FormData object
            const formData = new FormData(document.getElementById('brand-form'));
            const submitBtn = $('.save-button');
            const originalBtnText = submitBtn.html();

            // Disable button and show loading state
            submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');
            submitBtn.prop('disabled', true);

            $.ajax({
                url: $('#brand-form').attr('action'),
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
                        text: response.message || 'Thương hiệu đã được cập nhật thành công',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect after success
                        window.location.href = response.redirect || "{{ route('admin.brands.index') }}";
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