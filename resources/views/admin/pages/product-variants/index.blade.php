@extends('admin.layouts.sidebar')

@section('title', 'Quản lý biến thể sản phẩm')

@section('main-content')
<div class="category-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            @if(request('product_id'))
                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.products.edit', request('product_id')) }}">{{ $selectedProduct->name ?? 'Sản phẩm' }}</a></li>
                <li class="breadcrumb-item current">Biến thể</li>
            @else
                <li class="breadcrumb-item current">Biến thể sản phẩm</li>
            @endif
        </ol>
    </div>

    <div class="content-card">
        <div class="card-top">
            <div class="card-title">
                <i class="fas fa-layer-group icon-title"></i>
                <h5>
                    @if(request('product_id') && isset($selectedProduct))
                        Biến thể của: {{ $selectedProduct->name }}
                    @else
                        Danh sách biến thể sản phẩm
                    @endif
                </h5>
            </div>
            <div class="card-actions">
                @if(request('product_id'))
                    <a href="{{ route('admin.products.index') }}" class="back-button me-2">
                        <i class="fas fa-arrow-left"></i> Quay lại sản phẩm
                    </a>
                @endif
                <a href="{{ route('admin.product-variants.create', ['product_id' => request('product_id')]) }}" class="action-button">
                    <i class="fas fa-plus"></i> Thêm biến thể
                </a>
            </div>
        </div>
        
        <!-- Filter Section -->
        @if(!request('product_id'))
        <div class="filter-section">
            <form action="{{ route('admin.product-variants.index') }}" method="GET" class="filter-form">
                <div class="filter-group">
                    <div class="filter-item">
                        <label for="product_filter">Sản phẩm</label>
                        <select id="product_filter" name="product_id" class="filter-input">
                            <option value="">Tất cả sản phẩm</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-item">
                        <label for="size_filter">Size</label>
                        <input type="text" id="size_filter" name="size" class="filter-input" 
                            placeholder="Tìm theo size" value="{{ request('size') }}">
                    </div>
                    <div class="filter-item">
                        <label for="color_filter">Màu sắc</label>
                        <input type="text" id="color_filter" name="color_name" class="filter-input" 
                            placeholder="Tìm theo màu" value="{{ request('color_name') }}">
                    </div>
                    <div class="filter-item">
                        <label for="status_filter">Trạng thái</label>
                        <select id="status_filter" name="status" class="filter-input">
                            <option value="">Tất cả trạng thái</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                        </select>
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="filter-btn">
                        <i class="fas fa-filter"></i> Lọc
                    </button>
                    <a href="{{ route('admin.product-variants.index') }}" class="filter-clear-btn">
                        <i class="fas fa-times"></i> Xóa bộ lọc
                    </a>
                </div>
            </form>
        </div>
        @endif
        
        <div class="card-content">
            @if($productVariants->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    @if (request()->hasAny(['product_id', 'size', 'color_name', 'status']))
                        <h4>Không tìm thấy biến thể nào</h4>
                        <p>Không có biến thể nào phù hợp với bộ lọc hiện tại.</p>
                        <a href="{{ route('admin.product-variants.index') }}" class="action-button">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    @else
                        <h4>Chưa có biến thể sản phẩm nào</h4>
                        <p>Bắt đầu bằng cách thêm biến thể sản phẩm đầu tiên.</p>
                        <a href="{{ route('admin.product-variants.create', ['product_id' => request('product_id')]) }}" class="action-button">
                            <i class="fas fa-plus"></i> Thêm biến thể mới
                        </a>
                    @endif
                </div>
            @else
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="column-small">STT</th>
                                @if(!request('product_id'))
                                    <th class="column-large">Sản phẩm</th>
                                @endif
                                <th class="column-medium">SKU</th>
                                <th class="column-small">Size</th>
                                <th class="column-small">Màu sắc</th>
                                <th class="column-small">Giá</th>
                                <th class="column-small">Tồn kho</th>
                                <th class="column-small text-center">Trạng thái</th>
                                <th class="column-small text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productVariants as $index => $variant)
                                <tr>
                                    <td class="text-center">{{ ($productVariants->currentPage() - 1) * $productVariants->perPage() + $index + 1 }}</td>
                                    @if(!request('product_id'))
                                        <td class="item-name">
                                            <div class="product-name">{{ $variant->product->name ?? 'N/A' }}</div>
                                            @if($variant->product->description_short)
                                                <div class="product-desc">{{ Str::limit($variant->product->description_short, 40) }}</div>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="variant-sku">
                                        @if($variant->sku)
                                            <code>{{ $variant->sku }}</code>
                                        @else
                                            <span class="text-muted">Chưa có</span>
                                        @endif
                                    </td>
                                    <td class="variant-size">
                                        @if($variant->size)
                                            <span class="size-badge">{{ $variant->size }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="variant-color">
                                        @if($variant->color || $variant->color_name)
                                            <div class="color-info">
                                                @if($variant->color)
                                                    <span class="color-swatch" style="background-color: {{ $variant->color }}" title="{{ $variant->color }}"></span>
                                                @endif
                                                <span class="color-name">{{ $variant->color_name ?: $variant->color ?: 'N/A' }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="variant-price">
                                        <span class="price-amount">${{ number_format($variant->price, 2) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="stock-badge {{ $variant->quantity > 0 ? 'in-stock' : 'out-of-stock' }}">
                                            {{ $variant->quantity }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge {{ $variant->status == 'active' ? 'status-active' : 'status-inactive' }}">
                                            {{ $variant->status == 'active' ? 'Hoạt động' : 'Không hoạt động' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons-wrapper">
                                            <a href="{{ route('admin.product-variants.edit', $variant) }}" 
                                               class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @include('components.delete-form', [
                                                'id' => $variant->id,
                                                'route' => route('admin.product-variants.destroy', $variant),
                                                'message' => "Bạn có chắc chắn muốn xóa biến thể này?"
                                            ])
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        Hiển thị {{ $productVariants->firstItem() ?? 0 }} đến {{ $productVariants->lastItem() ?? 0 }} của {{ $productVariants->total() }} biến thể
                    </div>
                    <div class="pagination-controls">
                        {{ $productVariants->appends(request()->query())->links('components.paginate') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .color-swatch {
        display: inline-block;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px #ddd;
        margin-right: 8px;
        vertical-align: middle;
    }
    
    .color-info {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .color-name {
        font-size: 13px;
        color: #333;
    }
    
    .size-badge {
        display: inline-block;
        background: #f8f9fa;
        color: #495057;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid #dee2e6;
    }
    
    .stock-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        min-width: 30px;
        text-align: center;
    }
    
    .stock-badge.in-stock {
        background-color: #d4edda;
        color: #155724;
    }
    
    .stock-badge.out-of-stock {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .variant-sku code {
        background: #f8f9fa;
        color: #e83e8c;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 11px;
    }
    
    .price-amount {
        font-weight: 600;
        color: #D1A66E;
        font-size: 14px;
    }
    
    .product-name {
        font-weight: 500;
        color: #333;
        margin-bottom: 3px;
    }
    
    .product-desc {
        font-size: 12px;
        color: #666;
        line-height: 1.3;
    }
    
    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .status-active {
        background-color: #d4edda;
        color: #155724;
    }
    
    .status-inactive {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .card-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .back-button {
        display: inline-flex;
        align-items: center;
        padding: 8px 16px;
        background: #6c757d;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    
    .back-button:hover {
        background: #5a6268;
        color: white;
        text-decoration: none;
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto submit when filter changes
    document.getElementById('product_filter')?.addEventListener('change', function() {
        document.querySelector('.filter-form').submit();
    });
    
    document.getElementById('status_filter')?.addEventListener('change', function() {
        document.querySelector('.filter-form').submit();
    });
</script>
@endpush