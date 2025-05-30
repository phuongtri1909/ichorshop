@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa sản phẩm')

@section('main-content')
    <div class="category-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                <li class="breadcrumb-item current">Chỉnh sửa</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-edit icon-title"></i>
                    <h5>Chỉnh sửa sản phẩm</h5>
                </div>
                <div class="product-meta">
                    <div class="product-badge category">
                        <i class="fas fa-list"></i>
                        <span>{{ $product->category->name }}</span>
                    </div>
                    <div class="product-badge slug">
                        <i class="fas fa-link"></i>
                        <span>{{ $product->slug }}</span>
                    </div>
                    <div class="product-badge status {{ $product->is_active ? 'active' : 'inactive' }}">
                        <i class="fas fa-{{ $product->is_active ? 'check-circle' : 'times-circle' }}"></i>
                        <span>{{ $product->is_active ? 'Đang hiển thị' : 'Đã ẩn' }}</span>
                    </div>
                </div>
            </div>
            <div class="form-body">
                @include('admin.components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.products.update', $product) }}" method="POST" class="product-form"
                    enctype="multipart/form-data" id="product-form">
                    @csrf
                    @method('PUT')

                    <div class="form-tabs">
                        <div class="tab-header">
                            <div class="tab-btn active" data-tab="basic-info">Thông tin cơ bản</div>
                            <div class="tab-btn" data-tab="pricing">Quy cách & Giá</div>
                            <div class="tab-btn" data-tab="images">Hình ảnh</div>
                            <div class="tab-btn" data-tab="seo">Mô tả & SEO</div>
                        </div>

                        <div class="tab-content">
                            <!-- Tab Thông tin cơ bản -->
                            <div class="tab-pane active" id="basic-info">
                                <div class="form-group">
                                    <label for="name" class="form-label-custom">
                                        Tên sản phẩm <span class="required-mark">*</span>
                                    </label>
                                    <input type="text"
                                        class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}" id="name"
                                        name="name" value="{{ old('name', $product->name) }}" required>
                                    <div class="error-message" id="error-name">
                                        @error('name')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="category_id" class="form-label-custom">
                                        Danh mục <span class="required-mark">*</span>
                                    </label>
                                    <select class="custom-input {{ $errors->has('category_id') ? 'input-error' : '' }}"
                                        id="category_id" name="category_id" required>
                                        <option value="">-- Chọn danh mục --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="error-message" id="error-category_id">
                                        @error('category_id')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-check-group">
                                    <div class="custom-checkbox">
                                        <input type="checkbox" id="is_featured" name="is_featured" value="1"
                                            {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                        <label for="is_featured" class="form-label-custom mb-0">Sản phẩm nổi bật</label>
                                    </div>

                                    <div class="custom-checkbox">
                                        <input type="checkbox" id="is_active" name="is_active" value="1"
                                            {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                        <label for="is_active" class="form-label-custom mb-0">Hiển thị sản phẩm</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="highlight" class="form-label-custom">Điểm nổi bật</label>
                                    <div class="highlight-inputs">
                                        @if (old('highlight', $product->highlight))
                                            @foreach (old('highlight', $product->highlight) as $index => $highlight)
                                                <div class="highlight-item">
                                                    <input type="text" class="custom-input" name="highlight[]"
                                                        value="{{ $highlight }}" placeholder="Ví dụ: Hương vị đậm đà">
                                                    <button type="button" class="remove-highlight" title="Xóa"><i
                                                            class="fas fa-times"></i></button>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="highlight-item">
                                                <input type="text" class="custom-input" name="highlight[]"
                                                    placeholder="Ví dụ: Hương vị đậm đà">
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" id="add-highlight" class="btn btn-sm btn-light mt-2">
                                        <i class="fas fa-plus"></i> Thêm điểm nổi bật
                                    </button>
                                </div>
                            </div>
                            <!-- Tab Quy cách & Giá -->
                            <div class="tab-pane" id="pricing">
                                <div class="weight-container" id="weights-container">
                                    @if (old('weights'))
                                        @foreach (old('weights') as $index => $weightData)
                                            <div class="weight-item" data-index="{{ $index }}">
                                                <div class="weight-header">
                                                    <h5>Quy cách #{{ $index + 1 }}</h5>
                                                    <button type="button" class="weight-remove-btn" title="Xóa quy cách"
                                                        {{ count(old('weights')) <= 1 ? 'disabled' : '' }}>
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>

                                                <div class="weight-content">
                                                    <div class="form-row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-label-custom">
                                                                    Quy cách <span class="required-mark">*</span>
                                                                </label>
                                                                <input type="text" class="custom-input"
                                                                    name="weights[{{ $index }}][weight]"
                                                                    value="{{ $weightData['weight'] }}"
                                                                    placeholder="Ví dụ: 250g, Túi 1kg" required>
                                                                <div class="error-message"
                                                                    id="error-weights-{{ $index }}-weight">
                                                                    @if ($errors->has("weights.$index.weight"))
                                                                        {{ $errors->first("weights.$index.weight") }}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-label-custom">Mã SKU</label>
                                                                <input type="text" class="custom-input"
                                                                    name="weights[{{ $index }}][sku]"
                                                                    value="{{ $weightData['sku'] ?? '' }}"
                                                                    placeholder="Mã quản lý sản phẩm">
                                                                <div class="error-message"
                                                                    id="error-weights-{{ $index }}-sku">
                                                                    @if ($errors->has("weights.$index.sku"))
                                                                        {{ $errors->first("weights.$index.sku") }}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-label-custom">
                                                                    Giá <span class="required-mark">*</span>
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="number" class="custom-input"
                                                                        name="weights[{{ $index }}][original_price]"
                                                                        value="{{ $weight->original_price }}"
                                                                        min="0" step="1000" required>
                                                                    <span class="input-group-text">VNĐ</span>
                                                                </div>
                                                                <div class="error-message"
                                                                    id="error-weights-{{ $index }}-original_price">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-label-custom">Giảm giá</label>
                                                                <div class="input-group">
                                                                    <input type="number" class="custom-input"
                                                                        name="weights[{{ $index }}][discount_percent]"
                                                                        value="{{ $weight->discount_percent }}"
                                                                        min="0" max="100">
                                                                    <span class="input-group-text">%</span>
                                                                </div>
                                                                <div class="error-message"
                                                                    id="error-weights-{{ $index }}-discount_percent">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="discounted-price-display">
                                                        <span class="discounted-price-label">Giá sau giảm:</span>
                                                        <span
                                                            class="discounted-price-value">{{ number_format($weight->discounted_price) }}
                                                            VNĐ</span>
                                                    </div>

                                                    <div class="form-check-group">
                                                        <div class="custom-checkbox">
                                                            <input type="checkbox"
                                                                name="weights[{{ $index }}][is_default]"
                                                                id="is_default_{{ $index }}" value="1"
                                                                {{ isset($weightData['is_default']) && $weightData['is_default'] ? 'checked' : '' }}
                                                                class="default-weight-checkbox">
                                                            <label for="is_default_{{ $index }}"
                                                                class="form-label-custom mb-0">Quy cách mặc định</label>
                                                        </div>

                                                        <div class="custom-checkbox">
                                                            <input type="checkbox"
                                                                name="weights[{{ $index }}][is_active]"
                                                                id="is_active_{{ $index }}" value="1"
                                                                {{ isset($weightData['is_active']) && $weightData['is_active'] ? 'checked' : '' }}>
                                                            <label for="is_active_{{ $index }}"
                                                                class="form-label-custom mb-0">Hiển thị quy cách</label>
                                                        </div>
                                                    </div>

                                                    @if (isset($weightData['id']))
                                                        <input type="hidden" name="weights[{{ $index }}][id]"
                                                            value="{{ $weightData['id'] }}">
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        @foreach ($product->weights as $index => $weight)
                                            <div class="weight-item" data-index="{{ $index }}">
                                                <div class="weight-header">
                                                    <h5>Quy cách #{{ $index + 1 }}</h5>
                                                    <button type="button" class="weight-remove-btn" title="Xóa quy cách"
                                                        {{ $product->weights->count() <= 1 ? 'disabled' : '' }}>
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>

                                                <div class="weight-content">
                                                    <div class="form-row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-label-custom">
                                                                    Quy cách <span class="required-mark">*</span>
                                                                </label>
                                                                <input type="text" class="custom-input"
                                                                    name="weights[{{ $index }}][weight]"
                                                                    value="{{ $weight->weight }}"
                                                                    placeholder="Ví dụ: 250g, Túi 1kg" required>
                                                                <div class="error-message"
                                                                    id="error-weights-{{ $index }}-weight"></div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-label-custom">Mã SKU</label>
                                                                <input type="text" class="custom-input"
                                                                    name="weights[{{ $index }}][sku]"
                                                                    value="{{ $weight->sku }}"
                                                                    placeholder="Mã quản lý sản phẩm">
                                                                <div class="error-message"
                                                                    id="error-weights-{{ $index }}-sku"></div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-label-custom">
                                                                    Giá <span class="required-mark">*</span>
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="number" class="custom-input"
                                                                        name="weights[{{ $index }}][original_price]"
                                                                        value="{{ $weight->original_price }}"
                                                                        min="0" step="1000" required>
                                                                    <span class="input-group-text">VNĐ</span>
                                                                </div>
                                                                <div class="error-message"
                                                                    id="error-weights-{{ $index }}-original_price">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="form-label-custom">Giảm giá</label>
                                                                <div class="input-group">
                                                                    <input type="number" class="custom-input"
                                                                        name="weights[{{ $index }}][discount_percent]"
                                                                        value="{{ $weight->discount_percent }}"
                                                                        min="0" max="100">
                                                                    <span class="input-group-text">%</span>
                                                                </div>
                                                                <div class="error-message"
                                                                    id="error-weights-{{ $index }}-discount_percent">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="discounted-price-display">
                                                        <span class="discounted-price-label">Giá sau giảm:</span>
                                                        <span
                                                            class="discounted-price-value">{{ number_format($weight->discounted_price) }}
                                                            VNĐ</span>
                                                    </div>

                                                    <div class="form-check-group">
                                                        <div class="custom-checkbox">
                                                            <input type="checkbox"
                                                                name="weights[{{ $index }}][is_default]"
                                                                id="is_default_{{ $index }}" value="1"
                                                                {{ $weight->is_default ? 'checked' : '' }}
                                                                class="default-weight-checkbox">
                                                            <label for="is_default_{{ $index }}"
                                                                class="form-label-custom mb-0">Quy cách mặc định</label>
                                                        </div>

                                                        <div class="custom-checkbox">
                                                            <input type="checkbox"
                                                                name="weights[{{ $index }}][is_active]"
                                                                id="is_active_{{ $index }}" value="1"
                                                                {{ $weight->is_active ? 'checked' : '' }}>
                                                            <label for="is_active_{{ $index }}"
                                                                class="form-label-custom mb-0">Hiển thị quy cách</label>
                                                        </div>
                                                    </div>

                                                    <input type="hidden" name="weights[{{ $index }}][id]"
                                                        value="{{ $weight->id }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <button type="button" id="add-weight-btn" class="btn btn-light">
                                    <i class="fas fa-plus"></i> Thêm quy cách
                                </button>
                            </div>

                            <!-- Tab Hình ảnh -->
                            <div class="tab-pane" id="images">
                                <div class="form-group">
                                    <label class="form-label-custom">Ảnh đại diện sản phẩm</label>
                                    <div class="image-upload-container">
                                        <div class="image-preview" id="mainImagePreview"
                                            style="background-image: url('{{ $product->image ? asset('storage/' . $product->image) : '' }}');"
                                            class="{{ $product->image ? 'has-image' : '' }}">
                                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                            <span class="upload-text">Chọn ảnh</span>
                                        </div>
                                        <input type="file" name="image" id="main-image-upload"
                                            class="image-upload-input" accept="image/*">
                                    </div>
                                    {{-- <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Kích thước đề xuất: 800x800px, tối đa 2MB.</span>
                                    </div> --}}
                                    <div class="error-message" id="error-image">
                                        @error('image')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label-custom">Ảnh chi tiết sản phẩm</label>
                                    <div class="multi-image-upload-container">
                                        <div class="multi-image-preview" id="additionalImagesPreview">
                                            <div class="image-upload-item">
                                                <i class="fas fa-plus"></i>
                                            </div>

                                            @foreach ($product->images as $image)
                                                <div class="preview-image-item existing-image"
                                                    data-image-id="{{ $image->id }}">
                                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                                        alt="Preview">
                                                    <div class="remove-image" data-image-id="{{ $image->id }}">
                                                        <i class="fas fa-times"></i>
                                                    </div>
                                                    <input type="hidden" name="existing_images[]"
                                                        value="{{ $image->id }}">
                                                </div>
                                            @endforeach
                                        </div>
                                        <input type="file" name="additional_images[]" id="additional-images-upload"
                                            class="multi-image-upload-input" accept="image/*" multiple>

                                        <div id="delete-images-container">
                                            <!-- Sẽ chứa các input hidden để xóa ảnh -->
                                        </div>
                                    </div>
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Thêm tối đa 5 ảnh.</span>
                                    </div>
                                    <div class="error-message" id="error-additional_images">
                                        @error('additional_images')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                    <div class="error-message">
                                        @error('additional_images.*')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Mô tả & SEO -->
                            <div class="tab-pane" id="seo">
                                <div class="form-group">
                                    <label for="description" class="form-label-custom">
                                        Mô tả sản phẩm <span class="required-mark">*</span>
                                    </label>
                                    <textarea class="custom-input {{ $errors->has('description') ? 'input-error' : '' }}" id="description"
                                        name="description" rows="10" placeholder="Nhập mô tả chi tiết sản phẩm...">{{ old('description', $product->description) }}</textarea>
                                    <div class="error-message" id="error-description">
                                        @error('description')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.products.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <div class="action-group">
                            <button type="submit" class="save-button">
                                <i class="fas fa-save"></i> Cập nhật
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        #description {
            min-height: 250px;
            line-height: 1.5;
            padding: 12px 15px;
            font-size: 14px;
            resize: vertical;
            border-color: #e0e0e0;
            border-radius: 4px;
        }

        #description:focus {
            border-color: #D1A66E;
            box-shadow: 0 0 0 0.2rem rgba(209, 166, 110, 0.25);
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {

            // Tab handling
            $('.tab-btn').click(function() {
                $('.tab-btn').removeClass('active');
                $(this).addClass('active');

                const targetTab = $(this).data('tab');
                $('.tab-pane').removeClass('active');
                $('#' + targetTab).addClass('active');
            });

            // Weights handling
            let weightIndex = {{ isset($product) ? count(old('weights', $product->weights)) - 1 : 0 }};

            $('#add-weight-btn').click(function() {
                weightIndex++;
                addWeightItem(weightIndex);
            });

            function updateDiscountedPrice(weightItem) {
                const originalPrice = parseFloat($(weightItem).find('[name$="[original_price]"]').val()) || 0;
                const discountPercent = parseFloat($(weightItem).find('[name$="[discount_percent]"]').val()) || 0;
                const discountedPrice = originalPrice - (originalPrice * (discountPercent / 100));

                $(weightItem).find('.discounted-price-value').text(formatCurrency(discountedPrice));
            }

            function formatCurrency(amount) {
                return new Intl.NumberFormat('vi-VN').format(amount) + ' VNĐ';
            }

            // Thêm sự kiện theo dõi thay đổi giá và discount
            $(document).on('input', '[name$="[original_price]"], [name$="[discount_percent]"]', function() {
                const weightItem = $(this).closest('.weight-item');
                updateDiscountedPrice(weightItem);
            });

            function addWeightItem(index) {

                const template = `
                <div class="weight-item" data-index="${index}">
                    <div class="weight-header">
                        <h5>Quy cách #${index + 1}</h5>
                        <button type="button" class="weight-remove-btn" title="Xóa quy cách">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="weight-content">
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-custom">
                                        Quy cách <span class="required-mark">*</span>
                                    </label>
                                    <input type="text" class="custom-input weight-input" 
                                        name="weights[${index}][weight]" 
                                        placeholder="Ví dụ: 250g, Túi 1kg" required>
                                    <div class="error-message" id="error-weights-${index}-weight"></div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-custom">Mã SKU</label>
                                    <input type="text" class="custom-input" 
                                        name="weights[${index}][sku]" 
                                        placeholder="Mã quản lý sản phẩm">
                                    <div class="error-message" id="error-weights-${index}-sku"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-custom">
                                        Giá <span class="required-mark">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="custom-input price-input" 
                                            name="weights[${index}][original_price]" 
                                            min="0" step="1000" required>
                                        <span class="input-group-text">VNĐ</span>
                                    </div>
                                    <div class="error-message" id="error-weights-${index}-original_price"></div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-custom">Giảm giá</label>
                                    <div class="input-group">
                                        <input type="number" class="custom-input" 
                                            name="weights[${index}][discount_percent]" 
                                            value="0" min="0" max="100">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <div class="error-message" id="error-weights-${index}-discount_percent"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="discounted-price-display">
                            <span class="discounted-price-label">Giá sau giảm:</span>
                            <span class="discounted-price-value">0 VNĐ</span>
                        </div>
                        
                        <div class="form-check-group">
                            <div class="custom-checkbox">
                                <input type="checkbox" 
                                    name="weights[${index}][is_default]" 
                                    id="is_default_${index}" 
                                    value="1"
                                    class="default-weight-checkbox">
                                <label for="is_default_${index}" class="form-label-custom mb-0">Quy cách mặc định</label>
                            </div>
                            
                            <div class="custom-checkbox">
                                <input type="checkbox" 
                                    name="weights[${index}][is_active]" 
                                    id="is_active_${index}" 
                                    value="1" checked>
                                <label for="is_active_${index}" class="form-label-custom mb-0">Hiển thị quy cách</label>
                            </div>
                        </div>
                    </div>
                </div>
                `;

                $('#weights-container').append(template);

                // Enable all remove buttons when we have more than one weight
                if ($('.weight-item').length > 1) {
                    $('.weight-remove-btn').prop('disabled', false);
                }
            }

            // Handle weight removal
            $(document).on('click', '.weight-remove-btn', function() {
                $(this).closest('.weight-item').remove();

                // Disable removal if only one weight left
                if ($('.weight-item').length <= 1) {
                    $('.weight-remove-btn').prop('disabled', true);
                }

                // Rename weight headers
                $('.weight-item').each(function(index) {
                    $(this).find('h5').text(`Quy cách #${index + 1}`);
                });
            });

            // Ensure only one default weight is selected
            $(document).on('change', '.default-weight-checkbox', function() {
                if ($(this).is(':checked')) {
                    $('.default-weight-checkbox').not(this).prop('checked', false);
                }
            });

            // Highlight fields handling
            $('#add-highlight').click(function() {
                addHighlightItem();
            });

            function addHighlightItem(value = '') {
                const highlightTemplate = `
                <div class="highlight-item">
                    <input type="text" class="custom-input" name="highlight[]" 
                        value="${value}" placeholder="Ví dụ: Hương vị đậm đà">
                    <button type="button" class="remove-highlight" title="Xóa"><i class="fas fa-times"></i></button>
                </div>
            `;
                $('.highlight-inputs').append(highlightTemplate);
            }

            $(document).on('click', '.remove-highlight', function() {
                $(this).closest('.highlight-item').remove();
            });

            // Image handling
            $('#main-image-upload').change(function() {
                previewMainImage(this);
            });

            function previewMainImage(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        $('#mainImagePreview').css('background-image', `url('${e.target.result}')`);
                        $('#mainImagePreview').addClass('has-image');
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }

            // Additional images handling
            const additionalImagesInput = document.getElementById('additional-images-upload');
            const previewContainer = document.getElementById('additionalImagesPreview');
            const deleteImagesContainer = document.getElementById('delete-images-container');
            const maxAdditionalImages = 5;
            let additionalImageFiles = [];

            additionalImagesInput.addEventListener('change', function() {
                const files = Array.from(this.files);
                const existingImageCount = document.querySelectorAll('.existing-image').length;
                const totalAllowed = maxAdditionalImages - existingImageCount;

                additionalImageFiles = [...additionalImageFiles, ...files].slice(0, totalAllowed);
                updateAdditionalImagesPreviews();
            });

            function updateAdditionalImagesPreviews() {
                // Keep existing images and upload item
                const existingImages = document.querySelectorAll('.existing-image');
                const uploadItem = document.querySelector('.image-upload-item');

                // Clear new image previews
                document.querySelectorAll('.preview-image-item:not(.existing-image)').forEach(el => el.remove());

                // Add new preview images
                additionalImageFiles.forEach((file, index) => {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const previewItem = document.createElement('div');
                        previewItem.className = 'preview-image-item';
                        previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <div class="remove-image" data-index="${index}">
                            <i class="fas fa-times"></i>
                        </div>
                    `;

                        // Insert before upload button
                        previewContainer.insertBefore(previewItem, uploadItem);
                    };

                    reader.readAsDataURL(file);
                });

                // Hide upload button if reached max
                const totalImages = existingImages.length + additionalImageFiles.length;
                if (totalImages >= maxAdditionalImages) {
                    uploadItem.style.display = 'none';
                } else {
                    uploadItem.style.display = 'flex';
                }
            }

            // Remove new image preview
            $(document).on('click', '.remove-image[data-index]', function() {
                const index = $(this).data('index');
                additionalImageFiles.splice(index, 1);

                // Reset the file input
                additionalImagesInput.value = '';

                // Regenerate the input with the current files
                const dataTransfer = new DataTransfer();
                additionalImageFiles.forEach(file => {
                    dataTransfer.items.add(file);
                });
                additionalImagesInput.files = dataTransfer.files;

                updateAdditionalImagesPreviews();
            });

            // Remove existing image
            $(document).on('click', '.remove-image[data-image-id]', function() {
                const imageId = $(this).data('image-id');
                $(this).closest('.preview-image-item').remove();

                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete_images[]';
                deleteInput.value = imageId;
                deleteImagesContainer.appendChild(deleteInput);

                const remainingImages = document.querySelectorAll('.preview-image-item').length;
                if (remainingImages < maxAdditionalImages) {
                    document.querySelector('.image-upload-item').style.display = 'flex';
                }
            });

            // AJAX form submission
            $('#product-form').submit(function(e) {
                e.preventDefault();
                submitForm();
            });

            function submitForm() {
                // Clear previous errors
                $('.error-message').empty();
                $('.input-error').removeClass('input-error');

                // Create FormData object
                const formData = new FormData(document.getElementById('product-form'));
                const submitBtn = $('.save-button');
                const originalBtnText = submitBtn.html();

                // Disable button and show loading state
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');
                submitBtn.prop('disabled', true);

                $.ajax({
                    url: $('#product-form').attr('action'),
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
                            text: response.message || 'Sản phẩm đã được cập nhật thành công',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Redirect after success
                            window.location.href = response.redirect ||
                                "{{ route('admin.products.index') }}";
                        });
                    },
                    error: function(xhr) {
                        // Reset button
                        submitBtn.html(originalBtnText);
                        submitBtn.prop('disabled', false);

                        // Handle validation errors
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let firstErrorTab = null;

                            // Handle and display each error
                            $.each(errors, function(field, messages) {
                                // Handle nested fields (weights)
                                if (field.includes('.')) {
                                    const parts = field.split('.');
                                    if (parts[0] === 'weights') {
                                        const index = parts[1];
                                        const attribute = parts[2];
                                        const errorId = `error-weights-${index}-${attribute}`;
                                        $(`#${errorId}`).text(messages[0]);
                                        $(`[name="weights[${index}][${attribute}]"]`).addClass(
                                            'input-error');

                                        firstErrorTab = firstErrorTab || 'pricing';
                                    }
                                } else {
                                    // Basic fields
                                    $(`#${field}`).addClass('input-error');
                                    $(`#error-${field}`).text(messages[0]);

                                    // Determine which tab has the error
                                    if (field === 'name' || field === 'category_id') {
                                        firstErrorTab = firstErrorTab || 'basic-info';
                                    } else if (field === 'image') {
                                        firstErrorTab = firstErrorTab || 'images';
                                    } else if (field === 'description') {
                                        firstErrorTab = firstErrorTab || 'seo';
                                        $('.note-editor').addClass('input-error');
                                    } else if (field.startsWith('weights')) {
                                        firstErrorTab = firstErrorTab || 'pricing';
                                    }
                                }
                            });

                            // Switch to first tab with error
                            if (firstErrorTab) {
                                $('.tab-btn').removeClass('active');
                                $('.tab-btn[data-tab="' + firstErrorTab + '"]').addClass('active');
                                $('.tab-pane').removeClass('active');
                                $('#' + firstErrorTab).addClass('active');
                            }

                            // Show error message using SweetAlert
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Vui lòng kiểm tra lại thông tin nhập vào',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        } else {
                            // Server or other errors
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: xhr.responseJSON?.message ||
                                    'Đã xảy ra lỗi khi lưu sản phẩm',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        }
                    }
                });
            }

            // Initial setup - already existing highlights from old input
            // @if (isset($product) && count($product->highlight ?? []) > 0)
            //     @foreach ($product->highlight as $highlight)
            //         @if (!empty($highlight))
            //             addHighlightItem("{{ $highlight }}");
            //         @endif
            //     @endforeach
            // @elseif (old('highlight'))
            //     @foreach (old('highlight') as $highlight)
            //         @if (!empty($highlight))
            //             addHighlightItem("{{ $highlight }}");
            //         @endif
            //     @endforeach
            // @else
            //     // Add default empty highlight
            //     addHighlightItem();
            // @endif

            // Add an initial weight if none exists
            if ($('.weight-item').length === 0) {
                $('#add-weight-btn').click();
            }

            $('.weight-item').each(function() {
                updateDiscountedPrice(this);
            });
        });
    </script>
@endpush
