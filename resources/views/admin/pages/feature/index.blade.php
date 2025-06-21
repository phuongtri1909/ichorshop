@extends('admin.layouts.sidebar')

@section('title', 'Quản lý Feature Section')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Feature Sections</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-list-alt icon-title"></i>
                    <h5>Danh sách Feature Sections</h5>
                </div>
                <a href="{{ route('admin.feature-sections.create') }}" class="action-button">
                    <i class="fas fa-plus"></i> Thêm Section
                </a>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.feature-sections.index') }}" method="GET" class="filter-form">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="title_filter" class="form-label">Tiêu đề</label>
                                <input type="text" id="title_filter" name="title" class="form-control filter-input"
                                    placeholder="Nhập tiêu đề" value="{{ request('title') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-6 d-flex align-items-end">
                            <div class="filter-actions">
                                <button type="submit" class="filter-btn btn btn-primary me-2">
                                    <i class="fas fa-filter"></i> Lọc
                                </button>
                                <a href="{{ route('admin.feature-sections.index') }}"
                                    class="filter-clear-btn btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Xóa bộ lọc
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('title'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('title'))
                            <span class="filter-tag">
                                <span>Tiêu đề: {{ request('title') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('title')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                    </div>
                @endif

                @if ($featureSections->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-list-alt"></i>
                        </div>
                        @if (request('title'))
                            <h4>Không tìm thấy Feature Section nào</h4>
                            <p>Không có Feature Section nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.feature-sections.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có Feature Section nào</h4>
                            <p>Bắt đầu bằng cách thêm Feature Section đầu tiên.</p>
                            <a href="{{ route('admin.feature-sections.create') }}" class="action-button">
                                <i class="fas fa-plus"></i> Thêm Feature Section
                            </a>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small text-center">#</th>
                                    <th class="column-large">Tiêu đề</th>
                                    <th class="column-medium">Items</th>
                                    <th class="column-small">Button</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($featureSections as $index => $section)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="font-weight-medium">{{ $section->title }}</span>
                                                @if ($section->description)
                                                    <small class="text-muted">{{ Str::limit($section->description, 80) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.feature-sections.items.index', $section) }}" class="btn btn-sm btn-outline-primary">
                                                <span class="badge bg-info">{{ $section->items_count }}</span> Items
                                            </a>
                                        </td>
                                        <td>
                                            @if ($section->button_text)
                                                <span class="badge bg-secondary">{{ $section->button_text }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.feature-sections.edit', $section) }}"
                                                    class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @include('components.delete-form', [
                                                    'id' => $section->id,
                                                    'route' => route('admin.feature-sections.destroy', $section),
                                                    'message' => 'Bạn có chắc chắn muốn xóa Feature Section này? Tất cả các Feature Item liên quan cũng sẽ bị xóa.',
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
                            Hiển thị {{ $featureSections->firstItem() ?? 0 }} đến {{ $featureSections->lastItem() ?? 0 }} của
                            {{ $featureSections->total() }} Feature Sections
                        </div>
                        <div class="pagination-controls">
                            {{ $featureSections->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
