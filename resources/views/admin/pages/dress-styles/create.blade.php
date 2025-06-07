@extends('admin.layouts.sidebar')

@section('title', 'Thêm kiểu dáng mới')

@section('main-content')
    <div class="category-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.dress-styles.index') }}">Kiểu dáng</a></li>
                <li class="breadcrumb-item current">Thêm mới</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-plus icon-title"></i>
                    <h5>Thêm kiểu dáng mới</h5>
                </div>
            </div>
            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.dress-styles.store') }}" method="POST" class="dress-style-form" id="dress-style-form">
                    @csrf

                    <div class="form-section">
                        <h6 class="section-title">Thông tin kiểu dáng</h6>
                        
                        <div class="form-group">
                            <label for="name" class="form-label required">
                                Tên kiểu dáng <span class="required-asterisk">*</span>
                            </label>
                            <input type="text" id="name" name="name" class="custom-input" 
                                   placeholder="Nhập tên kiểu dáng" value="{{ old('name') }}" required>
                            <div class="error-message" id="name-error"></div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea id="description" name="description" class="custom-input" rows="4" 
                                      placeholder="Nhập mô tả về kiểu dáng">{{ old('description') }}</textarea>
                            <div class="error-message" id="description-error"></div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.dress-styles.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Lưu kiểu dáng
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
                        text: response.message || 'Kiểu dáng đã được tạo thành công',
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

</style>
@endpush