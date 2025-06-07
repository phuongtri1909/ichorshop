@extends('admin.layouts.sidebar')

@section('title', 'Quản lý sản phẩm')

@section('main-content')
<div class="category-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item current">Sản phẩm</li>
        </ol>
    </div>

    <div class="content-card">
        <div class="card-top">
            <div class="card-title">
                <i class="fas fa-coffee icon-title"></i>
                <h5>Danh sách sản phẩm</h5>
            </div>
            <a href="{{ route('admin.products.create') }}" class="action-button">
                <i class="fas fa-plus"></i> Thêm sản phẩm
            </a>
        </div>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <form action="{{ route('admin.products.index') }}" method="GET" class="filter-form">
                <div class="filter-group">
                    <div class="filter-item">
                        <label for="category_filter">Danh mục</label>
                        <select id="category_filter" name="category_id" class="filter-input">
                            <option value="">Tất cả danh mục</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-item">
                        <label for="brand_filter">Thương hiệu</label>
                        <select id="brand_filter" name="brand_id" class="filter-input">
                            <option value="">Tất cả thương hiệu</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-item">
                        <label for="name_filter">Tên sản phẩm</label>
                        <input type="text" id="name_filter" name="name" class="filter-input" 
                            placeholder="Tìm theo tên sản phẩm" value="{{ request('name') }}">
                    </div>
                    <div class="filter-item">
                        <label for="status_filter">Trạng thái</label>
                        <select id="status_filter" name="status" class="filter-input">
                            <option value="">Tất cả trạng thái</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang bán</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Đã ẩn</option>
                        </select>
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="filter-btn">
                        <i class="fas fa-filter"></i> Lọc
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="filter-clear-btn">
                        <i class="fas fa-times"></i> Xóa bộ lọc
                    </a>
                </div>
            </form>
        </div>
        
        <div class="card-content">
            @if($products->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-coffee"></i>
                    </div>
                    @if (request()->hasAny(['category_id', 'brand_id', 'name', 'status']))
                        <h4>Không tìm thấy sản phẩm nào</h4>
                        <p>Không có sản phẩm nào phù hợp với bộ lọc hiện tại.</p>
                        <a href="{{ route('admin.products.index') }}" class="action-button">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    @else
                        <h4>Chưa có sản phẩm nào</h4>
                        <p>Bắt đầu bằng cách thêm sản phẩm đầu tiên.</p>
                        <a href="{{ route('admin.products.create') }}" class="action-button">
                            <i class="fas fa-plus"></i> Thêm sản phẩm mới
                        </a>
                    @endif
                </div>
            @else
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="column-small">STT</th>
                                <th class="column-small">Ảnh</th>
                                <th class="column-large">Tên sản phẩm</th>
                                <th class="column-medium">Thương hiệu</th>
                                <th class="column-medium">Danh mục</th>
                                <th class="column-small">Biến thể</th>
                                <th class="column-small text-center">Trạng thái</th>
                                <th class="column-small text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $index => $product)
                                <tr>
                                    <td class="text-center">{{ ($products->currentPage() - 1) * $products->perPage() + $index + 1 }}</td>
                                    <td class="product-image">
                                        @if($product->avatar)
                                            <img src="{{ $product->avatar_medium_url }}" alt="{{ $product->name }}" class="thumbnail">
                                        @else
                                            <div class="no-image">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="item-name">
                                        <div class="product-name">{{ $product->name }}</div>
                                        @if($product->description_short)
                                            <div class="product-desc">{{ Str::limit($product->description_short, 60) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->brand)
                                            <div class="brand-info">
                                                {{-- @if($product->brand->logo)
                                                    <img src="{{ $product->brand->logo_url }}" alt="{{ $product->brand->name }}" class="brand-logo">
                                                @endif --}}
                                                <span>{{ $product->brand->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted">Chưa có thương hiệu</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->categories->count() > 0)
                                            <div class="category-tags">
                                                @foreach($product->categories->take(2) as $category)
                                                    <span class="category-tag">{{ $category->name }}</span>
                                                @endforeach
                                                @if($product->categories->count() > 2)
                                                    <span class="category-tag more">+{{ $product->categories->count() - 2 }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">Chưa phân loại</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="variant-count">{{ $product->variants->count() }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge {{ $product->status == 'active' ? 'status-active' : 'status-inactive' }}">
                                            {{ $product->status == 'active' ? 'Đang bán' : 'Đã ẩn' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons-wrapper">
                                            {{-- <a href="{{ route('admin.product-variants.index', ['product_id' => $product->id]) }}" 
                                               class="action-icon variant-icon text-decoration-none" title="Quản lý biến thể">
                                                <i class="fas fa-layer-group"></i>
                                            </a> --}}
                                            <a href="{{ route('admin.products.edit', $product) }}" 
                                               class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @include('components.delete-form', [
                                                'id' => $product->id,
                                                'route' => route('admin.products.destroy', $product),
                                                'message' => "Bạn có chắc chắn muốn xóa sản phẩm '{$product->name}'?"
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
                        Hiển thị {{ $products->firstItem() ?? 0 }} đến {{ $products->lastItem() ?? 0 }} của {{ $products->total() }} sản phẩm
                    </div>
                    <div class="pagination-controls">
                        {{ $products->appends(request()->query())->links('components.paginate') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    
    .no-image {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        color: #999;
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
    
    .brand-info {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .category-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }
    
    .category-tag {
        font-size: 11px;
        padding: 2px 6px;
        background: #e3f2fd;
        color: #1976d2;
        border-radius: 10px;
        white-space: nowrap;
    }
    
    .category-tag.more {
        background: #f5f5f5;
        color: #666;
    }
    
    .variant-count {
        display: inline-block;
        background: #fff3cd;
        color: #856404;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        min-width: 24px;
        text-align: center;
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
    
    .variant-icon {
        background: #fff3cd;
        color: #856404;
    }
    
    .variant-icon:hover {
        background: #ffeaa7;
        color: #6c5d00;
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto submit when filter changes
    document.getElementById('category_filter').addEventListener('change', function() {
        document.querySelector('.filter-form').submit();
    });
    
    document.getElementById('brand_filter').addEventListener('change', function() {
        document.querySelector('.filter-form').submit();
    });
    
    document.getElementById('status_filter').addEventListener('change', function() {
        document.querySelector('.filter-form').submit();
    });
</script>
@endpush