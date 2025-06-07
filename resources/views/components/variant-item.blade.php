<div class="variant-item mb-3 form-card p-2" data-index="{{ $index }}">
    <div class="card-header d-flex justify-content-between align-items-center py-2">
        <h6 class="mb-0">Biến thể #{{ $index + 1 }}</h6>
        <button type="button" class="btn btn-sm btn-outline-danger remove-variant-btn {{ $index == 0 ? 'd-none' : '' }}" onclick="removeVariant({{ $index }})">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    
    <div class="card-body">
        <div class="row">
            <div class="form-group col-md-4">
                <label class="form-label">Kích thước</label>
                <input type="text" name="variants[{{ $index }}][size]" class="custom-input" 
                       placeholder="Ví dụ: S, M, L, XL" value="{{ $variant['size'] ?? '' }}">
                <div class="error-message" id="variants-{{ $index }}-size-error"></div>
            </div>
            
            <div class="form-group col-md-4">
                <label class="form-label">Màu sắc</label>
                <div class="color-input-group">
                    <input type="color" name="variants[{{ $index }}][color]" class="color-picker color-input" 
                           value="{{ $variant['color'] ?? '#000000' }}" title="Chọn màu">
                    <input type="text" name="variants[{{ $index }}][color_name]" class="custom-input color-name-input" 
                           placeholder="Tên màu (VD: Đỏ, Xanh)" value="{{ $variant['color_name'] ?? '' }}">
                </div>
                <small class="form-text text-muted">Chọn màu và nhập tên màu hiển thị</small>
                <div class="error-message" id="variants-{{ $index }}-color_name-error"></div>
            </div>
            
            <div class="form-group col-md-4">
                <label class="form-label required">Trạng thái <span class="required-asterisk">*</span></label>
                <select name="variants[{{ $index }}][status]" class="custom-input">
                    <option value="">Chọn trạng thái</option>
                    <option value="active" {{ ($variant['status'] ?? 'active') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="inactive" {{ ($variant['status'] ?? '') == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                </select>
                <div class="error-message" id="variants-{{ $index }}-status-error"></div>
            </div>
        </div>
        
        <div class="row">
            <div class="form-group col-md-4">
                <label class="form-label required">Giá bán <span class="required-asterisk">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" name="variants[{{ $index }}][price]" class="custom-input" 
                           placeholder="0.00" min="0" step="0.01" value="{{ $variant['price'] ?? '' }}">
                </div>
                <div class="error-message" id="variants-{{ $index }}-price-error"></div>
            </div>
            
            <div class="form-group col-md-4">
                <label class="form-label required">Số lượng <span class="required-asterisk">*</span></label>
                <input type="number" name="variants[{{ $index }}][quantity]" class="custom-input" 
                       placeholder="0" min="0" value="{{ $variant['quantity'] ?? $variant['stock_quantity'] ?? '' }}">
                <div class="error-message" id="variants-{{ $index }}-quantity-error"></div>
            </div>
            
            <div class="form-group col-md-4">
                <label class="form-label">SKU</label>
                <input type="text" name="variants[{{ $index }}][sku]" class="custom-input" 
                       placeholder="Mã SKU (tùy chọn)" value="{{ $variant['sku'] ?? '' }}">
                <div class="error-message" id="variants-{{ $index }}-sku-error"></div>
            </div>
        </div>

        @if(isset($variant['id']))
            <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant['id'] }}">
        @endif
    </div>
</div>

@push('styles')
    <style>
        .input-group {
            display: flex !important;
            align-items: stretch !important;
        }

        .input-group-text {
            border: 1px solid #ced4da !important;
            border-right: none !important;
            border-radius: 0.375rem 0 0 0.375rem !important;
            background-color: #e9ecef !important;
            padding: 0.375rem 0.75rem !important;
            display: flex !important;
            align-items: center !important;
        }

        .input-group .custom-input {
            border-left: none !important;
            border-radius: 0 0.375rem 0.375rem 0 !important;
            flex: 1 !important;
        }
    </style>
@endpush
