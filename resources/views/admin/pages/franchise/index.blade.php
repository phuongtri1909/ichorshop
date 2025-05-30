@extends('admin.layouts.sidebar')

@section('title', 'Quản lý gói nhượng quyền')

@section('main-content')
<div class="category-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item current">Gói nhượng quyền</li>
        </ol>
    </div>

    <div class="content-card">
        <div class="card-top">
            <div class="card-title">
                <i class="fas fa-handshake icon-title"></i>
                <h5>Danh sách gói nhượng quyền</h5>
            </div>
            <a href="{{ route('admin.franchise.create') }}" class="action-button">
                <i class="fas fa-plus"></i> Thêm gói nhượng quyền
            </a>
        </div>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <form action="{{ route('admin.franchise.index') }}" method="GET" class="filter-form">
                <div class="filter-group">
                    <div class="filter-item">
                        <label for="name_filter">Tên gói</label>
                        <input type="text" id="name_filter" name="name" class="filter-input" 
                            placeholder="Tìm theo tên gói" value="{{ request('name') }}">
                    </div>
                    <div class="filter-item">
                        <label for="code_filter">Mã gói</label>
                        <input type="text" id="code_filter" name="code" class="filter-input" 
                            placeholder="Tìm theo mã gói" value="{{ request('code') }}">
                    </div>
                    <div class="filter-item">
                        <label for="sort_by">Sắp xếp theo</label>
                        <select id="sort_by" name="sort_by" class="filter-input">
                            <option value="sort_order" {{ request('sort_by') == 'sort_order' ? 'selected' : '' }}>Thứ tự hiển thị</option>
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                        </select>
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="filter-btn">
                        <i class="fas fa-filter"></i> Lọc
                    </button>
                    <a href="{{ route('admin.franchise.index') }}" class="filter-clear-btn">
                        <i class="fas fa-times"></i> Xóa bộ lọc
                    </a>
                </div>
            </form>
        </div>
        
        <div class="card-content">
            
            @if (request('name') || request('code'))
                <div class="active-filters">
                    <span class="active-filters-title">Đang lọc: </span>
                    @if (request('name'))
                        <span class="filter-tag">
                            <span>Tên gói: {{ request('name') }}</span>
                            <a href="{{ request()->url() }}?{{ http_build_query(request()->except('name')) }}" class="remove-filter">×</a>
                        </span>
                    @endif
                    @if (request('code'))
                        <span class="filter-tag">
                            <span>Mã gói: {{ request('code') }}</span>
                            <a href="{{ request()->url() }}?{{ http_build_query(request()->except('code')) }}" class="remove-filter">×</a>
                        </span>
                    @endif
                </div>
            @endif
            
            @if($franchises->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    @if (request('name') || request('code'))
                        <h4>Không tìm thấy gói nhượng quyền nào</h4>
                        <p>Không có gói nhượng quyền nào phù hợp với bộ lọc hiện tại.</p>
                        <a href="{{ route('admin.franchise.index') }}" class="action-button">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    @else
                        <h4>Chưa có gói nhượng quyền nào</h4>
                        <p>Bắt đầu bằng cách thêm gói nhượng quyền đầu tiên.</p>
                        <a href="{{ route('admin.franchise.create') }}" class="action-button">
                            <i class="fas fa-plus"></i> Thêm gói nhượng quyền mới
                        </a>
                    @endif
                </div>
            @else
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="column-small">STT</th>
                                <th class="column-medium">Tên gói</th>
                                <th class="column-medium">Tên hiển thị</th>
                                <th class="column-small">Mã gói</th>
                                <th class="column-small">Thứ tự</th>
                                <th class="column-small">Số đặc điểm</th>
                                <th class="column-small text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($franchises as $index => $item)
                                <tr>
                                    <td class="text-center">{{ ($franchises->currentPage() - 1) * $franchises->perPage() + $index + 1 }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->name_package }}</td>
                                    <td><span class="code-badge">{{ $item->code }}</span></td>
                                    <td class="text-center">{{ $item->sort_order }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info">
                                            {{ count(json_decode($item->details, true) ?? []) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons-wrapper">
                                            <a href="{{ route('admin.franchise.edit', $item) }}" class="action-icon edit-icon" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @include('admin.components.delete-form', [
                                                'id' => $item->id,
                                                'route' => route('admin.franchise.destroy', $item),
                                                'message' => "Bạn có chắc chắn muốn xóa gói nhượng quyền '{$item->name}'?"
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
                        Hiển thị {{ $franchises->firstItem() ?? 0 }} đến {{ $franchises->lastItem() ?? 0 }} của {{ $franchises->total() }} gói nhượng quyền
                    </div>
                    <div class="pagination-controls">
                        {{ $franchises->appends(request()->query())->links('admin.components.paginate') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection