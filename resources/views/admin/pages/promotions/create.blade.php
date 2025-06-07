@extends('admin.layouts.sidebar')

@section('title', 'Thêm khuyến mãi mới')

@section('main-content')
    <div class="category-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.promotions.index') }}">Khuyến mãi</a></li>
                <li class="breadcrumb-item current">Thêm mới</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-plus icon-title"></i>
                    <h5>Thêm khuyến mãi mới</h5>
                </div>
            </div>
            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.promotions.store') }}" method="POST" class="promotion-form" id="promotion-form">
                    @csrf

                    <div class="form-section">
                        <h6 class="section-title">Thông tin cơ bản</h6>
                        
                        <div class="form-group">
                            <label for="name" class="form-label required">
                                Tên khuyến mãi <span class="required-asterisk">*</span>
                            </label>
                            <input type="text" id="name" name="name" class="custom-input" 
                                   placeholder="Nhập tên khuyến mãi" value="{{ old('name') }}" required>
                            <div class="error-message" id="name-error"></div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea id="description" name="description" class="custom-input" rows="4" 
                                      placeholder="Nhập mô tả về chương trình khuyến mãi">{{ old('description') }}</textarea>
                            <div class="error-message" id="description-error"></div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h6 class="section-title">Thiết lập giảm giá</h6>
                        
                        <div class="row">
                            <div class="form-group">
                                <label for="discount_type" class="form-label required">
                                    Loại giảm giá <span class="required-asterisk">*</span>
                                </label>
                                <select id="discount_type" name="discount_type" class="custom-select" required>
                                    <option value="">Chọn loại giảm giá</option>
                                    <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Phần trăm (%)</option>
                                    <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Số tiền cố định (đ)</option>
                                </select>
                                <div class="error-message" id="discount_type-error"></div>
                            </div>

                            <div class="form-group">
                                <label for="discount_value" class="form-label required">
                                    Giá trị giảm <span class="required-asterisk">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" id="discount_value" name="discount_value" class="custom-input" 
                                           placeholder="0" value="{{ old('discount_value') }}" min="0" step="0.01" required>
                                    <span class="input-suffix" id="discount-suffix">%</span>
                                </div>
                                <div class="error-message" id="discount_value-error"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group">
                                <label for="min_order_amount" class="form-label">Đơn hàng tối thiểu</label>
                                <div class="input-group">
                                    <input type="number" id="min_order_amount" name="min_order_amount" class="custom-input" 
                                           placeholder="0" value="{{ old('min_order_amount') }}" min="0" step="1000">
                                    <span class="input-suffix">đ</span>
                                </div>
                                <div class="error-message" id="min_order_amount-error"></div>
                                <small class="form-text">Để trống nếu không có giới hạn</small>
                            </div>

                            <div class="form-group">
                                <label for="max_discount_amount" class="form-label">Giảm tối đa</label>
                                <div class="input-group">
                                    <input type="number" id="max_discount_amount" name="max_discount_amount" class="custom-input" 
                                           placeholder="0" value="{{ old('max_discount_amount') }}" min="0" step="1000">
                                    <span class="input-suffix">đ</span>
                                </div>
                                <div class="error-message" id="max_discount_amount-error"></div>
                                <small class="form-text">Áp dụng cho giảm theo phần trăm</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h6 class="section-title">Thời gian và giới hạn</h6>
                        
                        <div class="row">
                            <div class="form-group">
                                <label for="start_date" class="form-label required">
                                    Ngày bắt đầu <span class="required-asterisk">*</span>
                                </label>
                                <input type="datetime-local" id="start_date" name="start_date" class="custom-input" 
                                       value="{{ old('start_date') }}" required>
                                <div class="error-message" id="start_date-error"></div>
                            </div>

                            <div class="form-group">
                                <label for="end_date" class="form-label required">
                                    Ngày kết thúc <span class="required-asterisk">*</span>
                                </label>
                                <input type="datetime-local" id="end_date" name="end_date" class="custom-input" 
                                       value="{{ old('end_date') }}" required>
                                <div class="error-message" id="end_date-error"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="usage_limit" class="form-label">Giới hạn sử dụng</label>
                            <input type="number" id="usage_limit" name="usage_limit" class="custom-input" 
                                   placeholder="Để trống nếu không giới hạn" value="{{ old('usage_limit') }}" min="1">
                            <div class="error-message" id="usage_limit-error"></div>
                            <small class="form-text">Số lần tối đa có thể sử dụng khuyến mãi này</small>
                        </div>
                    </div>

                    <div class="form-section">
                        <h6 class="section-title">Trạng thái</h6>
                        
                        <div class="form-group">
                            <div class="custom-checkbox">
                                <input type="checkbox" id="is_active" name="is_active" value="1" 
                                       {{ old('is_active') ? 'checked' : '' }}>
                                <label for="is_active" class="checkbox-label">
                                    <span class="checkbox-text">Kích hoạt khuyến mãi</span>
                                    <small class="checkbox-desc">Khuyến mãi sẽ có hiệu lực trong khoảng thời gian đã thiết lập</small>
                                </label>
                            </div>
                            <div class="error-message" id="is_active-error"></div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.promotions.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Lưu khuyến mãi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    
    .input-group {
        position: relative;
    }
    
    .input-suffix {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #666;
        font-weight: 500;
    }
    
    .custom-checkbox {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }
    
    .custom-checkbox input[type="checkbox"] {
        margin: 0;
        width: 18px;
        height: 18px;
        flex-shrink: 0;
    }
    
    .checkbox-label {
        margin: 0;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .checkbox-text {
        font-weight: 500;
        color: #333;
    }
    
    .checkbox-desc {
        color: #666;
        font-size: 13px;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Update discount suffix based on type
        $('#discount_type').change(function() {
            const type = $(this).val();
            const suffix = type === 'percentage' ? '%' : 'đ';
            $('#discount-suffix').text(suffix);
            
            // Update placeholder and validation
            if (type === 'percentage') {
                $('#discount_value').attr('max', '100');
                $('#discount_value').attr('placeholder', 'Ví dụ: 10');
            } else {
                $('#discount_value').removeAttr('max');
                $('#discount_value').attr('placeholder', 'Ví dụ: 50000');
            }
        });

        // Set default dates
        const now = new Date();
        const tomorrow = new Date(now);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        const formatDateTime = (date) => {
            return date.toISOString().slice(0, 16);
        };
        
        if (!$('#start_date').val()) {
            $('#start_date').val(formatDateTime(now));
        }
        if (!$('#end_date').val()) {
            $('#end_date').val(formatDateTime(tomorrow));
        }

        // Validate end date
        $('#start_date, #end_date').change(function() {
            const startDate = new Date($('#start_date').val());
            const endDate = new Date($('#end_date').val());
            
            if (endDate <= startDate) {
                const newEndDate = new Date(startDate);
                newEndDate.setHours(startDate.getHours() + 1);
                $('#end_date').val(formatDateTime(newEndDate));
            }
        });

        // AJAX form submission
        $('#promotion-form').submit(function(e) {
            e.preventDefault();
            submitForm();
        });

        function submitForm() {
            // Clear previous errors
            $('.error-message').empty();
            $('.input-error').removeClass('input-error');

            const formData = new FormData(document.getElementById('promotion-form'));
            const submitBtn = $('.save-button');
            const originalBtnText = submitBtn.html();

            // Disable button and show loading state
            submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');
            submitBtn.prop('disabled', true);

            $.ajax({
                url: $('#promotion-form').attr('action'),
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
                        text: response.message || 'Khuyến mãi đã được tạo thành công',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect after success
                        window.location.href = response.redirect || "{{ route('admin.promotions.index') }}";
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