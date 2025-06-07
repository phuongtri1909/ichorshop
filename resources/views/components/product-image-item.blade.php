<div class="product-image-upload-item mb-3" data-index="{{ $index }}">
    <div class="row position-relative">
       
        <div class="col-md-3">
            <div class="product-image-preview-container">
                <div class="product-image-preview {{ isset($image) ? 'has-image' : '' }}"
                    id="productImagePreview{{ $index }}"
                    @if (isset($image)) style="background-image: url('{{ $image }}');" @endif>
                    @if (!isset($image))
                        <i class="fas fa-image"></i>
                        <span>Chọn ảnh</span>
                    @endif
                </div>

                <input type="file" name="product_images[{{ $index }}][file]" accept="image/*" class="d-none"
                    onchange="previewProductImage(this, {{ $index }})">

                @if (isset($existingImage))
                    <input type="hidden" name="product_images[{{ $index }}][existing_id]"
                        value="{{ $existingImage['id'] }}">
                @endif
            </div>
        </div>

        <div class="col-md-9">
            <div class="form-group">
                <label class="form-label">Chọn màu cho ảnh này:</label>
                <div class="color-selector-group">
                    <div class="color-option no-color-option">
                        <input type="radio" name="product_images[{{ $index }}][color]" value=""
                            id="no_color_{{ $index }}"
                            {{ !isset($selectedColor) || $selectedColor === '' ? 'checked' : '' }}>
                        <label for="no_color_{{ $index }}" class="color-label">Ảnh chung (không có màu cụ
                            thể)</label>
                    </div>

                    @if (isset($availableColors))
                        @foreach ($availableColors as $color)
                            @php
                                $colorId = "color_{$index}_" . str_replace(' ', '_', strtolower($color));
                            @endphp
                            <div class="color-option">
                                <input type="radio" name="product_images[{{ $index }}][color]"
                                    value="{{ $color }}" id="{{ $colorId }}"
                                    {{ isset($selectedColor) && $selectedColor === $color ? 'checked' : '' }}>
                                <label for="{{ $colorId }}" class="color-label">{{ $color }}</label>
                            </div>
                        @endforeach
                    @endif
                </div>
                <small class="form-text text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Chọn "Ảnh chung" nếu ảnh này dùng cho tất cả màu, hoặc chọn màu cụ thể
                </small>
            </div>
        </div>

         <button type="button" class="remove-product-image-btn" onclick="removeImageUpload({{ $index }})">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
