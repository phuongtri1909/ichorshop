@extends('admin.layouts.sidebar')

@section('title', 'Quản lý Feature Items')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.feature-sections.index') }}">Feature Sections</a></li>
                <li class="breadcrumb-item current">Feature Items</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-list-alt icon-title"></i>
                    <h5>Danh sách Feature Items - {{ $featureSection->title }}</h5>
                </div>
                <div class="d-flex">
                    <a href="{{ route('admin.feature-sections.edit', $featureSection) }}" class="btn btn-secondary me-2">
                        <i class="fas fa-edit"></i> Sửa Section
                    </a>
                    <a href="{{ route('admin.feature-sections.items.create', $featureSection) }}" class="action-button">
                        <i class="fas fa-plus"></i> Thêm Item
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="section-summary mb-4">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="section-info">
                                <h5>Thông tin Section</h5>
                                <div class="info-item">
                                    <strong>Tiêu đề:</strong> {{ $featureSection->title }}
                                </div>
                                @if($featureSection->description)
                                <div class="info-item">
                                    <strong>Mô tả:</strong> {{ $featureSection->description }}
                                </div>
                                @endif
                                @if($featureSection->button_text)
                                <div class="info-item">
                                    <strong>Nút:</strong> {{ $featureSection->button_text }} 
                                    @if($featureSection->button_link)
                                    <span class="text-muted">({{ $featureSection->button_link }})</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="section-stats">
                                <div class="stat-card">
                                    <div class="stat-title">Số lượng Items</div>
                                    <div class="stat-value">{{ $items->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($items->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-list-ul"></i>
                        </div>
                        <h4>Chưa có Feature Item nào</h4>
                        <p>Bắt đầu bằng cách thêm Feature Item đầu tiên.</p>
                        <a href="{{ route('admin.feature-sections.items.create', $featureSection) }}" class="action-button">
                            <i class="fas fa-plus"></i> Thêm Feature Item
                        </a>
                    </div>
                @else
                    <div class="data-table-container" id="sortable-items">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small text-center" width="80">Thứ tự</th>
                                    <th class="column-small text-center" width="100">Icon</th>
                                    <th class="column-medium">Tiêu đề</th>
                                    <th class="column-large">Mô tả</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-list">
                                @foreach ($items as $index => $item)
                                    <tr data-item-id="{{ $item->id }}" class="sortable-item">
                                        <td class="text-center">
                                            <div class="drag-handle">
                                                <i class="fas fa-grip-vertical"></i>
                                                <span class="order-number">{{ $item->sort_order }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="feature-icon">
                                                @if (pathinfo($item->icon, PATHINFO_EXTENSION) === 'svg')
                                                    {!! file_get_contents(storage_path('app/public/' . $item->icon)) !!}
                                                @else
                                                    <img src="{{ Storage::url($item->icon) }}" alt="{{ $item->title }}" width="50" height="50">
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $item->title }}</td>
                                        <td class="truncate-text" title="{{ $item->description }}">
                                            {{ Str::limit($item->description, 80) }}
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.feature-sections.items.edit', [$featureSection, $item]) }}"
                                                    class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @include('components.delete-form', [
                                                    'id' => $item->id,
                                                    'route' => route('admin.feature-sections.items.destroy', [$featureSection, $item]),
                                                    'message' => 'Bạn có chắc chắn muốn xóa Feature Item này?',
                                                ])
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .section-summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .section-info {
            margin-bottom: 15px;
        }

        .info-item {
            margin-bottom: 5px;
        }

        .stat-card {
            background-color: #e9ecef;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
        }

        .stat-title {
            font-size: 14px;
            color: #6c757d;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #495057;
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .feature-icon svg {
            max-width: 50px;
            max-height: 50px;
        }
        
        .drag-handle {
            cursor: move;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .drag-handle i {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .order-number {
            font-size: 12px;
            color: #6c757d;
        }

        .truncate-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 250px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sortableList = document.getElementById('sortable-list');
            if (sortableList) {
                const sortable = new Sortable(sortableList, {
                    handle: '.drag-handle',
                    animation: 150,
                    onEnd: function(evt) {
                        updateOrder();
                    }
                });
                
                function updateOrder() {
                    const items = [];
                    document.querySelectorAll('.sortable-item').forEach(function(item, index) {
                        const itemId = item.getAttribute('data-item-id');
                        items.push(itemId);
                        item.querySelector('.order-number').textContent = index + 1;
                    });
                    
                    // Send the new order to the server
                    fetch('{{ route("admin.feature-sections.items.reorder", $featureSection) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ items: items })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Order updated successfully');
                        }
                    })
                    .catch(error => {
                        console.error('Error updating order:', error);
                    });
                }
            }
        });
    </script>
@endpush