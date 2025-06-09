@extends('admin.layouts.sidebar')

@section('title', 'Quản lý biến thể áp dụng khuyến mãi')

@section('main-content')
    <div class="promotion-variants-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.promotions.index') }}">Khuyến mãi</a></li>
                <li class="breadcrumb-item current">Sản phẩm áp dụng</li>
            </ol>
        </div>

        <!-- Thông báo lưu ý -->
        <div class="content-card mb-4">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-info-circle icon-title"></i>
                    <h5>Lưu ý quan trọng</h5>
                </div>
            </div>
            <div class="card-content">
                <div class="alert alert-info mb-0">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Quy tắc áp dụng khuyến mãi:</strong> Mỗi biến thể sản phẩm chỉ có thể được áp dụng cho một
                    khuyến mãi tại một thời điểm.
                    Nếu muốn áp dụng khuyến mãi này cho biến thể đã có khuyến mãi khác, bạn cần xóa khuyến mãi cũ trước.
                </div>
            </div>
        </div>

        <!-- Promotion Details Card -->
        <div class="content-card mb-4">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-percent icon-title"></i>
                    <h5>Thông tin khuyến mãi</h5>
                </div>
            </div>
            <div class="card-content">
                <div class="row">
                    <div class="col-3">
                        <div class="info-item">
                            <label>Tên khuyến mãi:</label>
                            <p>{{ $promotion->name }}</p>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="info-item">
                            <label>Loại giảm giá:</label>
                            <p>{{ $promotion->type == 'percentage' ? 'Phần trăm' : 'Số tiền cố định' }}</p>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="info-item">
                            <label>Giá trị:</label>
                            <p>{{ $promotion->getFormattedDiscountValue() }}</p>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="info-item">
                            <label>Trạng thái:</label>
                            <p>
                                @if ($promotion->active())
                                    <span class="badge bg-success">Đang hoạt động</span>
                                @else
                                    <span class="badge bg-secondary">Không hoạt động</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="info-item">
                            <label>Đã áp dụng cho:</label>
                            <p>
                                @if ($appliedVariants->isEmpty())
                                    <span class="badge bg-secondary">Chưa có sản phẩm nào</span>
                                @else
                                    <span class="badge bg-primary">{{ $appliedVariants->count() }} sản phẩm</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="info-item">
                            <label>Ngày bắt đầu:</label>
                            <p>{{ $promotion->start_date->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="info-item">
                            <label>Ngày kết thúc:</label>
                            <p>{{ $promotion->end_date->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="info-item">
                            <label>Giá trị tối thiểu:</label>
                            <p>{{ $promotion->minimum_value ? '$' . number_format($promotion->minimum_value, 2) : 'Không yêu cầu' }}</p>
                        </div>

                        <div class="info-item">
                            <label>Giá trị tối đa:</label>
                            <p>{{ $promotion->maximum_value ? '$' . number_format($promotion->maximum_value, 2) : 'Không giới hạn' }}</p>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="info-item">
                            <label>Mô tả:</label>
                            <p>{{ $promotion->description ?: 'Không có mô tả' }}</p>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

        <!-- Applied Variants Card -->
        <div class="content-card mb-4">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-tags icon-title"></i>
                    <h5>Các sản phẩm đang áp dụng khuyến mãi</h5>
                </div>
            </div>
            <div class="card-content">
                @if ($appliedVariants->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <h4>Chưa có sản phẩm nào được áp dụng</h4>
                        <p>Sử dụng phần thêm mới bên dưới để áp dụng khuyến mãi cho sản phẩm.</p>
                    </div>
                @else
                    @foreach ($appliedVariants as $productId => $variants)
                        <div class="product-group mb-4">
                            <div class="product-header">
                                @php
                                    $firstVariant = $variants->first();
                                    $productExists = $firstVariant->productVariant && $firstVariant->productVariant->product;
                                    $productName = $productExists ? $firstVariant->productVariant->product->name : 'Sản phẩm không xác định';
                                    $productId = $productExists ? $firstVariant->productVariant->product_id : $productId;
                                @endphp
                                <h6>{{ $productName }}</h6>
                                <div class="product-actions">
                                    @include('components.delete-form', [
                                        'id' => $productId,
                                        'route' => route('admin.promotions.remove-product-variants', [
                                            'promotionId' => $promotion->id,
                                            'productId' => $productId,
                                        ]),
                                        'message' => "Bạn có chắc chắn muốn xóa tất cả biến thể của " . ($productExists ? "sản phẩm '{$productName}'" : "sản phẩm không xác định này") . " khỏi khuyến mãi '{$promotion->name}'?",
                                    ])
                                </div>
                            </div>
                            <div class="variants-table-container">
                                <table class="data-table variants-table">
                                    <thead>
                                        <tr>
                                            <th>Màu sắc</th>
                                            <th>Kích thước</th>
                                            <th>SKU</th>
                                            <th>Giá gốc</th>
                                            <th>Giá sau KM</th>
                                            
                                            <th class="text-center">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($variants as $variant)
                                            @php
                                                $variantExists = $variant->productVariant !== null;
                                                $price = $variantExists ? $variant->productVariant->price : 0;
                                                $discountedPrice = $variantExists 
                                                    ? ($promotion->type == 'percentage'
                                                        ? $price * (1 - $promotion->value / 100)
                                                        : max(0, $price - $promotion->value))
                                                    : 0;
                                                
                                                $colorName = $variantExists ? $variant->productVariant->color_name : 'Không xác định';
                                                $colorCode = $variantExists ? $variant->productVariant->color : '#cccccc';
                                                $size = $variantExists ? $variant->productVariant->size : 'Không xác định';
                                                $sku = $variantExists ? $variant->productVariant->sku : 'Không xác định';
                                            @endphp
                                            <tr class="{{ !$variantExists ? 'table-warning' : '' }}">
                                                <td>
                                                    <div class="color-display">
                                                        <span class="color-swatch"
                                                            style="background-color: {{ $colorCode }}"></span>
                                                        {{ $colorName }}
                                                        @if(!$variantExists)
                                                            <span class="badge bg-warning ms-2">Biến thể đã xóa</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>{{ $size }}</td>
                                                <td>{{ $sku }}</td>
                                                <td>{{ $variantExists ? '$'.number_format($price, 2) : 'N/A' }}</td>
                                                <td class="discounted-price">{{ $variantExists ? '$'.number_format($discountedPrice, 2) : 'N/A' }}</td>
                                                <td class="text-center">
                                                    @include('components.delete-form', [
                                                        'id' => $variant->id,
                                                        'route' => route(
                                                            'admin.promotions.remove-variant',
                                                            $variant),
                                                        'message' => "Bạn có chắc chắn muốn xóa " . 
                                                            ($variantExists 
                                                                ? "biến thể '{$colorName} ({$size})'" 
                                                                : "biến thể không xác định này") .
                                                            " khỏi khuyến mãi '{$promotion->name}'?",
                                                    ])
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Add Variants Card -->
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-plus-circle icon-title"></i>
                    <h5>Thêm sản phẩm áp dụng</h5>
                </div>
            </div>
            <div class="card-content">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <form action="{{ route('admin.promotions.apply-variants', $promotion) }}" method="POST"
                            id="applyToProductForm">
                            @csrf
                            <div class="form-group">
                                <label for="product_id">Áp dụng cho tất cả biến thể của sản phẩm:</label>
                                <select name="product_id" id="product_id" class="form-control">
                                    <option value="">-- Chọn sản phẩm --</option>
                                    @foreach ($availableProducts as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-pry mt-2" id="apply-to-product-btn">Áp dụng cho toàn
                                bộ biến thể</button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="product_select">Hoặc chọn từng biến thể:</label>
                            <select id="product_select" class="form-control product-selector">
                                <option value="">-- Chọn sản phẩm để xem biến thể --</option>
                                @foreach ($availableProducts as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="variant-selector-container" style="display: none;">
                    <form action="{{ route('admin.promotions.apply-variants', $promotion) }}" method="POST"
                        id="applyToVariantsForm">
                        @csrf
                        <div class="variants-list">
                            <div class="loading-spinner">
                                <i class="fas fa-spinner fa-spin"></i> Đang tải biến thể...
                            </div>
                            <div class="variants-checkboxes"></div>
                        </div>
                        <div class="form-actions mt-3">
                            <button type="submit" class="btn btn-pry" id="apply-variants-btn">Áp dụng khuyến mãi cho
                                biến thể đã chọn</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('components.toast-main')
@include('components.toast')

@push('styles')
    <style>
        .info-item {
            margin-bottom: 10px;
        }

        .info-item label {
            font-weight: 600;
            color: #555;
            margin-bottom: 3px;
        }

        .info-item p {
            margin: 0;
        }

        .product-group {
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
        }

        .product-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
        }

        .product-header h6 {
            margin: 0;
            font-weight: 600;
        }

        .variants-table {
            margin-bottom: 0;
        }

        .variants-table-container {
            padding: 0;
            overflow-x: auto;
        }

        .color-display {
            display: flex;
            align-items: center;
        }

        .color-swatch {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 8px;
            border: 1px solid #ddd;
        }

        .discounted-price {
            font-weight: 600;
            color: #D1A66E;
        }

        .variant-selector-container {
            margin-top: 20px;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
        }

        .variants-list {
            max-height: 300px;
            overflow-y: auto;
            padding: 10px 0;
        }

        .variant-checkbox {
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .variant-checkbox:hover {
            background-color: #f8f9fa;
        }

        .loading-spinner {
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .variants-checkboxes {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 10px;
        }

        .result-message {
            margin-top: 15px;
        }

        .alert {
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 15px;
        }

        .alert-list {
            margin-top: 8px;
            padding-left: 20px;
            margin-bottom: 0;
        }

        .variant-checkbox.already-applied {
            background-color: rgba(0, 123, 255, 0.1);
            border-left: 3px solid #007bff;
        }

        .variant-checkbox.applied-to-other {
            background-color: rgba(255, 193, 7, 0.1);
            border-left: 3px solid #ffc107;
        }

        .variant-status {
            margin-left: 10px;
        }

        /* Thêm style cho biến thể đã bị xóa */
        .table-warning {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .color-swatch.deleted {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3E%3Cg fill='%23999' fill-opacity='0.6'%3E%3Cpath fill-rule='evenodd' d='M0 0h4v4H0V0zm4 4h4v4H4V4z'/%3E%3C/g%3E%3C/svg%3E");
            position: relative;
        }

        .color-swatch.deleted::after {
            content: "×";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #ff0000;
            font-weight: bold;
        }

        .badge.bg-warning {
            font-size: 0.75rem;
            padding: 0.15rem 0.5rem;
            background-color: #ffc107 !important;
            color: #212529;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#product_select').change(function() {
                const productId = $(this).val();

                if (!productId) {
                    $('.variant-selector-container').hide();
                    return;
                }

                $('.variant-selector-container').show();
                $('.loading-spinner').show();
                $('.variants-checkboxes').empty();

                // Fetch variants for the selected product
                $.ajax({
                    url: '{{ route('admin.promotions.product-variants') }}',
                    method: 'GET',
                    data: {
                        product_id: productId,
                        promotion_id: {{ $promotion->id }}
                    },
                    success: function(response) {
                        $('.loading-spinner').hide();

                        if (response.variants.length === 0) {
                            $('.variants-checkboxes').html(
                                '<p>Không có biến thể nào khả dụng cho sản phẩm này.</p>');
                            return;
                        }

                        // Build checkboxes for each variant
                        response.variants.forEach(function(variant) {
                            const formattedPrice = new Intl.NumberFormat('en-US', {
                                style: 'currency',
                                currency: 'USD'
                            }).format(variant.price);

                            let disabled = '';
                            let statusMessage = '';
                            let statusClass = '';

                            // Đã áp dụng cho khuyến mãi hiện tại
                            if (variant.applied_to_current) {
                                disabled = 'disabled';
                                statusMessage =
                                    '<span class="badge bg-info">Đã áp dụng</span>';
                                statusClass = 'already-applied';
                            }
                            // Đã áp dụng cho khuyến mãi khác
                            else if (variant.other_promotion) {
                                disabled = 'disabled';
                                statusMessage =
                                    `<span class="badge bg-warning">Đã áp dụng khuyến mãi: ${variant.other_promotion.name}</span>`;
                                statusClass = 'applied-to-other';
                            }

                            const checkbox = `
                            <div class="variant-checkbox ${statusClass}">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="variant_ids[]" 
                                           id="variant-${variant.id}" value="${variant.id}" ${disabled}>
                                    <label class="form-check-label" for="variant-${variant.id}">
                                        <div class="">
                                            <div>
                                                <span class="color-swatch" style="background-color: ${variant.color}"></span>
                                                ${variant.color_name} | Size: ${variant.size} | SKU: ${variant.sku}
                                            </div>
                                                <div>Giá: ${formattedPrice}</div>
                                           
                                        </div>
                                        <div class="variant-status">
                                            ${statusMessage}
                                        </div>
                                    </label>
                                </div>
                            </div>
                        `;

                            $('.variants-checkboxes').append(checkbox);
                        });
                    },
                    error: function() {
                        $('.loading-spinner').hide();
                        $('.variants-checkboxes').html(
                            '<p class="text-danger">Có lỗi xảy ra khi tải biến thể. Vui lòng thử lại.</p>'
                        );
                    }
                });
            });

            // Prevent submission if no checkboxes selected
            $('#applyToVariantsForm').submit(function(e) {
                const checkedBoxes = $('input[name="variant_ids[]"]:checked:not(:disabled)');
                if (checkedBoxes.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Lưu ý',
                        text: 'Vui lòng chọn ít nhất một biến thể khả dụng.',
                        confirmButtonText: 'Đã hiểu'
                    });
                }
            });

            $('#applyToProductForm').submit(function(e) {
                if (!$('#product_id').val()) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Lưu ý',
                        text: 'Vui lòng chọn một sản phẩm để áp dụng.',
                        confirmButtonText: 'Đã hiểu'
                    });
                    return false;
                }

                // Xác nhận trước khi gửi form
                e.preventDefault();
                Swal.fire({
                    title: 'Xác nhận áp dụng',
                    text: 'Áp dụng khuyến mãi cho tất cả biến thể có thể ghi đè khuyến mãi hiện có. Bạn có chắc chắn muốn tiếp tục?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Đồng ý',
                    cancelButtonText: 'Hủy bỏ'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    </script>
@endpush
