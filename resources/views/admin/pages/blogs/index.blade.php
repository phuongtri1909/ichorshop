@extends('admin.layouts.sidebar')

@section('title', 'Quản lý bài viết')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Bài viết</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-newspaper icon-title"></i>
                    <h5>Danh sách bài viết</h5>
                </div>
                <a href="{{ route('admin.blogs.create') }}" class="action-button">
                    <i class="fas fa-plus"></i> Thêm bài viết
                </a>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.blogs.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-3">
                            <label for="title_filter">Tiêu đề</label>
                            <input type="text" id="title_filter" name="title" class="filter-input"
                                placeholder="Tìm theo tiêu đề" value="{{ request('title') }}">
                        </div>
                        <div class="col-3">
                            <label for="category_filter">Danh mục</label>
                            <select id="category_filter" name="category_id" class="filter-input">
                                <option value="">Tất cả danh mục</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="status_filter">Trạng thái</label>
                            <select id="status_filter" name="status" class="filter-input">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hiển thị
                                </option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Đã ẩn
                                </option>
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="featured_filter">Nổi bật</label>
                            <select id="featured_filter" name="featured" class="filter-input">
                                <option value="">Tất cả</option>
                                <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>Bài viết nổi bật
                                </option>
                                <option value="0" {{ request('featured') == '0' ? 'selected' : '' }}>Bài viết thường
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.blogs.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('title') || request('category_id') || request('status') || request('featured'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('title'))
                            <span class="filter-tag">
                                <span>Tiêu đề: {{ request('title') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('title')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('category_id'))
                            <span class="filter-tag">
                                <span>Danh mục: {{ $categories->find(request('category_id'))->name }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('category_id')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('status'))
                            <span class="filter-tag">
                                <span>Trạng thái: {{ request('status') == 'active' ? 'Đang hiển thị' : 'Đã ẩn' }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('status')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('featured') !== null && request('featured') !== '')
                            <span class="filter-tag">
                                <span>Nổi bật: {{ request('featured') == '1' ? 'Có' : 'Không' }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('featured')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                    </div>
                @endif

                @if ($blogs->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        @if (request('title') || request('category_id') || request('status') || request('featured'))
                            <h4>Không tìm thấy bài viết nào</h4>
                            <p>Không có bài viết nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.blogs.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có bài viết nào</h4>
                            <p>Bắt đầu bằng cách thêm bài viết đầu tiên.</p>
                            <a href="{{ route('admin.blogs.create') }}" class="action-button">
                                <i class="fas fa-plus"></i> Thêm bài viết mới
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
                                    <th class="column-medium">Danh mục</th>
                                    <th class="column-medium">Ngày tạo</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                    <th class="column-small text-center">Nổi bật</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($blogs as $index => $blog)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($blogs->currentPage() - 1) * $blogs->perPage() + $index + 1 }}</td>
                                        <td class="blog-image">
                                            <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}"
                                                class="thumbnail">
                                        </td>
                                        <td class="item-title">
                                            {{ $blog->title }}
                                        </td>
                                        <td>
                                            @foreach ($blog->categories as $category)
                                                <span class="category-badge">{{ $category->name }}</span>
                                            @endforeach
                                        </td>
                                        <td class="blog-date">
                                            {{ $blog->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="status-badge {{ $blog->is_active ? 'status-active' : 'status-inactive' }}">
                                                {{ $blog->is_active ? 'Đang hiển thị' : 'Đã ẩn' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if ($blog->is_featured)
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
                                                <a href="{{ route('admin.blogs.edit', $blog) }}"
                                                    class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @include('components.delete-form', [
                                                    'id' => $blog->id,
                                                    'route' => route('admin.blogs.destroy', $blog),
                                                    'message' => "Bạn có chắc chắn muốn xóa bài viết '{$blog->title}'?",
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
                            Hiển thị {{ $blogs->firstItem() ?? 0 }} đến {{ $blogs->lastItem() ?? 0 }} của
                            {{ $blogs->total() }} bài viết
                        </div>
                        <div class="pagination-controls">
                            {{ $blogs->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .blog-image img {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
        }

        .category-badge {
            display: inline-block;
            background: #e3f2fd;
            color: #1976d2;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-right: 4px;
            margin-bottom: 4px;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
        }

        .status-active {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .status-inactive {
            background-color: #f5f5f5;
            color: #757575;
        }

        .featured-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Khi thay đổi bộ lọc danh mục, trạng thái hoặc nổi bật, tự động submit form
        document.getElementById('category_filter').addEventListener('change', function() {
            document.querySelector('.filter-form').submit();
        });

        document.getElementById('status_filter').addEventListener('change', function() {
            document.querySelector('.filter-form').submit();
        });

        document.getElementById('featured_filter').addEventListener('change', function() {
            document.querySelector('.filter-form').submit();
        });
    </script>
@endpush
