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
            @if (request('category_id') || request('name') || request('status'))
                <div class="active-filters">
                    <span class="active-filters-title">Đang lọc: </span>
                    @if (request('category_id'))
                        <span class="filter-tag">
                            <span>Danh mục: {{ $categories->find(request('category_id'))->name }}</span>
                            <a href="{{ request()->url() }}?{{ http_build_query(request()->except('category_id')) }}" class="remove-filter">×</a>
                        </span>
                    @endif
                    @if (request('name'))
                        <span class="filter-tag">
                            <span>Tên sản phẩm: {{ request('name') }}</span>
                            <a href="{{ request()->url() }}?{{ http_build_query(request()->except('name')) }}" class="remove-filter">×</a>
                        </span>
                    @endif
                    @if (request('status'))
                        <span class="filter-tag">
                            <span>Trạng thái: {{ request('status') == 'active' ? 'Đang bán' : 'Đã ẩn' }}</span>
                            <a href="{{ request()->url() }}?{{ http_build_query(request()->except('status')) }}" class="remove-filter">×</a>
                        </span>
                    @endif
                </div>
            @endif
            
            @if($products->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-coffee"></i>
                    </div>
                    @if (request('category_id') || request('name') || request('status'))
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
                                <th class="column-medium">Danh mục</th>
                                <th class="column-medium">Giá</th>
                                <th class="column-small text-center">Trạng thái</th>
                                <th class="column-small text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $index => $product)
                                <tr>
                                    <td class="text-center">{{ ($products->currentPage() - 1) * $products->perPage() + $index + 1 }}</td>
                                    <td class="product-image">
                                        <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="thumbnail">
                                    </td>
                                    <td class="item-name">
                                        {{ $product->name }}
                                        @if($product->is_featured)
                                            <span class="featured-badge">Nổi bật</span>
                                        @endif
                                    </td>
                                    <td>{{ $product->category->name }}</td>
                                    <td class="product-price">
                                        @if($product->weights->count() > 0)
                                            @php $defaultWeight = $product->default_weight; @endphp
                                            <div class="price-info">
                                                @if($defaultWeight->discount_percent > 0)
                                                    <span class="original-price">{{ number_format($defaultWeight->original_price) }}đ</span>
                                                    <span class="discounted-price">{{ number_format($defaultWeight->discounted_price) }}đ</span>
                                                @else
                                                    <span class="single-price">{{ number_format($defaultWeight->original_price) }}đ</span>
                                                @endif
                                            </div>
                                            <div class="weight-info">{{ $defaultWeight->weight }}</div>
                                        @else
                                            <span class="no-price">Chưa có giá</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge {{ $product->is_active ? 'status-active' : 'status-inactive' }}">
                                            {{ $product->is_active ? 'Đang bán' : 'Đã ẩn' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons-wrapper">
                                            <a href="{{ route('admin.products.edit', $product) }}" class="action-icon edit-icon" title="Chỉnh sửa">
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
                        {{ $products->appends(request()->query())->links('admin.components.paginate') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Products specific styles */
    .thumbnail {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #eee;
    }
    
    .product-price {
        line-height: 1.3;
    }
    
    .price-info {
        margin-bottom: 3px;
    }
    
    .original-price {
        text-decoration: line-through;
        color: #999;
        font-size: 12px;
        margin-right: 5px;
    }
    
    .discounted-price, .single-price {
        font-weight: 600;
        color: #d35400;
    }
    
    .weight-info {
        font-size: 13px;
        color: #666;
    }
    
    .no-price {
        color: #999;
        font-style: italic;
        font-size: 13px;
    }
    
    .featured-badge {
        display: inline-block;
        background-color: #fef9e7;
        color: #f39c12;
        font-size: 11px;
        padding: 2px 6px;
        border-radius: 30px;
        margin-left: 6px;
        font-weight: 500;
    }
    
    .status-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .status-active {
        background-color: #e3fcef;
        color: #00875a;
    }
    
    .status-inactive {
        background-color: #f1f1f1;
        color: #666;
    }
</style>
@endpush
@push('scripts')
<script>
    // Khi thay đổi bộ lọc danh mục, tự động submit form
    document.getElementById('category_filter').addEventListener('change', function() {
        document.querySelector('.filter-form').submit();
    });
    
    // Khi thay đổi bộ lọc trạng thái, tự động submit form
    document.getElementById('status_filter').addEventListener('change', function() {
        document.querySelector('.filter-form').submit();
    });
</script>
@endpush