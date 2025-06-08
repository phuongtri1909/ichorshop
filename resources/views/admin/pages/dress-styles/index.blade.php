@extends('admin.layouts.sidebar')

@section('title', 'Quản lý kiểu dáng')

@section('main-content')
<div class="category-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item current">Kiểu dáng</li>
        </ol>
    </div>

    <div class="content-card">
        <div class="card-top">
            <div class="card-title">
                <i class="fas fa-tshirt icon-title"></i>
                <h5>Danh sách kiểu dáng</h5>
            </div>
            <a href="{{ route('admin.dress-styles.create') }}" class="action-button">
                <i class="fas fa-plus"></i> Thêm kiểu dáng
            </a>
        </div>
        
        <div class="card-content">
            @if($dressStyles->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <h4>Chưa có kiểu dáng nào</h4>
                    <p>Bắt đầu bằng cách thêm kiểu dáng đầu tiên.</p>
                    <a href="{{ route('admin.dress-styles.create') }}" class="action-button">
                        <i class="fas fa-plus"></i> Thêm kiểu dáng mới
                    </a>
                </div>
            @else
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="column-small">STT</th>
                                <th class="column-large">Tên kiểu dáng</th>
                                <th class="column-large">Mô tả</th>
                                <th class="column-small text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dressStyles as $index => $style)
                                <tr>
                                    <td class="text-center">{{ ($dressStyles->currentPage() - 1) * $dressStyles->perPage() + $index + 1 }}</td>
                                    <td class="item-name">{{ $style->name }}</td>
                                    <td class="item-description">{{ Str::limit($style->description, 80) }}</td>
                                    <td>
                                        <div class="action-buttons-wrapper">
                                            <a href="{{ route('admin.dress-styles.edit', $style) }}" class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @include('components.delete-form', [
                                                'id' => $style->id,
                                                'route' => route('admin.dress-styles.destroy', $style),
                                                'message' => "Bạn có chắc chắn muốn xóa kiểu dáng '{$style->name}'?"
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
                        Hiển thị {{ $dressStyles->firstItem() ?? 0 }} đến {{ $dressStyles->lastItem() ?? 0 }} của {{ $dressStyles->total() }} kiểu dáng
                    </div>
                    <div class="pagination-controls">
                        {{ $dressStyles->links('components.paginate') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection