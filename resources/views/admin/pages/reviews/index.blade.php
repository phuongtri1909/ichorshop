@extends('admin.layouts.sidebar')

@section('title', 'Quản lý đánh giá')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Đánh giá</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-star icon-title"></i>
                    <h5>Danh sách đánh giá</h5>
                </div>
                <a href="{{ route('admin.reviews.create') }}" class="action-button">
                    <i class="fas fa-plus"></i> Thêm đánh giá
                </a>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.reviews.index') }}" method="GET" class="filter-form">
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
                            <label for="user_filter">Người đánh giá</label>
                            <input type="text" id="user_filter" name="user_name" class="filter-input" 
                                placeholder="Tên người đánh giá" value="{{ request('user_name') }}">
                        </div>
                        <div class="filter-item">
                            <label for="date_from">Từ ngày</label>
                            <input type="date" id="date_from" name="date_from" class="filter-input" 
                                value="{{ request('date_from') }}">
                        </div>
                        <div class="filter-item">
                            <label for="date_to">Đến ngày</label>
                            <input type="date" id="date_to" name="date_to" class="filter-input" 
                                value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.reviews.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('product_id') || request('user_name') || request('date_from') || request('date_to'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('product_id'))
                            <span class="filter-tag">
                                <span>Sản phẩm: {{ $products->find(request('product_id'))->name }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('product_id')) }}" class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('user_name'))
                            <span class="filter-tag">
                                <span>Người đánh giá: {{ request('user_name') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('user_name')) }}" class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('date_from'))
                            <span class="filter-tag">
                                <span>Từ ngày: {{ date('d/m/Y', strtotime(request('date_from'))) }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('date_from')) }}" class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('date_to'))
                            <span class="filter-tag">
                                <span>Đến ngày: {{ date('d/m/Y', strtotime(request('date_to'))) }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('date_to')) }}" class="remove-filter">×</a>
                            </span>
                        @endif
                    </div>
                @endif

                @if ($reviews->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-comment-alt"></i>
                        </div>
                        @if (request('product_id') || request('user_name') || request('date_from') || request('date_to'))
                            <h4>Không tìm thấy đánh giá nào</h4>
                            <p>Không có đánh giá nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.reviews.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có đánh giá nào</h4>
                            <p>Bắt đầu bằng cách thêm đánh giá mẫu đầu tiên.</p>
                            <a href="{{ route('admin.reviews.create') }}" class="action-button">
                                <i class="fas fa-plus"></i> Thêm đánh giá mới
                            </a>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-medium">Người đánh giá</th>
                                    <th class="column-medium">Sản phẩm</th>
                                    <th class="column-large">Nội dung</th>
                                    <th class="column-small text-center">Đánh giá</th>
                                    <th class="column-medium">Ngày đánh giá</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reviews as $index => $review)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($reviews->currentPage() - 1) * $reviews->perPage() + $index + 1 }}</td>
                                        <td>
                                            <div class="reviewer-info">
                                                @if ($review->avatar)
                                                    <img src="{{ asset('storage/' . $review->avatar) }}"
                                                        alt="{{ $review->user_name }}" class="reviewer-avatar">
                                                @else
                                                    <div class="reviewer-avatar-placeholder">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                @endif
                                                <span class="truncate-text" title="{{ $review->user_name }}">
                                                    {{ Str::limit($review->user_name, 25) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="product-name">
                                            <a class="text-info truncate-text" target="_blank"
                                                href="{{ route('admin.products.edit', $review->product->id) }}"
                                                title="{{ $review->product->name }}">
                                                {{ Str::limit($review->product->name, 40) }}
                                            </a>
                                        </td>
                                        <td class="comment-content truncate-text" title="{{ $review->comment }}">
                                            {{ Str::limit($review->comment, 40) }}
                                        </td>
                                        <td class="text-center">
                                            <div class="rating-display">
                                                <div class="stars-container">
                                                    <i class="fas fa-star"></i>
                                                    <span class="rating-number">{{ $review->rating }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.reviews.edit', $review) }}"
                                                    class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @include('components.delete-form', [
                                                    'id' => $review->id,
                                                    'route' => route('admin.reviews.destroy', $review),
                                                    'message' => 'Bạn có chắc chắn muốn xóa đánh giá này?',
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
                            Hiển thị {{ $reviews->firstItem() ?? 0 }} đến {{ $reviews->lastItem() ?? 0 }} của
                            {{ $reviews->total() }} đánh giá
                        </div>
                        <div class="pagination-controls">
                            {{ $reviews->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Reviews specific styles */
    .reviewer-info {
        display: flex;
        align-items: center;
    }
    
    .reviewer-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 10px;
        flex-shrink: 0;
    }
    
    .reviewer-avatar-placeholder {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #f0f0f0;
        color: #999;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        flex-shrink: 0;
    }
    
    .product-name {
        font-weight: 500;
    }
    
    .truncate-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        display: inline-block;
    }
    
    .rating-display {
        display: flex;
        justify-content: center;
    }
    
    .stars-container {
        color: #f7c427;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .stars-container .fa-star {
        font-size: 16px;
    }
    
    .rating-number {
        font-size: 14px;
        font-weight: 600;
        color: #666;
    }
    
    .comment-content {
        color: #555;
    }
</style>
@endpush

@push('scripts')
<script>
    // Khi thay đổi bộ lọc sản phẩm, tự động submit form
    document.getElementById('product_filter').addEventListener('change', function() {
        document.querySelector('.filter-form').submit();
    });
    
    // Tự động submit khi chọn ngày
    document.getElementById('date_from').addEventListener('change', function() {
        // Nếu date_to chưa có giá trị và date_from có giá trị mới
        if (!document.getElementById('date_to').value && this.value) {
            // Đặt date_to thành ngày hiện tại
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            document.getElementById('date_to').value = `${year}-${month}-${day}`;
        }
        document.querySelector('.filter-form').submit();
    });
    
    document.getElementById('date_to').addEventListener('change', function() {
        document.querySelector('.filter-form').submit();
    });
</script>
@endpush