@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa danh mục')

@section('main-content')
    <div class="category-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Danh mục</a></li>
                <li class="breadcrumb-item current">Chỉnh sửa</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-edit icon-title"></i>
                    <h5>Chỉnh sửa danh mục</h5>
                </div>
            </div>
            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="category-form" id="category-form">
                    @csrf
                    @method('PUT')

                    <div class="form-section">
                        <h6 class="section-title">Thông tin danh mục</h6>
                        
                        <div class="form-group">
                            <label for="name" class="form-label required">Tên danh mục</label>
                            <input type="text" id="name" name="name" class="custom-input" 
                                   placeholder="Nhập tên danh mục" value="{{ old('name', $category->name) }}" required>
                            <div class="error-message" id="name-error"></div>
                            <small class="form-text">Slug sẽ được tự động cập nhật từ tên danh mục</small>
                        </div>

                        <div class="form-group">
                            <label for="slug-preview" class="form-label">Slug hiện tại</label>
                            <input type="text" id="slug-preview" class="custom-input" 
                                   value="{{ $category->slug }}" readonly>
                            <small class="form-text">Đây là đường dẫn URL hiện tại</small>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea id="description" name="description" class="custom-input" rows="4" 
                                      placeholder="Nhập mô tả về danh mục sản phẩm">{{ old('description', $category->description) }}</textarea>
                            <div class="error-message" id="description-error"></div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h6 class="section-title">Thống kê</h6>
                        
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number">{{ $category->products()->count() }}</div>
                                    <div class="stat-label">Sản phẩm</div>
                                </div>
                            </div>
                            
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number">{{ $category->created_at->format('d/m/Y') }}</div>
                                    <div class="stat-label">Ngày tạo</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.categories.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Cập nhật danh mục
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 16px;
    }
    
    .stat-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #D1A66E;
        color: white;
        border-radius: 8px;
        font-size: 20px;
    }
    
    .stat-content {
        flex: 1;
    }
    
    .stat-number {
        font-size: 24px;
        font-weight: 700;
        color: #333;
        line-height: 1;
    }
    
    .stat-label {
        font-size: 14px;
        color: #666;
        margin-top: 4px;
    }
</style>
@endpush

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
                        text: response.message || 'Danh mục đã được cập nhật thành công',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect after success
                        window.location.href = response.redirect || "{{ route('admin.categories.index') }}";
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