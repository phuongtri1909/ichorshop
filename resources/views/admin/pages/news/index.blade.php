@extends('admin.layouts.sidebar')

@section('title', 'Quản lý tin tức')

@section('main-content')
<div class="category-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item current">Tin tức</li>
        </ol>
    </div>

    <div class="content-card">
        <div class="card-top">
            <div class="card-title">
                <i class="fas fa-newspaper icon-title"></i>
                <h5>Danh sách tin tức</h5>
            </div>
            <a href="{{ route('admin.news.create') }}" class="action-button">
                <i class="fas fa-plus"></i> Thêm tin tức
            </a>
        </div>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <form action="{{ route('admin.news.index') }}" method="GET" class="filter-form">
                <div class="filter-group">
                    <div class="filter-item">
                        <label for="title_filter">Tiêu đề</label>
                        <input type="text" id="title_filter" name="title" class="filter-input" 
                            placeholder="Tìm theo tiêu đề" value="{{ request('title') }}">
                    </div>
                    <div class="filter-item">
                        <label for="status_filter">Trạng thái</label>
                        <select id="status_filter" name="status" class="filter-input">
                            <option value="">Tất cả trạng thái</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hiển thị</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Đã ẩn</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label for="featured_filter">Nổi bật</label>
                        <select id="featured_filter" name="featured" class="filter-input">
                            <option value="">Tất cả</option>
                            <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>Bài viết nổi bật</option>
                            <option value="0" {{ request('featured') == '0' ? 'selected' : '' }}>Bài viết thường</option>
                        </select>
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="filter-btn">
                        <i class="fas fa-filter"></i> Lọc
                    </button>
                    <a href="{{ route('admin.news.index') }}" class="filter-clear-btn">
                        <i class="fas fa-times"></i> Xóa bộ lọc
                    </a>
                </div>
            </form>
        </div>
        
        <div class="card-content">
            @if (request('title') || request('status') || request('featured'))
                <div class="active-filters">
                    <span class="active-filters-title">Đang lọc: </span>
                    @if (request('title'))
                        <span class="filter-tag">
                            <span>Tiêu đề: {{ request('title') }}</span>
                            <a href="{{ request()->url() }}?{{ http_build_query(request()->except('title')) }}" class="remove-filter">×</a>
                        </span>
                    @endif
                    @if (request('status'))
                        <span class="filter-tag">
                            <span>Trạng thái: {{ request('status') == 'active' ? 'Đang hiển thị' : 'Đã ẩn' }}</span>
                            <a href="{{ request()->url() }}?{{ http_build_query(request()->except('status')) }}" class="remove-filter">×</a>
                        </span>
                    @endif
                    @if (request('featured') !== null && request('featured') !== '')
                        <span class="filter-tag">
                            <span>Nổi bật: {{ request('featured') == '1' ? 'Có' : 'Không' }}</span>
                            <a href="{{ request()->url() }}?{{ http_build_query(request()->except('featured')) }}" class="remove-filter">×</a>
                        </span>
                    @endif
                </div>
            @endif
            
            @if($news->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    @if (request('title') || request('status') || request('featured'))
                        <h4>Không tìm thấy tin tức nào</h4>
                        <p>Không có tin tức nào phù hợp với bộ lọc hiện tại.</p>
                        <a href="{{ route('admin.news.index') }}" class="action-button">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    @else
                        <h4>Chưa có tin tức nào</h4>
                        <p>Bắt đầu bằng cách thêm tin tức đầu tiên.</p>
                        <a href="{{ route('admin.news.create') }}" class="action-button">
                            <i class="fas fa-plus"></i> Thêm tin tức mới
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
                                <th class="column-large">Tiêu đề</th>
                                <th class="column-medium">Ngày tạo</th>
                                <th class="column-small text-center">Trạng thái</th>
                                <th class="column-small text-center">Nổi bật</th>
                                <th class="column-small text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($news as $index => $item)
                                <tr>
                                    <td class="text-center">{{ ($news->currentPage() - 1) * $news->perPage() + $index + 1 }}</td>
                                    <td class="news-image">
                                        <img src="{{ asset('storage/'.$item->thumbnail) }}" alt="{{ $item->title }}" class="thumbnail">
                                    </td>
                                    <td class="item-title">
                                        {{ $item->title }}
                                    </td>
                                    <td class="news-date">
                                        {{ $item->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge {{ $item->is_active ? 'status-active' : 'status-inactive' }}">
                                            {{ $item->is_active ? 'Đang hiển thị' : 'Đã ẩn' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($item->is_featured)
                                            <span class="featured-badge">
                                                <i class="fas fa-star text-warning"></i>
                                            </span>
                                        @else
                                            <span class="normal-badge">
                                                <i class="fas fa-minus text-muted"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons-wrapper">
                                            <a href="{{ route('admin.news.edit', $item) }}" class="action-icon edit-icon" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @include('components.delete-form', [
                                                'id' => $item->id,
                                                'route' => route('admin.news.destroy', $item),
                                                'message' => "Bạn có chắc chắn muốn xóa tin tức '{$item->title}'?"
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
                        Hiển thị {{ $news->firstItem() ?? 0 }} đến {{ $news->lastItem() ?? 0 }} của {{ $news->total() }} tin tức
                    </div>
                    <div class="pagination-controls">
                        {{ $news->appends(request()->query())->links('components.paginate') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Khi thay đổi bộ lọc trạng thái hoặc nổi bật, tự động submit form
    document.getElementById('status_filter').addEventListener('change', function() {
        document.querySelector('.filter-form').submit();
    });
    
    document.getElementById('featured_filter').addEventListener('change', function() {
        document.querySelector('.filter-form').submit();
    });
</script>
@endpush