@extends('admin.layouts.sidebar')

@section('title', 'Thêm danh mục mới')

@section('main-content')
    <div class="category-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Danh mục</a></li>
                <li class="breadcrumb-item current">Thêm mới</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-plus icon-title"></i>
                    <h5>Thêm danh mục mới</h5>
                </div>
            </div>
            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.categories.store') }}" method="POST" class="category-form"
                    id="category-form">
                    @csrf

                    <div class="form-section">
                        <h6 class="section-title">Thông tin danh mục</h6>

                        <div class="form-group">
                            <label for="name" class="form-label required">
                                Tên danh mục <span class="required-asterisk">*</span>
                            </label>
                            <input type="text" id="name" name="name" class="custom-input"
                                placeholder="Nhập tên danh mục" value="{{ old('name') }}" required>
                            <div class="error-message" id="name-error"></div>
                            <small class="form-text">Slug sẽ được tự động tạo từ tên danh mục</small>
                        </div>

                        <div class="form-group">
                            <label for="slug-preview" class="form-label">Slug (URL thân thiện)</label>
                            <input type="text" id="slug-preview" class="custom-input" placeholder="slug-tu-dong-tao"
                                readonly>
                            <small class="form-text">Đây là đường dẫn URL sẽ được hiển thị</small>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea id="description" name="description" class="custom-input" rows="4"
                                placeholder="Nhập mô tả về danh mục sản phẩm">{{ old('description') }}</textarea>
                            <div class="error-message" id="description-error"></div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.categories.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Lưu danh mục
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
            // Auto generate slug from name
            $('#name').on('input', function() {
                const name = $(this).val();
                const slug = generateSlug(name);
                $('#slug-preview').val(slug);
            });

            function generateSlug(text) {
                return text
                    .toLowerCase()
                    .replace(/[àáạảãâầấậẩẫăằắặẳẵ]/g, 'a')
                    .replace(/[èéẹẻẽêềếệểễ]/g, 'e')
                    .replace(/[ìíịỉĩ]/g, 'i')
                    .replace(/[òóọỏõôồốộổỗơờớợởỡ]/g, 'o')
                    .replace(/[ùúụủũưừứựửữ]/g, 'u')
                    .replace(/[ỳýỵỷỹ]/g, 'y')
                    .replace(/đ/g, 'd')
                    .replace(/[^a-z0-9 -]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim();
            }

            // AJAX form submission
            $('#category-form').submit(function(e) {
                e.preventDefault();
                submitForm();
            });

            function submitForm() {
                // Clear previous errors
                $('.error-message').empty();
                $('.input-error').removeClass('input-error');

                const formData = new FormData(document.getElementById('category-form'));
                const submitBtn = $('.save-button');
                const originalBtnText = submitBtn.html();

                // Disable button and show loading state
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');
                submitBtn.prop('disabled', true);

                $.ajax({
                    url: $('#category-form').attr('action'),
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
                            text: response.message || 'Danh mục đã được tạo thành công',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Redirect after success
                            window.location.href = response.redirect ||
                                "{{ route('admin.categories.index') }}";
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
