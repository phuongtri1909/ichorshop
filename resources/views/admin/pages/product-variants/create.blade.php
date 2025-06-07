@extends('admin.layouts.sidebar')

@section('title', 'Thêm biến thể sản phẩm')

@section('main-content')
    <div class="product-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                @if (request('product_id'))
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('admin.products.edit', request('product_id')) }}">{{ $selectedProduct->name ?? 'Sản phẩm' }}</a>
                    </li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('admin.product-variants.index', ['product_id' => request('product_id')]) }}">Biến
                            thể</a></li>
                @else
                    <li class="breadcrumb-item"><a href="{{ route('admin.product-variants.index') }}">Biến thể sản phẩm</a>
                    </li>
                @endif
                <li class="breadcrumb-item current">Thêm mới</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-plus icon-title"></i>
                    <h5>Thêm biến thể sản phẩm</h5>
                </div>
            </div>
            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs mb-4" id="variantTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active title-tab-primary-5 color-primary-hover" id="variant-info-tab"
                            data-bs-toggle="tab" data-bs-target="#variant-info" type="button" role="tab">
                            <i class="fas fa-info-circle me-1"></i>
                            Thông tin biến thể
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link title-tab-primary-5 color-primary-hover" id="variant-images-tab"
                            data-bs-toggle="tab" data-bs-target="#variant-images" type="button" role="tab">
                            <i class="fas fa-images me-1"></i>
                            Hình ảnh biến thể
                        </button>
                    </li>
                </ul>

                <form action="{{ route('admin.product-variants.store') }}" method="POST" class="variant-form"
                    enctype="multipart/form-data" id="variant-form">
                    @csrf

                    <!-- Tab Content -->
                    <div class="tab-content" id="variantTabsContent">
                        <!-- Tab 1: Thông tin biến thể -->
                        <div class="tab-pane fade show active" id="variant-info" role="tabpanel">
                            <div class="form-section">
                                <h6 class="section-title">Thông tin cơ bản</h6>

                                <div class="form-group">
                                    <label for="product_id" class="form-label required">
                                        Sản phẩm <span class="required-asterisk">*</span>
                                    </label>
                                    <select id="product_id" name="product_id" class="custom-input" required>
                                        <option value="">Chọn sản phẩm</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ old('product_id', request('product_id')) == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="error-message" id="product_id-error"></div>
                                </div>

                                <div class="form-group">
                                    <label for="sku" class="form-label">SKU</label>
                                    <input type="text" id="sku" name="sku" class="custom-input"
                                        placeholder="Mã SKU (tùy chọn)" value="{{ old('sku') }}">
                                    <div class="error-message" id="sku-error"></div>
                                    <small class="form-text">Để trống sẽ tự động tạo SKU</small>
                                </div>
                            </div>

                            <div class="form-section">
                                <h6 class="section-title">Thuộc tính biến thể</h6>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="size" class="form-label">Kích thước</label>
                                        <input type="text" id="size" name="size" class="custom-input"
                                            placeholder="Ví dụ: S, M, L, XL" value="{{ old('size') }}">
                                        <div class="error-message" id="size-error"></div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="status" class="form-label required">
                                            Trạng thái <span class="required-asterisk">*</span>
                                        </label>
                                        <select id="status" name="status" class="custom-input" required>
                                            <option value="">Chọn trạng thái</option>
                                            <option value="active"
                                                {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Hoạt động
                                            </option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                                Không hoạt động</option>
                                        </select>
                                        <div class="error-message" id="status-error"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="color_name" class="form-label">Màu sắc</label>
                                    <div class="color-input-group">
                                        <input type="color" id="color" name="color" class="color-picker"
                                            value="{{ old('color', '#000000') }}" title="Chọn màu">
                                        <input type="text" id="color_name" name="color_name"
                                            class="custom-input color-name-input" placeholder="Tên màu (VD: Đỏ, Xanh)"
                                            value="{{ old('color_name') }}">
                                    </div>
                                    <div class="error-message" id="color_name-error"></div>
                                    <small class="form-text">Chọn màu và nhập tên màu hiển thị</small>
                                </div>
                            </div>

                            <div class="form-section">
                                <h6 class="section-title">Giá và tồn kho</h6>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="price" class="form-label required">
                                            Giá bán <span class="required-asterisk">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" id="price" name="price" class="custom-input"
                                                placeholder="0.00" value="{{ old('price') }}" min="0"
                                                step="0.01" required>
                                        </div>
                                        <div class="error-message" id="price-error"></div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="quantity" class="form-label required">
                                            Số lượng tồn kho <span class="required-asterisk">*</span>
                                        </label>
                                        <input type="number" id="quantity" name="quantity" class="custom-input"
                                            placeholder="0" value="{{ old('quantity') }}" min="0" required>
                                        <div class="error-message" id="quantity-error"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Hình ảnh biến thể -->
                        <div class="tab-pane fade" id="variant-images" role="tabpanel">
                            <div class="form-section">
                                <h6 class="section-title">Hình ảnh cho biến thể</h6>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Hướng dẫn:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Nếu biến thể có màu mà <strong>chưa có ảnh nào</strong>, bắt buộc phải upload
                                            ảnh</li>
                                        <li>Ảnh chung (không màu) có thể dùng cho mọi biến thể</li>
                                        <li>Ảnh có màu chỉ hiển thị cho biến thể có màu tương ứng</li>
                                    </ul>
                                </div>

                                <!-- Existing Images Preview -->
                                @if ($productImages && $productImages->count() > 0)
                                    <div class="form-group">
                                        <label class="form-label">Ảnh hiện có của sản phẩm</label>
                                        <div class="existing-images-preview">
                                            <div class="row" id="current-product-images">
                                                @foreach ($productImages as $image)
                                                    <div class="col-md-2 mb-3">
                                                        <div class="image-preview-item {{ !$image->color ? 'general-image' : 'color-image' }}"
                                                            data-color="{{ $image->color }}">
                                                            <img src="{{ Storage::url($image->image_path_medium ?? $image->image_path) }}"
                                                                alt="Product Image" class="img-fluid">
                                                            <div class="image-color-tag">
                                                                @if ($image->color)
                                                                    <span class="color-tag">{{ $image->color }}</span>
                                                                @else
                                                                    <span class="color-tag general">Ảnh chung</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div id="image-requirement-notice" class="alert alert-light mt-2"
                                            style="display: none;">
                                            <i class="fas fa-lightbulb me-1"></i>
                                            <span id="image-requirement-text"></span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Add New Images for Variant -->
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0">Thêm ảnh mới cho biến thể này</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            id="addVariantImageBtn">
                                            <i class="fas fa-plus me-1"></i> Thêm ảnh
                                        </button>
                                    </div>

                                    <div id="variant-images-container">
                                        <!-- Ảnh mới sẽ được thêm động ở đây -->
                                    </div>
                                    <div class="error-message" id="variant-images-error"></div>

                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Ảnh sẽ được gắn với màu của biến thể hoặc là ảnh chung nếu không có màu.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        @if (request('product_id'))
                            <a href="{{ route('admin.product-variants.index', ['product_id' => request('product_id')]) }}"
                                class="back-button">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        @else
                            <a href="{{ route('admin.product-variants.index') }}" class="back-button">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        @endif
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Tạo biến thể
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Copy styles từ product create */
        .color-input-group {
            display: flex;
            align-items: stretch;
            gap: 8px;
        }

        .color-picker {
            width: 50px;
            height: 42px;
            border: 1px solid #ddd;
            border-radius: 6px 0 0 6px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .color-picker::-webkit-color-swatch-wrapper {
            padding: 0;
            border-radius: 4px;
        }

        .color-picker::-webkit-color-swatch {
            border: none;
            border-radius: 4px;
        }

        .color-name-input {
            flex: 1;
            border-radius: 0 6px 6px 0 !important;
            margin-left: 0 !important;
        }

        .input-group {
            display: flex;
            align-items: stretch;
            flex-wrap: nowrap;
        }

        .input-group-text {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-right: none;
            padding: 10px 12px;
            border-radius: 6px 0 0 6px;
            color: #666;
            font-weight: 500;
        }

        .input-group .custom-input {
            border-radius: 0 6px 6px 0 !important;
            border-left: none;
        }

        /* Variant Image Upload Styles */
        .variant-image-upload-item {
            border: 1px solid #e0e6ed;
            border-radius: 8px;
            background: #fff;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .variant-image-upload-item:hover {
            border-color: #007bff;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
        }

        .variant-image-preview-container {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .variant-image-preview {
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

        .variant-image-preview:hover {
            border-color: #007bff;
            background-color: #e3f2fd;
        }

        .variant-image-preview.has-image {
            border: 2px solid #28a745;
        }

        .variant-image-preview.has-image i,
        .variant-image-preview.has-image span {
            display: none !important;
        }

        .variant-image-preview i {
            font-size: 24px;
            color: #6c757d;
            margin-bottom: 8px;
        }

        .variant-image-preview span {
            font-size: 12px;
            color: #6c757d;
            text-align: center;
            padding: 0 10px;
        }

        .remove-variant-image-btn {
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

        .remove-variant-image-btn:hover {
            background: #c82333;
            transform: scale(1.1);
        }

        .variant-color-info {
            background: #f8f9fa;
            border: 1px solid #e0e6ed;
            border-radius: 6px;
            padding: 15px;
            margin-top: 10px;
        }

        .color-display {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .color-swatch-display {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px #ddd;
        }

        .color-name-display {
            font-weight: 500;
            color: #495057;
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
    </style>
@endpush

@push('scripts')
    <script>
        let variantImageCount = 0;
        let currentTab = 'variant-info';

        $(document).ready(function() {
            // Remove HTML5 validation
            $('input[required], select[required]').removeAttr('required');

            // Track current active tab
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                currentTab = e.target.getAttribute('data-bs-target').replace('#', '');
            });

            // Add variant image button
            $('#addVariantImageBtn').click(function() {
                addVariantImageUpload();
            });

            // Handle color changes - update image info
            $('#color, #color_name').on('input change', function() {
                updateVariantImageColorInfo();
            });

            // Handle variant image click - delegated event
            $(document).on('click', '.variant-image-preview', function() {
                const index = $(this).closest('.variant-image-upload-item').data('index');
                selectVariantImage(index);
            });

            // Handle variant image file change - delegated event
            $(document).on('change', '.variant-image-upload-item input[type="file"]', function() {
                const index = $(this).closest('.variant-image-upload-item').data('index');
                previewVariantImage(this, index);
            });

            // Handle remove variant image - delegated event
            $(document).on('click', '.remove-variant-image-btn', function() {
                const index = $(this).closest('.variant-image-upload-item').data('index');
                removeVariantImage(index);
            });

            // AJAX form submission
            $('#variant-form').submit(function(e) {
                e.preventDefault();
                e.stopPropagation();

                const validationResult = validateVariantForm();
                if (!validationResult.isValid) {
                    markTabsWithErrors(validationResult);

                    if (validationResult.errorTab && validationResult.errorTab !== currentTab) {
                        $(`#${validationResult.errorTab}-tab`).tab('show');
                        currentTab = validationResult.errorTab;
                    }
                    return false;
                }

                submitForm();
                return false;
            });

            // Check existing images for color when product changes
            $('#product_id').change(function() {
                checkExistingImagesForProduct();
            });

            // Check when color changes
            $('#color_name').on('input change', function() {
                checkColorImageRequirement();
            });
        });

        function addVariantImageUpload() {
            const variantColor = $('#color_name').val().trim();
            const colorValue = $('#color').val();

            let colorInfoHtml = '';
            if (variantColor) {
                colorInfoHtml = `
                <div class="variant-color-info">
                    <label class="form-label">Màu của ảnh này:</label>
                    <div class="color-display">
                        <span class="color-swatch-display" style="background-color: ${colorValue}"></span>
                        <span class="color-name-display">${variantColor}</span>
                    </div>
                    <input type="hidden" name="variant_images[${variantImageCount}][color]" value="${variantColor}">
                </div>
            `;
            } else {
                colorInfoHtml = `
                <div class="variant-color-info">
                    <label class="form-label">Ảnh chung (không có màu cụ thể)</label>
                    <input type="hidden" name="variant_images[${variantImageCount}][color]" value="">
                </div>
            `;
            }

            const newImageUpload = $(`
            <div class="variant-image-upload-item" data-index="${variantImageCount}">
                <div class="row position-relative">
                    <div class="col-md-4">
                        <div class="variant-image-preview-container">
                            <div class="variant-image-preview">
                                <i class="fas fa-plus"></i>
                                <span>Chọn ảnh</span>
                            </div>
                            
                            <input type="file" name="variant_images[${variantImageCount}][file]" accept="image/*" style="display: none;">
                        </div>
                    </div>
                    <div class="col-md-8">
                        ${colorInfoHtml}
                    </div>
                    <button type="button" class="remove-variant-image-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `);

            $('#variant-images-container').append(newImageUpload);
            variantImageCount++;
        }

        function selectVariantImage(index) {
            const fileInput = $(`.variant-image-upload-item[data-index="${index}"] input[type="file"]`);
            if (fileInput.length > 0) {
                fileInput.click();
            }
        }

        function previewVariantImage(input, index) {
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
                    const preview = $(`.variant-image-upload-item[data-index="${index}"] .variant-image-preview`);
                    preview.css('background-image', `url('${e.target.result}')`);
                    preview.addClass('has-image');
                    preview.find('i, span').hide();
                }
                reader.readAsDataURL(file);
            }
        }

        function removeVariantImage(index) {
            $(`.variant-image-upload-item[data-index="${index}"]`).remove();
        }

        function updateVariantImageColorInfo() {
            const variantColor = $('#color_name').val().trim();
            const colorValue = $('#color').val();

            $('.variant-image-upload-item').each(function() {
                const index = $(this).data('index');
                let colorInfoHtml = '';

                if (variantColor) {
                    colorInfoHtml = `
                    <div class="variant-color-info">
                        <label class="form-label">Màu của ảnh này:</label>
                        <div class="color-display">
                            <span class="color-swatch-display" style="background-color: ${colorValue}"></span>
                            <span class="color-name-display">${variantColor}</span>
                        </div>
                        <input type="hidden" name="variant_images[${index}][color]" value="${variantColor}">
                    </div>
                `;
                } else {
                    colorInfoHtml = `
                    <div class="variant-color-info">
                        <label class="form-label">Ảnh chung (không có màu cụ thể)</label>
                        <input type="hidden" name="variant_images[${index}][color]" value="">
                    </div>
                `;
                }

                $(this).find('.col-md-8').html(colorInfoHtml);
            });
        }

        function validateVariantForm() {
            let validationResult = {
                isValid: true,
                errorTab: null,
                errors: []
            };

            // Clear previous errors
            $('.error-message').empty();
            $('.input-error').removeClass('input-error');
            $('.nav-link').removeClass('tab-error');

            // Validate variant info tab
            const variantInfoErrors = validateVariantInfo();
            if (variantInfoErrors.length > 0) {
                validationResult.isValid = false;
                if (!validationResult.errorTab) {
                    validationResult.errorTab = 'variant-info';
                }
                validationResult.errors = validationResult.errors.concat(variantInfoErrors);
            }

            // Validate images tab
            const imageErrors = validateVariantImages();
            if (imageErrors.length > 0) {
                validationResult.isValid = false;
                if (!validationResult.errorTab) {
                    validationResult.errorTab = 'variant-images';
                }
                validationResult.errors = validationResult.errors.concat(imageErrors);
            }

            // Show errors
            if (!validationResult.isValid) {
                showValidationErrors(validationResult);
            }

            return validationResult;
        }

        function validateVariantInfo() {
            const errors = [];

            // Validate product
            const productId = $('#product_id').val();
            if (!productId) {
                $('#product_id').addClass('input-error');
                $('#product_id-error').text('Vui lòng chọn sản phẩm');
                errors.push('Vui lòng chọn sản phẩm');
            }

            // Validate status
            const status = $('#status').val();
            if (!status) {
                $('#status').addClass('input-error');
                $('#status-error').text('Vui lòng chọn trạng thái');
                errors.push('Vui lòng chọn trạng thái');
            }

            // Validate price
            const price = $('#price').val();
            if (!price || parseFloat(price) < 0) {
                $('#price').addClass('input-error');
                $('#price-error').text('Giá bán phải lớn hơn hoặc bằng 0');
                errors.push('Giá bán phải lớn hơn hoặc bằng 0');
            }

            // Validate quantity
            const quantity = $('#quantity').val();
            if (quantity === '' || parseInt(quantity) < 0) {
                $('#quantity').addClass('input-error');
                $('#quantity-error').text('Số lượng phải lớn hơn hoặc bằng 0');
                errors.push('Số lượng phải lớn hơn hoặc bằng 0');
            }

            return errors;
        }

        function validateVariantImages() {
            const errors = [];
            const variantColor = $('#color_name').val().trim();

            if (variantColor) {
                // Check if this color has existing images in the product
                const hasExistingImages = window.existingProductImages &&
                    (window.existingProductImages.includes(variantColor) ||
                        window.existingProductImages.includes(null) ||
                        window.existingProductImages.includes(''));

                // If no existing images, must have new images
                if (!hasExistingImages) {
                    let hasNewImages = false;
                    $('.variant-image-upload-item').each(function() {
                        const fileInput = $(this).find('input[type="file"]')[0];
                        if (fileInput && fileInput.files && fileInput.files.length > 0) {
                            hasNewImages = true;
                        }
                    });

                    if (!hasNewImages) {
                        $('#variant-images-error').text(
                            `Màu "${variantColor}" chưa có ảnh nào. Bắt buộc phải upload ít nhất 1 ảnh cho màu này hoặc ảnh chung.`
                            );
                        errors.push(
                            `Màu "${variantColor}" chưa có ảnh nào. Bắt buộc phải upload ít nhất 1 ảnh cho màu này hoặc ảnh chung.`
                            );
                    }
                }
            }

            return errors;
        }

        function markTabsWithErrors(validationResult) {
            // Clear all error indicators first
            $('.nav-link').removeClass('tab-error');

            // Add error indicators to tabs with errors
            if (validationResult.errorTab) {
                $(`#${validationResult.errorTab}-tab`).addClass('tab-error');
            }

            // Check other tabs for errors too
            const variantInfoErrors = validationResult.errors.filter(error =>
                error.includes('sản phẩm') ||
                error.includes('trạng thái') ||
                error.includes('Giá bán') ||
                error.includes('Số lượng')
            );

            const imageErrors = validationResult.errors.filter(error =>
                error.includes('ảnh')
            );

            if (variantInfoErrors.length > 0) {
                $('#variant-info-tab').addClass('tab-error');
            }
            if (imageErrors.length > 0) {
                $('#variant-images-tab').addClass('tab-error');
            }
        }

        function showValidationErrors(validationResult) {
            // Create HTML content for better formatting
            let htmlContent = '<div class="validation-errors-container">';

            validationResult.errors.forEach(error => {
                htmlContent +=
                    `<div class="error-item"><i class="fas fa-times-circle text-danger me-1"></i>${error}</div>`;
            });

            htmlContent += '</div>';

            // Add footer message if switching tabs
            let footerMessage = '';
            if (validationResult.errorTab !== currentTab) {
                const tabNames = {
                    'variant-info': 'Thông tin biến thể',
                    'variant-images': 'Hình ảnh biến thể'
                };
                footerMessage = `
                <div class="tab-switch-notice">
                    <i class="fas fa-arrow-right me-1"></i>
                    Đã chuyển đến tab "<strong>${tabNames[validationResult.errorTab]}</strong>" để sửa lỗi
                </div>
            `;
            }

            Swal.fire({
                icon: 'error',
                title: 'Thông tin chưa hợp lệ',
                html: htmlContent,
                footer: footerMessage,
                width: '500px',
                confirmButtonText: 'Đã hiểu',
                confirmButtonColor: '#dc3545'
            });
        }

        function submitForm() {
            // Clear previous errors
            $('.error-message').empty();
            $('.input-error').removeClass('input-error');

            const formData = new FormData(document.getElementById('variant-form'));
            const submitBtn = $('.save-button');
            const originalBtnText = submitBtn.html();

            // Disable button and show loading state
            submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');
            submitBtn.prop('disabled', true);

            $.ajax({
                url: $('#variant-form').attr('action'),
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
                        text: response.message || 'Biến thể sản phẩm đã được tạo thành công',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect after success
                        window.location.href = response.redirect ||
                            "{{ route('admin.product-variants.index') }}";
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
                            text: xhr.responseJSON?.message || 'Có lỗi xảy ra, vui lòng thử lại sau.'
                        });
                    }
                }
            });
        }

        // Check existing images for color when product changes
        $('#product_id').change(function() {
            checkExistingImagesForProduct();
        });

        // Check when color changes
        $('#color_name').on('input change', function() {
            checkColorImageRequirement();
        });

        function checkExistingImagesForProduct() {
            const productId = $('#product_id').val();
            if (!productId) return;

            // AJAX call to get existing images for product
            $.ajax({
                url: "{{ route('admin.products.get-existing-images') }}", // Tạo route mới
                type: 'GET',
                data: {
                    product_id: productId
                },
                success: function(response) {
                    window.existingProductImages = response.colors || [];
                    checkColorImageRequirement();
                }
            });
        }

        function checkColorImageRequirement() {
            const variantColor = $('#color_name').val().trim();
            const alertContainer = $('#color-image-requirement-alert');

            // Remove existing alert
            alertContainer.remove();

            if (variantColor) {
                // Check if this color has existing images
                const hasExistingImages = window.existingProductImages &&
                    (window.existingProductImages.includes(variantColor) ||
                        window.existingProductImages.includes(null) ||
                        window.existingProductImages.includes(''));

                if (!hasExistingImages) {
                    // Show requirement alert
                    const alertHtml = `
                    <div id="color-image-requirement-alert" class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>Chú ý:</strong> Màu "<strong>${variantColor}</strong>" chưa có ảnh nào trong sản phẩm này. 
                        Bạn <strong>bắt buộc</strong> phải upload ít nhất 1 ảnh cho màu này hoặc ảnh chung ở tab "Hình ảnh biến thể".
                    </div>
                `;
                    $('#color_name').closest('.form-group').after(alertHtml);

                    // Auto switch to images tab and add image if no images exist
                    if ($('.variant-image-upload-item').length === 0) {
                        $('#variant-images-tab').addClass('tab-require-attention');
                    }
                }
            }
        }

        // Update validation function
        function validateVariantImages() {
            const errors = [];
            const variantColor = $('#color_name').val().trim();

            if (variantColor) {
                // Check if this color has existing images in the product
                const hasExistingImages = window.existingProductImages &&
                    (window.existingProductImages.includes(variantColor) ||
                        window.existingProductImages.includes(null) ||
                        window.existingProductImages.includes(''));

                // If no existing images, must have new images
                if (!hasExistingImages) {
                    let hasNewImages = false;
                    $('.variant-image-upload-item').each(function() {
                        const fileInput = $(this).find('input[type="file"]')[0];
                        if (fileInput && fileInput.files && fileInput.files.length > 0) {
                            hasNewImages = true;
                        }
                    });

                    if (!hasNewImages) {
                        $('#variant-images-error').text(
                            `Màu "${variantColor}" chưa có ảnh nào. Bắt buộc phải upload ít nhất 1 ảnh cho màu này hoặc ảnh chung.`
                            );
                        errors.push(
                            `Màu "${variantColor}" chưa có ảnh nào. Bắt buộc phải upload ít nhất 1 ảnh cho màu này hoặc ảnh chung.`
                            );
                    }
                }
            }

            return errors;
        }

        window.existingProductColors = @json($existingProductColors ?? []);
        window.productImages = @json($productImages ?? []);

        $(document).ready(function() {
            // Check image requirements when color changes
            $('#color_name').on('input change', function() {
                checkImageRequirements();
                updateImagePreviews();
            });

            // Check when product changes (for create)
            $('#product_id').change(function() {
                loadProductImages();
            });

            // Initial check
            checkImageRequirements();
            updateImagePreviews();
        });

        function checkImageRequirements() {
            const variantColor = $('#color_name').val().trim();
            const noticeElement = $('#image-requirement-notice');
            const noticeText = $('#image-requirement-text');

            if (!variantColor) {
                noticeElement.hide();
                return;
            }

            // Check if color has existing images
            const hasColorImages = hasImagesForColor(variantColor);
            const hasGeneralImages = hasImagesForColor(null);

            if (!hasColorImages && !hasGeneralImages) {
                // No images for this color - REQUIRED to upload
                noticeElement.removeClass('alert-light alert-warning').addClass('alert-danger').show();
                noticeText.html(`
            <strong>Bắt buộc upload ảnh!</strong> Màu "<strong>${variantColor}</strong>" chưa có ảnh nào. 
            Bạn phải upload ít nhất 1 ảnh cho màu này hoặc ảnh chung.
        `);
                $('#variant-images-tab').addClass('tab-error');
            } else if (!hasColorImages && hasGeneralImages) {
                // Has general images but no color-specific images
                noticeElement.removeClass('alert-light alert-danger').addClass('alert-warning').show();
                noticeText.html(`
            <strong>Khuyến nghị:</strong> Màu "<strong>${variantColor}</strong>" chỉ có ảnh chung. 
            Bạn có thể upload ảnh riêng cho màu này để hiển thị tốt hơn.
        `);
                $('#variant-images-tab').removeClass('tab-error');
            } else {
                // Has images for this color
                noticeElement.removeClass('alert-danger alert-warning').addClass('alert-light').show();
                noticeText.html(`
            <strong>Tốt!</strong> Màu "<strong>${variantColor}</strong>" đã có ảnh hiển thị.
        `);
                $('#variant-images-tab').removeClass('tab-error');
            }
        }

        function hasImagesForColor(color) {
            if (!window.productImages) return false;

            return window.productImages.some(img => {
                if (color === null) {
                    return img.color === null || img.color === '';
                }
                return img.color === color;
            });
        }

        function updateImagePreviews() {
            const variantColor = $('#color_name').val().trim();

            $('.image-preview-item').each(function() {
                const imageColor = $(this).data('color');

                if (!variantColor) {
                    // No color selected - show all images
                    $(this).removeClass('highlight-relevant dim-irrelevant');
                } else if (!imageColor || imageColor === variantColor) {
                    // Show relevant images (general or matching color)
                    $(this).addClass('highlight-relevant').removeClass('dim-irrelevant');
                } else {
                    // Dim irrelevant images
                    $(this).addClass('dim-irrelevant').removeClass('highlight-relevant');
                }
            });
        }

        // Enhanced validation for variants
        function validateVariantImages() {
            const errors = [];
            const variantColor = $('#color_name').val().trim();

            if (variantColor) {
                const hasColorImages = hasImagesForColor(variantColor);
                const hasGeneralImages = hasImagesForColor(null);

                // Check new images being uploaded
                let hasNewImages = false;
                $('.variant-image-upload-item').each(function() {
                    const fileInput = $(this).find('input[type="file"]')[0];
                    if (fileInput && fileInput.files && fileInput.files.length > 0) {
                        hasNewImages = true;
                    }
                });

                // If no existing images and no new images - ERROR
                if (!hasColorImages && !hasGeneralImages && !hasNewImages) {
                    $('#variant-images-error').text(
                        `Màu "${variantColor}" chưa có ảnh nào. Bắt buộc phải upload ít nhất 1 ảnh.`);
                    errors.push(`Màu "${variantColor}" chưa có ảnh nào. Bắt buộc phải upload ít nhất 1 ảnh.`);
                }
            }

            return errors;
        }

        // For edit page - check deletion impact
        function checkDeletionImpact() {
            const variantColor = $('#color_name').val().trim();
            if (!variantColor) return;

            const warningElement = $('#image-deletion-warning');
            const warningText = $('#deletion-warning-text');

            // Count remaining images after deletion
            let remainingColorImages = 0;
            let remainingGeneralImages = 0;

            $('.existing-image-item:not(.marked-for-deletion)').each(function() {
                const imageColor = $(this).data('color');
                if (!imageColor) {
                    remainingGeneralImages++;
                } else if (imageColor === variantColor) {
                    remainingColorImages++;
                }
            });

            // Check new images
            let newImages = $('.variant-image-upload-item').length;

            if (remainingColorImages === 0 && remainingGeneralImages === 0 && newImages === 0) {
                warningElement.removeClass('alert-warning').addClass('alert-danger').show();
                warningText.text(`Cảnh báo: Màu "${variantColor}" sẽ không còn ảnh nào sau khi xóa!`);
            } else if (remainingColorImages === 0 && remainingGeneralImages > 0) {
                warningElement.removeClass('alert-danger').addClass('alert-warning').show();
                warningText.text(`Lưu ý: Màu "${variantColor}" sẽ chỉ còn ảnh chung sau khi xóa.`);
            } else {
                warningElement.hide();
            }
        }

        // Enhanced delete existing image handler for edit
        $(document).on('click', '.delete-existing-image', function() {
            const imageId = $(this).data('image-id');
            const imageItem = $(this).closest('.existing-image-item');

            if (imageItem.hasClass('marked-for-deletion')) {
                // Unmark for deletion
                imageItem.removeClass('marked-for-deletion');
                imageItem.find('input[name="delete_images[]"]').prop('disabled', true).val('');
                $(this).html('<i class="fas fa-trash"></i>');
            } else {
                // Mark for deletion
                imageItem.addClass('marked-for-deletion');
                imageItem.find('input[name="delete_images[]"]').prop('disabled', false).val(imageId);
                $(this).html('<i class="fas fa-undo"></i> Hoàn tác');
            }

            // Check deletion impact
            checkDeletionImpact();
        });
    </script>
@endpush
