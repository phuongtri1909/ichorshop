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
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.reviews.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-3">
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
                        <div class="col-3">
                            <label for="user_filter">Người đánh giá</label>
                            <input type="text" id="user_filter" name="full_name" class="filter-input" 
                                placeholder="Tên người đánh giá" value="{{ request('full_name') }}">
                        </div>
                        <div class="col-3">
                            <label for="status_filter">Trạng thái</label>
                            <select id="status_filter" name="status" class="filter-input">
                                <option value="">Tất cả trạng thái</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>
                                    Đã xuất bản
                                </option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                    Chờ duyệt
                                </option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                    Đã từ chối
                                </option>
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="date_from">Từ ngày</label>
                            <input type="date" id="date_from" name="date_from" class="filter-input" 
                                value="{{ request('date_from') }}">
                        </div>
                        <div class="col-3">
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
                @if (request('product_id') || request('full_name') || request('date_from') || request('date_to') || request('status'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('product_id'))
                            <span class="filter-tag">
                                <span>Sản phẩm: {{ $products->find(request('product_id'))->name }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('product_id')) }}" class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('full_name'))
                            <span class="filter-tag">
                                <span>Người đánh giá: {{ request('full_name') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('full_name')) }}" class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('status'))
                            <span class="filter-tag">
                                <span>Trạng thái: 
                                    @if(request('status') == 'published')
                                        Đã xuất bản
                                    @elseif(request('status') == 'pending')
                                        Chờ duyệt
                                    @elseif(request('status') == 'rejected')
                                        Đã từ chối
                                    @endif
                                </span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('status')) }}" class="remove-filter">×</a>
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
                        @if (request('product_id') || request('full_name') || request('date_from') || request('date_to') || request('status'))
                            <h4>Không tìm thấy đánh giá nào</h4>
                            <p>Không có đánh giá nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.reviews.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có đánh giá nào</h4>
                            <p>Hiện chưa có đánh giá nào của khách hàng.</p>
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
                                    <th class="column-small text-center">Trạng thái</th>
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
                                                <div class="reviewer-avatar-placeholder">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <span class="truncate-text" title="{{ $review->user->full_name }} {{ $review->user->last_name }}">
                                                    {{ $review->user->full_name }} 
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
                                        <td class="comment-content truncate-text" title="{{ $review->content }}">
                                            {{ Str::limit($review->content, 40) }}
                                        </td>
                                        <td class="text-center">
                                            <div class="rating-display">
                                                <div class="stars-container">
                                                    <i class="fas fa-star"></i>
                                                    <span class="rating-number">{{ $review->rating }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <span class="status-badge status-{{ $review->status }}" 
                                                      id="reviewStatus{{ $review->id }}" 
                                                      data-bs-toggle="dropdown" 
                                                      aria-expanded="false"
                                                      role="button">
                                                    @if($review->status == 'published')
                                                        <i class="fas fa-check-circle"></i> Đã xuất bản
                                                    @elseif($review->status == 'pending')
                                                        <i class="fas fa-clock"></i> Chờ duyệt
                                                    @else
                                                        <i class="fas fa-ban"></i> Đã từ chối
                                                    @endif
                                                </span>
                                                <ul class="dropdown-menu status-dropdown" aria-labelledby="reviewStatus{{ $review->id }}">
                                                    <li>
                                                        <form action="{{ route('admin.reviews.update-status', $review->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="published">
                                                            <button type="submit" class="dropdown-item status-action {{ $review->status == 'published' ? 'active' : '' }}">
                                                                <i class="fas fa-check-circle text-success"></i> Xuất bản
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('admin.reviews.update-status', $review->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="pending">
                                                            <button type="submit" class="dropdown-item status-action {{ $review->status == 'pending' ? 'active' : '' }}">
                                                                <i class="fas fa-clock text-warning"></i> Chờ duyệt
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('admin.reviews.update-status', $review->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="rejected">
                                                            <button type="submit" class="dropdown-item status-action {{ $review->status == 'rejected' ? 'active' : '' }}">
                                                                <i class="fas fa-ban text-danger"></i> Từ chối
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
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
    
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
    }
    
    .status-badge i {
        margin-right: 4px;
    }
    
    .status-published {
        background-color: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
    }
    
    .status-pending {
        background-color: #fff8e1;
        color: #f57c00;
        border: 1px solid #ffecb3;
    }
    
    .status-rejected {
        background-color: #ffebee;
        color: #c62828;
        border: 1px solid #ffcdd2;
    }
    
    .status-dropdown .dropdown-item {
        display: flex;
        align-items: center;
        padding: 8px 15px;
    }
    
    .status-dropdown .dropdown-item i {
        margin-right: 8px;
    }
    
    .status-dropdown .dropdown-item.active {
        background-color: #f8f9fa;
        color: inherit;
        font-weight: bold;
    }
    
    .status-dropdown .dropdown-item:active {
        background-color: #f8f9fa;
        color: inherit;
    }
</style>
@endpush

@push('scripts')
<script>
    // Khi thay đổi bộ lọc sản phẩm, tự động submit form
    document.getElementById('product_filter').addEventListener('change', function() {
        document.querySelector('.filter-form').submit();
    });
    
    // Khi thay đổi bộ lọc trạng thái, tự động submit form
    document.getElementById('status_filter').addEventListener('change', function() {
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