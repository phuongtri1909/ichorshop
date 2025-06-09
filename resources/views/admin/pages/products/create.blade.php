@extends('admin.layouts.sidebar')

@section('title', 'Thêm sản phẩm mới')

@section('main-content')
    <div class="product-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                <li class="breadcrumb-item current">Thêm mới</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-plus icon-title"></i>
                    <h5>Thêm sản phẩm mới</h5>
                </div>
            </div>
            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs mb-4" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active title-tab-primary-5 color-primary-hover" id="basic-info-tab"
                            data-bs-toggle="tab" data-bs-target="#basic-info" type="button" role="tab">
                            <i class="fas fa-info-circle me-1"></i>
                            Thông tin cơ bản
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link title-tab-primary-5 color-primary-hover" id="variants-tab"
                            data-bs-toggle="tab" data-bs-target="#variants" type="button" role="tab">
                            <i class="fas fa-layer-group me-1"></i>
                            Biến thể sản phẩm
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link title-tab-primary-5 color-primary-hover" id="images-tab"
                            data-bs-toggle="tab" data-bs-target="#images" type="button" role="tab">
                            <i class="fas fa-images me-1"></i>
                            Hình ảnh sản phẩm
                        </button>
                    </li>

                </ul>

                <form action="{{ route('admin.products.store') }}" method="POST" class="product-form"
                    enctype="multipart/form-data" id="product-form">
                    @csrf

                    <!-- Tab Content -->
                    <div class="tab-content" id="productTabsContent">
                        <!-- Tab 1: Thông tin cơ bản -->
                        <div class="tab-pane fade show active" id="basic-info" role="tabpanel">
                            <!-- Thông tin cơ bản -->
                            <div class="form-section">
                                <h6 class="section-title">Thông tin cơ bản</h6>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="name" class="form-label required">
                                            Tên sản phẩm <span class="required-asterisk">*</span>
                                        </label>
                                        <input type="text" id="name" name="name" class="custom-input"
                                            placeholder="Nhập tên sản phẩm" value="{{ old('name') }}">
                                        <div class="error-message" id="name-error"></div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="status" class="form-label required">
                                            Trạng thái <span class="required-asterisk">*</span>
                                        </label>
                                        <select id="status" name="status" class="custom-input">
                                            <option value="">Chọn trạng thái</option>
                                            <option value="active"
                                                {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Đang bán
                                            </option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Đã
                                                ẩn</option>
                                        </select>
                                        <div class="error-message" id="status-error"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description_short" class="form-label">Mô tả ngắn</label>
                                    <textarea id="description_short" name="description_short" class="custom-input" rows="3"
                                        placeholder="Nhập mô tả ngắn về sản phẩm (tối đa 500 ký tự)" maxlength="500">{{ old('description_short') }}</textarea>
                                    <div class="error-message" id="description_short-error"></div>
                                    
                                </div>

                                <div class="form-group">
                                    <label for="description_long" class="form-label">Mô tả chi tiết</label>
                                    <textarea id="description_long" name="description_long" class="custom-input" rows="8"
                                        placeholder="Nhập mô tả chi tiết về sản phẩm">{{ old('description_long') }}</textarea>
                                    <div class="error-message" id="description_long-error"></div>
                                </div>
                            </div>

                            <!-- Phân loại sản phẩm -->
                            <div class="form-section">
                                <h6 class="section-title">Phân loại sản phẩm</h6>

                                <div class="row">

                                    <div class="form-group col-12 col-md-4">
                                        <label for="categories" class="form-label required">
                                            Danh mục <span class="required-asterisk">*</span>
                                        </label>
                                        <select id="categories" name="categories[]" class="custom-input" multiple>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ in_array($category->id, old('categories', [])) ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="error-message" id="categories-error"></div>
                                        <small class="form-text">Chọn ít nhất một danh mục (Ctrl+Click để chọn
                                            nhiều)</small>
                                    </div>

                                    <div class="form-group col-12 col-md-4">
                                        <label for="brand_id" class="form-label">
                                            Thương hiệu
                                        </label>
                                        <select id="brand_id" name="brand_id" class="custom-input">
                                            <option value="">Chọn thương hiệu</option>
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}"
                                                    {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="error-message" id="brand_id-error"></div>
                                    </div>



                                    <div class="form-group col-12 col-md-4">
                                        <label for="dress_styles" class="form-label">Kiểu dáng</label>
                                        <select id="dress_styles" name="dress_styles[]" class="custom-input" multiple>
                                            @foreach ($dressStyles as $style)
                                                <option value="{{ $style->id }}"
                                                    {{ in_array($style->id, old('dress_styles', [])) ? 'selected' : '' }}>
                                                    {{ $style->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text">Tùy chọn - có thể chọn nhiều kiểu dáng
                                            (Ctrl+Click)</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Ảnh đại diện -->
                            <div class="form-section">
                                <h6 class="section-title">Ảnh đại diện sản phẩm</h6>

                                <div class="form-group d-flex flex-column">
                                    <label for="avatar" class="form-label required">
                                        Ảnh đại diện <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="image-upload-container">
                                        <div class="image-preview main-image" id="avatarPreview">
                                            <i class="fas fa-image"></i>
                                            <span>Chọn ảnh đại diện</span>
                                        </div>
                                        <input type="file" id="avatar" name="avatar" accept="image/*"
                                            class="image-input" style="display: none;" required>
                                    </div>
                                    <div class="error-message" id="avatar-error"></div>
                                    <small class="form-text">Ảnh chính của sản phẩm. Định dạng: JPG, PNG, JPEG, Tỉ lệ 1:1</small>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Biến thể sản phẩm -->
                        <div class="tab-pane fade " id="variants" role="tabpanel">
                            <div class="form-section">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="section-title mb-0">
                                        Biến thể sản phẩm <span class="required-asterisk">*</span>
                                        <small class="text-muted">(Phải có ít nhất 1 biến thể)</small>
                                    </h6>
                                    <button type="button" class="btn btn-success btn-sm" onclick="addVariant()">
                                        <i class="fas fa-plus me-1"></i> Thêm biến thể
                                    </button>
                                </div>

                                <div id="variants-container">
                                    @include('components.variant-item', ['index' => 0, 'variant' => []])
                                </div>

                                <div class="error-message" id="variants-error"></div>
                            </div>
                        </div>

                        <!-- Tab 3: Hình ảnh sản phẩm -->
                        <div class="tab-pane fade" id="images" role="tabpanel">
                            <div class="form-section">
                                <h6 class="section-title">Quản lý hình ảnh sản phẩm</h6>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Hướng dẫn:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Nếu bạn có biến thể có màu, hãy chọn màu tương ứng khi upload ảnh</li>
                                        <li>Nếu không chọn màu, ảnh sẽ là ảnh chung cho tất cả biến thể</li>
                                        <li>Mỗi ảnh tối đa định dạng JPG, PNG, JPEG, Tỉ lệ 3:4</li>
                                    </ul>
                                </div>

                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0">Ảnh sản phẩm: Tỉ lệ 3:4</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="addImageBtn">
                                            <i class="fas fa-plus me-1"></i> Thêm ảnh
                                        </button>
                                    </div>

                                    <div id="product-images-container">
                                        <!-- Ảnh sẽ được thêm động ở đây -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.products.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Lưu sản phẩm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Color Input Group Styles */
        .color-input-group {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .color-picker {
            width: 50px;
            height: 38px;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            background: none;
            padding: 0;
            flex-shrink: 0;
        }

        .color-picker::-webkit-color-swatch-wrapper {
            padding: 0;
            border: none;
        }

        .color-picker::-webkit-color-swatch {
            border: none;
            border-radius: 3px;
        }

        .color-name-input {
            flex: 1;
        }

        /* Product Images Upload Styles */
        .product-image-upload-item {
            border: 1px solid #e0e6ed;
            border-radius: 8px;
            background: #fff;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .product-image-upload-item:hover {
            border-color: #007bff;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
        }

        .product-image-preview-container {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .product-image-preview {
            width: 120px;
            height: 120px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            background-color: #f8f9fa;
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .product-image-preview:hover {
            border-color: #007bff;
            background-color: #e3f2fd;
        }

        .product-image-preview.has-image {
            border: 2px solid #28a745;
        }

        .product-image-preview.has-image i,
        .product-image-preview.has-image span {
            display: none !important;
        }

        .product-image-preview i {
            font-size: 24px;
            color: #6c757d;
            margin-bottom: 8px;
        }

        .product-image-preview span {
            font-size: 12px;
            color: #6c757d;
            text-align: center;
            padding: 0 10px;
        }

        .remove-product-image-btn {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 24px;
            height: 24px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            transition: all 0.3s ease;
        }

        .remove-product-image-btn:hover {
            background: #c82333;
            transform: scale(1.1);
        }

        .color-selector-group {
            background: #f8f9fa;
            border: 1px solid #e0e6ed;
            border-radius: 6px;
            padding: 15px;
            margin-top: 10px;
        }

        .color-option {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.2s ease;
            border: 1px solid transparent;
        }

        .color-option:hover {
            background-color: #e3f2fd;
            border-color: #007bff;
        }

        .color-option:last-child {
            margin-bottom: 0;
        }

        .color-option input[type="radio"] {
            margin-right: 10px;
            transform: scale(1.1);
        }

        .color-label {
            font-size: 14px;
            color: #495057;
            cursor: pointer;
            flex: 1;
            display: flex;
            align-items: center;
        }

        .color-display-swatch {
            width: 20px;
            height: 20px;
            border-radius: 3px;
            border: 1px solid #ddd;
            margin-left: 8px;
            flex-shrink: 0;
        }

        .no-color-option .color-label {
            font-style: italic;
            color: #6c757d;
            font-weight: 500;
        }

        /* Avatar styles - separate and distinct */
        .avatar-upload-container {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .avatar-image-preview {
            width: 180px;
            height: 180px;
            border: 3px dashed #ddd;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            background-color: #f8f9fa;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .avatar-image-preview:hover {
            border-color: #007bff;
            background-color: #e3f2fd;
        }

        .avatar-image-preview.has-image {
            border-color: #28a745;
        }

        .avatar-image-preview.has-image i,
        .avatar-image-preview.has-image span {
            display: none !important;
        }

        .avatar-image-preview i {
            font-size: 32px;
            color: #6c757d;
            margin-bottom: 12px;
        }

        .avatar-image-preview span {
            font-size: 14px;
            color: #6c757d;
            text-align: center;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .product-image-upload-item .row {
                flex-direction: column;
            }

            .product-image-upload-item .col-md-3,
            .product-image-upload-item .col-md-9 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .product-image-preview-container {
                display: flex;
                justify-content: center;
                margin-bottom: 15px;
            }

            .color-input-group {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }

            .color-picker {
                width: 100%;
                height: 45px;
            }
        }

        /* Tab error indication */
        .nav-link.tab-error {
            color: #dc3545 !important;
            border-color: #dc3545;
            position: relative;
        }

        .nav-link.tab-error::after {
            content: '!';
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Error message styling */
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }

        .input-error {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }

        /* Form section error indication */
        .form-section.has-error {
            border-left: 3px solid #dc3545;
            background-color: #fff5f5;
            padding-left: 15px;
        }

        .form-section.has-error .section-title {
            color: #dc3545;
        }

        /* Variant item error indication */
        .variant-item.has-error {
            border-color: #dc3545;
            background-color: #fff5f5;
        }

        .variant-item.has-error .card-header {
            background-color: #f8d7da;
            border-bottom-color: #dc3545;
        }

        /* Validation Error Popup Styles */
        .validation-error-popup {
            border-radius: 12px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
        }

        .validation-error-title {
            color: #dc3545 !important;
            font-weight: 600 !important;
            font-size: 1.25rem !important;
        }

        .validation-error-content {
            text-align: left !important;
            padding: 0 !important;
        }

        .validation-errors-container {
            max-height: 400px;
            overflow-y: auto;
            padding: 10px 0;
        }

        .error-section {
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            background: #f8f9fa;
            padding: 15px;
        }

        .error-section:last-child {
            margin-bottom: 0;
        }

        .error-section-title {
            margin: 0 0 12px 0;
            font-size: 1rem;
            font-weight: 600;
            color: #495057;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .error-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .error-list li {
            padding: 6px 0;
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.4;
            display: flex;
            align-items: flex-start;
        }

        .error-list li:last-child {
            padding-bottom: 0;
        }

        .error-list li i {
            margin-top: 2px;
            flex-shrink: 0;
        }

        .tab-switch-notice {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 6px;
            padding: 10px 15px;
            margin: 10px 0 0 0;
            color: #1976d2;
            font-size: 0.85rem;
            text-align: center;
        }

        .tab-switch-notice i {
            color: #2196f3;
        }

        /* Custom scrollbar for validation container */
        .validation-errors-container::-webkit-scrollbar {
            width: 6px;
        }

        .validation-errors-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .validation-errors-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .validation-errors-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Animation for error sections */
        .error-section {
            animation: slideInLeft 0.3s ease-out;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive design for mobile */
        @media (max-width: 768px) {
            .validation-error-popup {
                width: 95% !important;
                margin: 0 auto !important;
            }

            .validation-errors-container {
                max-height: 300px;
            }

            .error-section {
                padding: 10px;
            }

            .error-section-title {
                font-size: 0.9rem;
            }

            .error-list li {
                font-size: 0.85rem;
                padding: 4px 0;
            }
        }

        .color-toggle-wrapper {
            display: flex;
            align-items: center;
        }

        .form-check-input.color-toggle {
            height: 1.5rem;
            width: 3rem;
        }

        .form-check-label {
            margin-left: 0.5rem;
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let variantCount = 1;
        let imageCount = 0;
        let currentTab = 'basic-info'; // Track current active tab

        $(document).ready(function() {
            // Remove all HTML5 required attributes to prevent browser validation
            $('input[required], select[required], textarea[required]').removeAttr('required');

            // Track current active tab
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                currentTab = e.target.getAttribute('data-bs-target').replace('#', '');
            });

            // Avatar image handling
            $('#avatarPreview').click(function() {
                $('#avatar').click();
            });

            $('#avatar').change(function() {
                previewMainImage(this);
            });

            // Add image button
            $('#addImageBtn').click(function() {
                addImageUpload();
            });

            function previewMainImage(input) {
                if (input.files && input.files[0]) {
                    const file = input.files[0];
                    if (!file.type.startsWith('image/')) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi định dạng',
                            text: 'Vui lòng chọn tệp hình ảnh (JPG, PNG, JPEG)'
                        });
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#avatarPreview').css('background-image', `url('${e.target.result}')`);
                        $('#avatarPreview').addClass('has-image');
                        $('#avatarPreview').find('i, span').hide();
                    }
                    reader.readAsDataURL(file);
                }
            }

            // Handle color picker changes
            $(document).on('input', '.color-picker', function() {
                updateAllImageColorOptions();
            });

            // Handle color name changes
            $(document).on('input', '.color-name-input', function() {
                updateAllImageColorOptions();
            });

            // Character counter for short description
            $('#description_short').on('input', function() {
                const maxLength = 500;
                const currentLength = $(this).val().length;
                const remaining = maxLength - currentLength;

                let counterText = $(this).siblings('.form-text');
                if (counterText.find('.char-counter').length === 0) {
                    counterText.append(' <span class="char-counter"></span>');
                }

                counterText.find('.char-counter').text(`(${remaining} ký tự còn lại)`);

                if (remaining < 50) {
                    counterText.find('.char-counter').css('color', '#dc3545');
                } else {
                    counterText.find('.char-counter').css('color', '#6c757d');
                }
            });

            // AJAX form submission - prevent default form submission
            $('#product-form').on('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const validationResult = validateForm();
                if (!validationResult.isValid) {


                    // Mark tabs with errors
                    markTabsWithErrors(validationResult);

                    // Switch to the tab with errors if current tab is different
                    if (validationResult.errorTab && validationResult.errorTab !== currentTab) {

                        $(`#${validationResult.errorTab}-tab`).tab('show');
                        currentTab = validationResult.errorTab;
                    }
                    return false;
                }

                submitForm();
                return false;
            });

            function markTabsWithErrors(validationResult) {
                // Clear all error indicators first
                $('.nav-link').removeClass('tab-error');

                // Add error indicators to tabs with errors
                if (validationResult.errorTab) {
                    $(`#${validationResult.errorTab}-tab`).addClass('tab-error');
                }

                // Check other tabs for errors too
                const basicInfoErrors = validationResult.errors.filter(error =>
                    error.includes('Tên sản phẩm') ||
                    error.includes('trạng thái') ||
                    error.includes('danh mục') ||
                    error.includes('ảnh đại diện') ||
                    error.includes('Mô tả ngắn')
                );

                const variantErrors = validationResult.errors.filter(error =>
                    error.includes('Biến thể') ||
                    error.includes('biến thể')
                );

                const imageErrors = validationResult.errors.filter(error =>
                    error.includes('màu') && error.includes('ảnh')
                );

                if (basicInfoErrors.length > 0) {
                    $('#basic-info-tab').addClass('tab-error');
                }
                if (variantErrors.length > 0) {
                    $('#variants-tab').addClass('tab-error');
                }
                if (imageErrors.length > 0) {
                    $('#images-tab').addClass('tab-error');
                }
            }

            function validateForm() {
                let validationResult = {
                    isValid: true,
                    errorTab: null,
                    errors: []
                };

                // Clear previous errors
                $('.error-message').empty();
                $('.input-error').removeClass('input-error');
                $('.nav-link').removeClass('tab-error');

                // Validate basic info tab
                const basicInfoErrors = validateBasicInfo();
                if (basicInfoErrors.length > 0) {
                    validationResult.isValid = false;
                    if (!validationResult.errorTab) {
                        validationResult.errorTab = 'basic-info';
                    }
                    validationResult.errors = validationResult.errors.concat(basicInfoErrors);
                }

                // Validate variants tab
                const variantErrors = validateVariants();
                if (variantErrors.length > 0) {
                    validationResult.isValid = false;
                    if (!validationResult.errorTab) {
                        validationResult.errorTab = 'variants';
                    }
                    validationResult.errors = validationResult.errors.concat(variantErrors);
                }

                // Validate images tab
                const imageErrors = validateImages();
                if (imageErrors.length > 0) {
                    validationResult.isValid = false;
                    if (!validationResult.errorTab) {
                        validationResult.errorTab = 'images';
                    }
                    validationResult.errors = validationResult.errors.concat(imageErrors);
                }

                // Show errors
                if (!validationResult.isValid) {
                    showValidationErrors(validationResult);
                }

                return validationResult;
            }

            function validateBasicInfo() {
                const errors = [];

                // Validate name
                const name = $('#name').val().trim();
                if (!name) {
                    $('#name').addClass('input-error');
                    $('#name-error').text('Tên sản phẩm là bắt buộc');
                    errors.push('Tên sản phẩm là bắt buộc');
                }

                // Validate status
                const status = $('#status').val();
                if (!status) {
                    $('#status').addClass('input-error');
                    $('#status-error').text('Vui lòng chọn trạng thái sản phẩm');
                    errors.push('Vui lòng chọn trạng thái sản phẩm');
                }

                // Validate categories
                const categories = $('#categories').val();
                if (!categories || categories.length === 0) {
                    $('#categories').addClass('input-error');
                    $('#categories-error').text('Vui lòng chọn ít nhất một danh mục');
                    errors.push('Vui lòng chọn ít nhất một danh mục');
                }

                // Validate avatar
                const avatar = $('#avatar')[0].files;
                if (!avatar || avatar.length === 0) {
                    $('#avatarPreview').addClass('input-error');
                    $('#avatar-error').text('Vui lòng chọn ảnh đại diện sản phẩm');
                    errors.push('Vui lòng chọn ảnh đại diện sản phẩm');
                }

                // Validate short description length
                const shortDesc = $('#description_short').val();
                if (shortDesc && shortDesc.length > 500) {
                    $('#description_short').addClass('input-error');
                    $('#description_short-error').text('Mô tả ngắn không được vượt quá 500 ký tự');
                    errors.push('Mô tả ngắn không được vượt quá 500 ký tự');
                }

                return errors;
            }

            function validateVariants() {
                const errors = [];

                // Check if has at least one variant
                if ($('.variant-item').length === 0) {
                    $('#variants-error').text('Phải có ít nhất một biến thể sản phẩm');
                    errors.push('Phải có ít nhất một biến thể sản phẩm');
                    return errors;
                }

                // Validate each variant
                let hasValidVariant = false;
                const skus = [];

                $('.variant-item').each(function(index) {
                    let variantErrors = [];
                    let variantHasErrors = false;
                    const variantNumber = index + 1;

                    // Validate price - sử dụng attribute selector chính xác hơn
                    const priceInput = $(this).find('input[name*="[price]"]');
                    const price = priceInput.val();
                    if (!price || parseFloat(price) < 0) {
                        priceInput.addClass('input-error');
                        variantErrors.push(`Biến thể #${variantNumber}: Giá bán phải lớn hơn hoặc bằng 0`);
                        variantHasErrors = true;
                    }

                    // Validate quantity - kiểm tra cả stock_quantity và quantity
                    const quantityInput = $(this).find(
                        'input[name*="[quantity]"], input[name*="[stock_quantity]"]');
                    const quantity = quantityInput.val();
                    if (quantity === '' || parseInt(quantity) < 0) {
                        quantityInput.addClass('input-error');
                        variantErrors.push(`Biến thể #${variantNumber}: Số lượng phải lớn hơn hoặc bằng 0`);
                        variantHasErrors = true;
                    }

                    // Validate status
                    const statusSelect = $(this).find('select[name*="[status]"]');
                    const status = statusSelect.val();
                    if (!status) {
                        statusSelect.addClass('input-error');
                        variantErrors.push(`Biến thể #${variantNumber}: Vui lòng chọn trạng thái`);
                        variantHasErrors = true;
                    }

                    // Validate SKU uniqueness
                    const skuInput = $(this).find('input[name*="[sku]"]');
                    const sku = skuInput.val().trim();
                    if (sku) {
                        if (skus.includes(sku)) {
                            skuInput.addClass('input-error');
                            variantErrors.push(`Biến thể #${variantNumber}: Mã SKU đã trùng lặp`);
                            variantHasErrors = true;
                        } else {
                            skus.push(sku);
                        }
                    }

                    // Mark variant as having errors
                    if (variantHasErrors) {
                        $(this).addClass('has-error');
                    } else {
                        $(this).removeClass('has-error');
                        hasValidVariant = true;
                    }

                    errors.push(...variantErrors);
                });

                if (!hasValidVariant && $('.variant-item').length > 0) {
                    $('#variants-error').text('Ít nhất một biến thể phải hợp lệ');
                    errors.push('Ít nhất một biến thể phải hợp lệ');
                }

                return errors;
            }

            function validateImages() {
                const errors = [];

                // Get all unique colors from variants (using color names)
                const variantColors = [];
                $('.variant-item').each(function() {
                    const colorToggle = $(this).find('.color-toggle');
                    // Chỉ lấy màu từ các biến thể không có checkbox "Không màu"
                    if (colorToggle.length > 0 && !colorToggle.is(':checked')) {
                        const colorName = $(this).find('.color-name-input').val().trim();
                        if (colorName && !variantColors.includes(colorName.toLowerCase())) {
                            variantColors.push(colorName.toLowerCase());
                        }
                    }
                });

                // Get all uploaded images with colors
                const imageColors = [];
                let hasGeneralImage = false;
                let hasAnyImage = false;

                $('.product-image-upload-item').each(function() {
                    const fileInput = $(this).find('input[type="file"]')[0];
                    if (fileInput && fileInput.files && fileInput.files.length > 0) {
                        hasAnyImage = true;
                        const colorInput = $(this).find('input[name*="[color]"]:checked');
                        if (colorInput.length > 0 && colorInput.val()) {
                            imageColors.push(colorInput.val().toLowerCase());
                        } else {
                            hasGeneralImage = true;
                        }
                    }
                });

                // Validation logic for images
                if (variantColors.length > 0) {
                    // Has colored variants - must have images for each color
                    const missingColors = variantColors.filter(color =>
                        !imageColors.includes(color)
                    );

                    if (missingColors.length > 0) {
                        errors.push(`Các màu sau cần có ảnh 1: ${missingColors.join(', ')}`);
                    }
                }

                // Luôn phải có ít nhất một ảnh (chung hoặc theo màu)
                if (!hasAnyImage) {
                    errors.push('Bạn phải thêm ít nhất một ảnh cho sản phẩm');
                }

                return errors;
            }

            function showValidationErrors(validationResult) {
                // Group errors by tab
                const basicInfoErrors = validationResult.errors.filter(error =>
                    error.includes('Tên sản phẩm') ||
                    error.includes('trạng thái') ||
                    error.includes('danh mục') ||
                    error.includes('ảnh đại diện') ||
                    error.includes('Mô tả ngắn')
                );

                const variantErrors = validationResult.errors.filter(error =>
                    error.includes('Biến thể') ||
                    error.includes('biến thể')
                );

                const imageErrors = validationResult.errors.filter(error =>
                    error.includes('màu') && error.includes('ảnh')
                );

                // Create HTML content for better formatting
                let htmlContent = '<div class="validation-errors-container">';

                if (basicInfoErrors.length > 0) {
                    htmlContent += `
                        <div class="error-section">
                            <h6 class="error-section-title">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                Thông tin cơ bản
                            </h6>
                            <ul class="error-list">
                    `;
                    basicInfoErrors.forEach(error => {
                        htmlContent +=
                            `<li><i class="fas fa-times-circle text-danger me-1"></i>${error}</li>`;
                    });
                    htmlContent += '</ul></div>';
                }

                if (variantErrors.length > 0) {
                    htmlContent += `
                        <div class="error-section">
                            <h6 class="error-section-title">
                                <i class="fas fa-layer-group text-warning me-2"></i>
                                Biến thể sản phẩm
                            </h6>
                            <ul class="error-list">
                    `;
                    variantErrors.forEach(error => {
                        htmlContent +=
                            `<li><i class="fas fa-times-circle text-danger me-1"></i>${error}</li>`;
                    });
                    htmlContent += '</ul></div>';
                }

                if (imageErrors.length > 0) {
                    htmlContent += `
                        <div class="error-section">
                            <h6 class="error-section-title">
                                <i class="fas fa-images text-info me-2"></i>
                                Hình ảnh sản phẩm
                            </h6>
                            <ul class="error-list">
                    `;
                    imageErrors.forEach(error => {
                        htmlContent +=
                            `<li><i class="fas fa-times-circle text-danger me-1"></i>${error}</li>`;
                    });
                    htmlContent += '</ul></div>';
                }

                htmlContent += '</div>';

                // Add footer message if switching tabs
                let footerMessage = '';
                if (validationResult.errorTab !== currentTab) {
                    footerMessage = `
                        <div class="tab-switch-notice">
                            <i class="fas fa-arrow-right me-1"></i>
                            Đã chuyển đến tab "<strong>${getTabName(validationResult.errorTab)}</strong>" để sửa lỗi
                        </div>
                    `;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Thông tin chưa hợp lệ',
                    html: htmlContent,
                    footer: footerMessage,
                    width: '650px',
                    confirmButtonText: 'Đã hiểu',
                    confirmButtonColor: '#dc3545',
                    customClass: {
                        popup: 'validation-error-popup',
                        title: 'validation-error-title',
                        htmlContainer: 'validation-error-content'
                    },
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    }
                });
            }

            function getTabName(tabId) {
                const tabNames = {
                    'basic-info': 'Thông tin cơ bản',
                    'variants': 'Biến thể sản phẩm',
                    'images': 'Hình ảnh sản phẩm'
                };
                return tabNames[tabId] || tabId;
            }

            function submitForm() {
                $('.error-message').empty();
                $('.input-error').removeClass('input-error');
                $('.nav-link').removeClass('tab-error');
                $('.form-section').removeClass('has-error');
                $('.variant-item').removeClass('has-error');

                const formData = new FormData(document.getElementById('product-form'));
                const submitBtn = $('.save-button');
                const originalBtnText = submitBtn.html();

                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');
                submitBtn.prop('disabled', true);

                $.ajax({
                    url: $('#product-form').attr('action'),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        submitBtn.html(originalBtnText);
                        submitBtn.prop('disabled', false);

                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: response.message || 'Sản phẩm đã được tạo thành công',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect ||
                                "{{ route('admin.products.index') }}";
                        });
                    },
                    error: function(xhr) {
                        submitBtn.html(originalBtnText);
                        submitBtn.prop('disabled', false);

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorTab = 'basic-info';
                            let hasVariantErrors = false;
                            let hasImageErrors = false;

                            $.each(errors, function(field, messages) {
                                console.log('Processing error for field:', field); // Debug log

                                // Xử lý lỗi variant
                                if (field.startsWith('variants.')) {
                                    hasVariantErrors = true;
                                    errorTab = 'variants';

                                    // Parse variant field để lấy index và field name
                                    // Ví dụ: variants.0.price -> index: 0, fieldName: price
                                    const matches = field.match(/variants\.(\d+)\.(.+)/);
                                    if (matches) {
                                        const variantIndex = matches[1];
                                        const fieldName = matches[2];

                                        // Tìm variant element theo index thực tế trong DOM
                                        const variantElement = $(`.variant-item`).eq(
                                            variantIndex);
                                        if (variantElement.length > 0) {
                                            // Tìm input field trong variant này
                                            const fieldInput = variantElement.find(
                                                `[name*="[${fieldName}]"]`);
                                            if (fieldInput.length > 0) {
                                                fieldInput.addClass('input-error');

                                                // Tìm error message container
                                                const errorContainer = variantElement.find(
                                                    `#variants-${variantElement.data('index')}-${fieldName}-error`
                                                );
                                                if (errorContainer.length > 0) {
                                                    errorContainer.text(messages[0]);
                                                }
                                            }

                                            // Mark variant as having error
                                            variantElement.addClass('has-error');
                                        }
                                    }
                                }
                                // Xử lý lỗi image
                                else if (field.startsWith('product_images.')) {
                                    hasImageErrors = true;
                                    if (errorTab === 'basic-info') {
                                        errorTab = 'images';
                                    }

                                    // Parse image field
                                    const matches = field.match(/product_images\.(\d+)\.(.+)/);
                                    if (matches) {
                                        const imageIndex = matches[1];
                                        const fieldName = matches[2];

                                        const imageElement = $(
                                            `.product-image-upload-item[data-index="${imageIndex}"]`
                                        );
                                        if (imageElement.length > 0) {
                                            const fieldInput = imageElement.find(
                                                `[name*="[${fieldName}]"]`);
                                            fieldInput.addClass('input-error');
                                        }
                                    }
                                }
                                // Xử lý lỗi basic info
                                else {
                                    const fieldElement = $(`[name="${field}"]`).first();
                                    const errorElement = $(`#${field}-error`);

                                    if (fieldElement.length > 0) {
                                        fieldElement.addClass('input-error');
                                    }

                                    if (errorElement.length > 0) {
                                        errorElement.text(messages[0]);
                                    }
                                }
                            });

                            // Set error indicators for tabs
                            if (hasVariantErrors) {
                                $('#variants-tab').addClass('tab-error');
                            }
                            if (hasImageErrors) {
                                $('#images-tab').addClass('tab-error');
                            }

                            // Switch to error tab if different from current
                            if (errorTab !== currentTab) {
                                $(`#${errorTab}-tab`).tab('show');
                                currentTab = errorTab;
                            }

                            // Show summary error message
                            let errorMessage = 'Vui lòng kiểm tra và sửa các lỗi trong form';
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi validation',
                                text: errorMessage,
                                confirmButtonText: 'Đã hiểu'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: xhr.responseJSON?.message ||
                                    'Có lỗi xảy ra, vui lòng thử lại sau.'
                            });
                        }
                    }
                });
            }
        });

        // Variant functions
        function addVariant() {
            $.ajax({
                url: "{{ route('admin.products.get-variant-component') }}",
                type: 'GET',
                data: {
                    index: variantCount,
                    variant: {}
                },
                success: function(html) {
                    $('#variants-container').append(html);
                    variantCount++;
                    $('.remove-variant-btn').removeClass('d-none');
                    updateAllImageColorOptions();
                },
                error: function() {
                    addVariantFallback();
                }
            });
        }

        function addVariantFallback() {
            const container = $('#variants-container');
            const newVariant = $(`
                <div class="variant-item mb-3 form-card p-2" data-index="${variantCount}">
                    <div class="card-header d-flex justify-content-between align-items-center py-2">
                        <h6 class="mb-0">Biến thể #${variantCount + 1}</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-variant-btn ${$('.variant-item').length === 0 ? 'd-none' : ''}" onclick="removeVariant(${variantCount})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="form-label">Kích thước</label>
                                <input type="text" name="variants[${variantCount}][size]" class="custom-input" placeholder="Ví dụ: S, M, L, XL">
                                <div class="error-message" id="variants-${variantCount}-size-error"></div>
                            </div>
                            
                            <div class="form-group col-md-4">
                                <label class="form-label">Màu sắc</label>
                                <div class="color-input-group">
                                    <div class="color-toggle-wrapper mb-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input color-toggle" type="checkbox" id="colorToggle${variantCount}">
                                            <label class="form-check-label" for="colorToggle${variantCount}">Không màu</label>
                                        </div>
                                    </div>
                                    <div class="color-inputs">
                                        <input type="color" name="variants[${variantCount}][color]" class="color-picker color-input" value="" title="Chọn màu">
                                        <input type="text" name="variants[${variantCount}][color_name]" class="custom-input color-name-input" placeholder="Tên màu (VD: Đỏ, Xanh)">
                                    </div>
                                </div>
                                <small class="form-text text-muted">Chọn màu và nhập tên màu hiển thị, hoặc chọn "Không màu"</small>
                                <div class="error-message" id="variants-${variantCount}-color_name-error"></div>
                            </div>
                            
                            <div class="form-group col-md-4">
                                <label class="form-label required">Trạng thái <span class="required-asterisk">*</span></label>
                                <select name="variants[${variantCount}][status]" class="custom-input">
                                    <option value="">Chọn trạng thái</option>
                                    <option value="active" selected>Hoạt động</option>
                                    <option value="inactive">Không hoạt động</option>
                                </select>
                                <div class="error-message" id="variants-${variantCount}-status-error"></div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="form-label required">Giá bán <span class="required-asterisk">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="variants[${variantCount}][price]" class="custom-input" placeholder="0.00" min="0" step="0.01">
                                </div>
                                <div class="error-message" id="variants-${variantCount}-price-error"></div>
                            </div>
                            
                            <div class="form-group col-md-4">
                                <label class="form-label required">Số lượng <span class="required-asterisk">*</span></label>
                                <input type="number" name="variants[${variantCount}][quantity]" class="custom-input" placeholder="0" min="0">
                                <div class="error-message" id="variants-${variantCount}-quantity-error"></div>
                            </div>
                            
                            <div class="form-group col-md-4">
                                <label class="form-label">SKU</label>
                                <input type="text" name="variants[${variantCount}][sku]" class="custom-input" placeholder="Mã SKU (tùy chọn)">
                                <div class="error-message" id="variants-${variantCount}-sku-error"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `);

            container.append(newVariant);

            // Xử lý color toggle
            $(`#colorToggle${variantCount}`).on('change', function() {
                const colorInputs = $(this).closest('.color-input-group').find('.color-inputs');
                const colorPicker = colorInputs.find('.color-picker');
                const colorName = colorInputs.find('.color-name-input');

                if ($(this).is(':checked')) {
                    // Lưu giá trị cũ trước khi xóa
                    colorPicker.data('old-value', colorPicker.val());
                    colorName.data('old-value', colorName.val());

                    // Xóa giá trị
                    colorPicker.val('');
                    colorName.val('');
                    colorInputs.hide();
                } else {
                    // Khôi phục giá trị cũ nếu có
                    if (colorPicker.data('old-value')) {
                        colorPicker.val(colorPicker.data('old-value'));
                    }
                    if (colorName.data('old-value')) {
                        colorName.val(colorName.data('old-value'));
                    }
                    colorInputs.show();
                }

                updateAllImageColorOptions();
            });

            variantCount++;

            // Hiển thị nút xóa cho tất cả variants nếu có nhiều hơn 1
            if ($('.variant-item').length > 1) {
                $('.remove-variant-btn').removeClass('d-none');
            }

            updateAllImageColorOptions();
        }

        function removeVariant(index) {
            $(`.variant-item[data-index="${index}"]`).remove();

            if ($('.variant-item').length === 1) {
                $('.remove-variant-btn').addClass('d-none');
            }

            $('.variant-item').each(function(i) {
                $(this).find('.card-header h6').text(`Biến thể #${i + 1}`);
            });

            updateAllImageColorOptions();
        }

        function updateAllImageColorOptions() {
            const variantColors = [];

            $('.variant-item').each(function() {
                const colorToggle = $(this).find('.color-toggle');
                // Chỉ lấy màu từ các biến thể không có checkbox "Không màu"
                if (colorToggle.length > 0 && !colorToggle.is(':checked')) {
                    const colorName = $(this).find('.color-name-input').val().trim();
                    if (colorName && !variantColors.includes(colorName)) {
                        variantColors.push(colorName);
                    }
                }
            });

            // Cập nhật color options cho mỗi ảnh
            $('.product-image-upload-item').each(function() {
                const imageIndex = $(this).data('index');
                updateImageColorOptions(imageIndex);
            });
        }

        function updateImageColorOptions(imageIndex) {
            // Get all unique colors from variants (using color names)
            const variantColors = [];
            $('.variant-item .color-name-input').each(function() {
                const colorName = $(this).val().trim();
                if (colorName && !variantColors.includes(colorName)) {
                    variantColors.push(colorName);
                }
            });

            $.ajax({
                url: "{{ route('admin.products.get-image-color-options') }}",
                type: 'GET',
                data: {
                    index: imageIndex,
                    colors: variantColors,
                    currentSelected: $(
                            `.product-image-upload-item[data-index="${imageIndex}"] input[name*="[color]"]:checked`)
                        .val()
                },
                success: function(html) {
                    $(`.product-image-upload-item[data-index="${imageIndex}"] .color-selector-group`).html(
                        html);
                },
                error: function() {
                    updateImageColorOptionsFallback(imageIndex);
                }
            });
        }

        function updateImageColorOptionsFallback(imageIndex) {
            const colorContainer = $(`.product-image-upload-item[data-index="${imageIndex}"] .color-selector-group`);
            const currentSelected = colorContainer.find('input[name*="[color]"]:checked').val();

            const variantColors = [];
            const colorData = [];
            $('.variant-item').each(function() {
                const colorName = $(this).find('.color-name-input').val().trim();
                const colorValue = $(this).find('.color-picker').val();
                if (colorName && !variantColors.includes(colorName)) {
                    variantColors.push(colorName);
                    colorData.push({
                        name: colorName,
                        value: colorValue
                    });
                }
            });

            let colorOptions = `
                <div class="color-option no-color-option">
                    <input type="radio" name="product_images[${imageIndex}][color]" value="" id="no_color_${imageIndex}" ${currentSelected === '' || !currentSelected ? 'checked' : ''}>
                    <label for="no_color_${imageIndex}" class="color-label">Ảnh chung (không có màu cụ thể)</label>
                </div>
            `;

            colorData.forEach(color => {
                const colorId = `color_${imageIndex}_${color.name.replace(/\s+/g, '_').toLowerCase()}`;
                colorOptions += `
                    <div class="color-option">
                        <input type="radio" name="product_images[${imageIndex}][color]" value="${color.name}" id="${colorId}" ${currentSelected === color.name ? 'checked' : ''}>
                        <label for="${colorId}" class="color-label">
                            ${color.name}
                            <span class="color-display-swatch" style="background-color: ${color.value};"></span>
                        </label>
                    </div>
                `;
            });

            colorContainer.html(colorOptions);
        }

        // Image functions
        function addImageUpload() {
            const variantColors = [];
            $('.variant-item .color-name-input').each(function() {
                const colorName = $(this).val().trim();
                if (colorName && !variantColors.includes(colorName)) {
                    variantColors.push(colorName);
                }
            });

            $.ajax({
                url: "{{ route('admin.products.get-image-component') }}",
                type: 'GET',
                data: {
                    index: imageCount,
                    availableColors: variantColors
                },
                success: function(html) {
                    $('#product-images-container').append(html);

                    $(`#productImagePreview${imageCount}`).click(function() {
                        $(this).siblings('input[type="file"]').click();
                    });

                    imageCount++;
                },
                error: function() {
                    addImageUploadFallback();
                }
            });
        }

        function addImageUploadFallback() {
            const container = $('#product-images-container');
            const imageItem = $(`
                <div class="product-image-upload-item mb-3" data-index="${imageCount}">
                    <div class="row align-items-start">
                        <div class="col-md-3">
                            <div class="product-image-preview-container">
                                <div class="product-image-preview" id="productImagePreview${imageCount}">
                                    <i class="fas fa-image"></i>
                                    <span>Chọn ảnh</span>
                                </div>
                                <button type="button" class="remove-product-image-btn" onclick="removeImageUpload(${imageCount})">
                                    <i class="fas fa-times"></i>
                                </button>
                                <input type="file" name="product_images[${imageCount}][file]" accept="image/*" class="d-none" onchange="previewProductImage(this, ${imageCount})">
                            </div>
                        </div>
                        
                        <div class="col-md-9">
                            <div class="form-group">
                                <label class="form-label">Chọn màu cho ảnh này:</label>
                                <div class="color-selector-group">
                                    <!-- Color options will be dynamically updated -->
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Chọn "Ảnh chung" nếu ảnh này dùng cho tất cả màu, hoặc chọn màu cụ thể
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            `);

            container.append(imageItem);

            $(`#productImagePreview${imageCount}`).click(function() {
                $(this).siblings('input[type="file"]').click();
            });

            updateImageColorOptions(imageCount);
            imageCount++;
        }

        function removeImageUpload(index) {
            $(`.product-image-upload-item[data-index="${index}"]`).remove();
        }

        function previewProductImage(input, index) {
            if (input.files && input.files[0]) {
                const file = input.files[0];

                if (!file.type.startsWith('image/')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi định dạng',
                        text: 'Vui lòng chọn tệp hình ảnh (JPG, PNG, JPEG)'
                    });
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    $(`#productImagePreview${index}`).css('background-image', `url('${e.target.result}')`);
                    $(`#productImagePreview${index}`).addClass('has-image');
                    $(`#productImagePreview${index}`).find('i, span').hide();
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
@endpush
