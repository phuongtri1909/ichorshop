@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa biến thể sản phẩm')

@section('main-content')
    <div class="product-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('admin.products.edit', $productVariant->product_id) }}">{{ $productVariant->product->name }}</a>
                </li>
                <li class="breadcrumb-item"><a
                        href="{{ route('admin.product-variants.index', ['product_id' => $productVariant->product_id]) }}">Biến
                        thể</a></li>
                <li class="breadcrumb-item current">Chỉnh sửa</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-edit icon-title"></i>
                    <h5>Chỉnh sửa biến thể: {{ $productVariant->product->name }}</h5>
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

                <form action="{{ route('admin.product-variants.update', $productVariant) }}" method="POST"
                    class="variant-form" enctype="multipart/form-data" id="variant-form">
                    @csrf
                    @method('PUT')

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
                                                {{ old('product_id', $productVariant->product_id) == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="error-message" id="product_id-error"></div>
                                </div>

                                <div class="form-group">
                                    <label for="sku" class="form-label">SKU</label>
                                    <input type="text" id="sku" name="sku" class="custom-input"
                                        placeholder="Mã SKU (tùy chọn)" value="{{ old('sku', $productVariant->sku) }}">
                                    <div class="error-message" id="sku-error"></div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h6 class="section-title">Thuộc tính biến thể</h6>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="size" class="form-label">Kích thước</label>
                                        <input type="text" id="size" name="size" class="custom-input"
                                            placeholder="Ví dụ: S, M, L, XL"
                                            value="{{ old('size', $productVariant->size) }}">
                                        <div class="error-message" id="size-error"></div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="status" class="form-label required">
                                            Trạng thái <span class="required-asterisk">*</span>
                                        </label>
                                        <select id="status" name="status" class="custom-input" required>
                                            <option value="">Chọn trạng thái</option>
                                            <option value="active"
                                                {{ old('status', $productVariant->status) == 'active' ? 'selected' : '' }}>
                                                Hoạt động</option>
                                            <option value="inactive"
                                                {{ old('status', $productVariant->status) == 'inactive' ? 'selected' : '' }}>
                                                Không hoạt động</option>
                                        </select>
                                        <div class="error-message" id="status-error"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="color_name" class="form-label">Màu sắc</label>
                                    <div class="color-input-group">
                                        <input type="color" id="color" name="color" class="color-picker"
                                            value="{{ old('color', $productVariant->color ?: '#000000') }}"
                                            title="Chọn màu">
                                        <input type="text" id="color_name" name="color_name"
                                            class="custom-input color-name-input" placeholder="Tên màu (VD: Đỏ, Xanh)"
                                            value="{{ old('color_name', $productVariant->color_name) }}">
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
                                                placeholder="0.00" value="{{ old('price', $productVariant->price) }}"
                                                min="0" step="0.01" required>
                                        </div>
                                        <div class="error-message" id="price-error"></div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="quantity" class="form-label required">
                                            Số lượng tồn kho <span class="required-asterisk">*</span>
                                        </label>
                                        <input type="number" id="quantity" name="quantity" class="custom-input"
                                            placeholder="0" value="{{ old('quantity', $productVariant->quantity) }}"
                                            min="0" required>
                                        <div class="error-message" id="quantity-error"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Hình ảnh biến thể -->
                        <div class="tab-pane fade" id="variant-images" role="tabpanel">
                            <div class="form-section">
                                <h6 class="section-title">Quản lý ảnh cho biến thể</h6>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Quản lý ảnh:</strong> Bạn đang xem ảnh chung + ảnh của màu
                                    "{{ $productVariant->color_name ?: 'không có màu' }}"
                                </div>

                                <!-- Current Images (General + Color-specific) -->
                                @if ($variantImages && $variantImages->count() > 0)
                                    <div class="form-group">
                                        <label class="form-label">Ảnh hiện tại ({{ $variantImages->count() }} ảnh)</label>
                                        <div class="existing-images-grid row">
                                            @foreach ($variantImages as $image)
                                                <div class="col-md-3 mb-3">
                                                    <div class="existing-image-item {{ !$image->color ? 'general-image' : 'color-image' }}"
                                                        data-image-id="{{ $image->id }}"
                                                        data-color="{{ $image->color }}">
                                                        <img src="{{ Storage::url($image->image_path_medium ?? $image->image_path) }}"
                                                            alt="Variant Image" class="img-fluid">
                                                        <div class="image-info">
                                                            @if ($image->color)
                                                                <span
                                                                    class="color-tag color-specific">{{ $image->color }}</span>
                                                            @else
                                                                <span class="color-tag general">Ảnh chung</span>
                                                            @endif

                                                            @if ($image->variant_id == $productVariant->id)
                                                                <span class="variant-tag">Của biến thể này</span>
                                                            @else
                                                                <span class="variant-tag shared">Ảnh dùng chung</span>
                                                            @endif
                                                        </div>

                                                        <!-- Only allow deletion of images belonging to this variant -->
                                                        @if ($image->variant_id == $productVariant->id)
                                                            <div class="image-actions">
                                                                <button type="button" class="delete-existing-image"
                                                                    data-image-id="{{ $image->id }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                            <input type="hidden" name="delete_images[]" value=""
                                                                disabled>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div id="image-deletion-warning" class="alert alert-warning mt-2"
                                            style="display: none;">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            <span id="deletion-warning-text"></span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Add New Images -->
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0">Thêm ảnh mới</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            id="addVariantImageBtn">
                                            <i class="fas fa-plus me-1"></i> Thêm ảnh
                                        </button>
                                    </div>

                                    <div id="variant-images-container">
                                        <!-- Ảnh mới sẽ được thêm động ở đây -->
                                    </div>
                                    <div class="error-message" id="variant-images-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.product-variants.index', ['product_id' => $productVariant->product_id]) }}"
                            class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Cập nhật biến thể
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Copy styles from create */
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

        /* New styles for image variant section */
        .existing-images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
        }

        .existing-image-item {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            border: 1px solid #ddd;
            background: #f8f9fa;
        }

        .existing-image-item img {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 8px;
        }

        .image-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 8px;
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
            font-size: 14px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        .color-tag {
            display: inline-block;
            padding: 2px 6px;
            margin-right: 4px;
            font-size: 12px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.2);
        }

        .color-tag.general {
            background: rgba(0, 123, 255, 0.2);
            color: #007bff;
        }

        .image-actions {
            position: absolute;
            top: 8px;
            right: 8px;
            display: flex;
            gap: 4px;
        }

        .delete-existing-image {
            background: rgba(255, 255, 255, 0.8);
            border: none;
            border-radius: 4px;
            padding: 6px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .delete-existing-image:hover {
            background: rgba(255, 255, 255, 0.6);
        }

        #variant-images-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .new-image-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f8f9fa;
        }

        .new-image-item input[type="file"] {
            flex: 1;
        }

        .remove-image-btn {
            background: #dc3545;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 6px 12px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .remove-image-btn:hover {
            background: #c82333;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Initialize existing images data
        window.existingProductImages = @json($existingProductImages);
        window.originalVariantColor = "{{ $productVariant->color_name }}";

        $(document).ready(function() {
            // Check color requirement on load
            checkColorImageRequirement();

            // Check when color changes
            $('#color_name').on('input change', function() {
                checkColorImageRequirement();
            });

            // Handle existing image deletion
            $('.delete-existing-image').click(function() {
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

                // Recheck image requirements
                checkColorImageRequirement();
            });
        });

        function checkColorImageRequirement() {
            const variantColor = $('#color_name').val().trim();
            const alertContainer = $('#color-image-requirement-alert');

            // Remove existing alert
            alertContainer.remove();

            if (variantColor) {
                // Count remaining images after deletion
                const imagesToDelete = [];
                $('.existing-image-item.marked-for-deletion').each(function() {
                    const imageId = $(this).find('input[name="delete_images[]"]').val();
                    if (imageId) imagesToDelete.push(imageId);
                });

                // Check remaining existing images for this color or general
                let hasRemainingImages = false;
                $('.existing-image-item:not(.marked-for-deletion)').each(function() {
                    const colorTag = $(this).find('.color-tag').text().trim();
                    if (colorTag === variantColor || colorTag === 'Ảnh chung') {
                        hasRemainingImages = true;
                    }
                });

                // Check new images being uploaded
                let hasNewImages = false;
                $('.variant-image-upload-item').each(function() {
                    const fileInput = $(this).find('input[type="file"]')[0];
                    if (fileInput && fileInput.files && fileInput.files.length > 0) {
                        hasNewImages = true;
                    }
                });

                // If no remaining images and no new images
                if (!hasRemainingImages && !hasNewImages) {
                    const alertHtml = `
                    <div id="color-image-requirement-alert" class="alert alert-danger mt-3">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>Cảnh báo:</strong> Màu "<strong>${variantColor}</strong>" sẽ không còn ảnh nào sau khi xóa. 
                        Bạn <strong>bắt buộc</strong> phải upload ít nhất 1 ảnh cho màu này hoặc ảnh chung ở tab "Hình ảnh biến thể".
                    </div>
                `;
                    $('#color_name').closest('.form-group').after(alertHtml);

                    $('#variant-images-tab').addClass('tab-require-attention');
                }
            }
        }

        // Update validation function for edit
        function validateVariantImages() {
            const errors = [];
            const variantColor = $('#color_name').val().trim();

            if (variantColor) {
                // Count remaining images after deletion
                let hasRemainingImages = false;
                $('.existing-image-item:not(.marked-for-deletion)').each(function() {
                    const colorTag = $(this).find('.color-tag').text().trim();
                    if (colorTag === variantColor || colorTag === 'Ảnh chung') {
                        hasRemainingImages = true;
                    }
                });

                // Check new images being uploaded
                let hasNewImages = false;
                $('.variant-image-upload-item').each(function() {
                    const fileInput = $(this).find('input[type="file"]')[0];
                    if (fileInput && fileInput.files && fileInput.files.length > 0) {
                        hasNewImages = true;
                    }
                });

                // If no remaining images and no new images
                if (!hasRemainingImages && !hasNewImages) {
                    $('#variant-images-error').text(
                        `Màu "${variantColor}" sẽ không còn ảnh nào sau khi xóa. Bắt buộc phải upload ít nhất 1 ảnh cho màu này hoặc ảnh chung.`
                        );
                    errors.push(
                        `Màu "${variantColor}" sẽ không còn ảnh nào sau khi xóa. Bắt buộc phải upload ít nhất 1 ảnh cho màu này hoặc ảnh chung.`
                        );
                }
            }

            return errors;
        }

        $(document).ready(function() {
            // Remove HTML5 validation
            $('input[required], select[required]').removeAttr('required');

            // AJAX form submission
            $('#variant-form').submit(function(e) {
                e.preventDefault();
                submitForm();
            });

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
                            text: response.message ||
                                'Biến thể sản phẩm đã được cập nhật thành công',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Redirect after success
                            window.location.href = response.redirect ||
                                "{{ route('admin.product-variants.index', ['product_id' => $productVariant->product_id]) }}";
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
                                text: xhr.responseJSON?.message ||
                                    'Có lỗi xảy ra, vui lòng thử lại sau.'
                            });
                        }
                    }
                });
            }

            // Image variant scripts
            let imageIndex = 0;

            // Add new image variant field
            $('#addVariantImageBtn').click(function() {
                imageIndex++;
                const newImageItem = `
                <div class="new-image-item" id="image-item-${imageIndex}">
                    <input type="hidden" name="image_variants[${imageIndex}][id]" value="">
                    <input type="file" name="image_variants[${imageIndex}][file]" class="form-control" accept="image/*">
                    <div class="image-preview" id="image-preview-${imageIndex}" style="display: none;">
                        <img src="" alt="Image Preview" class="img-thumbnail">
                    </div>
                    <button type="button" class="remove-image-btn" data-image-index="${imageIndex}">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                </div>
            `;

                $('#variant-images-container').append(newImageItem);
            });

            // Remove image variant field
            $(document).on('click', '.remove-image-btn', function() {
                const imageIndex = $(this).data('image-index');
                $(`#image-item-${imageIndex}`).remove();
            });

            // Preview image variant
            $(document).on('change', 'input[type="file"][name^="image_variants"]', function() {
                const file = this.files[0];
                const imageIndex = $(this).closest('.new-image-item').attr('id').split('-').pop();

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $(`#image-preview-${imageIndex} img`).attr('src', e.target.result);
                        $(`#image-preview-${imageIndex}`).show();
                    }
                    reader.readAsDataURL(file);
                } else {
                    $(`#image-preview-${imageIndex}`).hide();
                }
            });
        });

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
