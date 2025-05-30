@extends('admin.layouts.sidebar')

@section('title', 'Quản lý liên hệ')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Liên hệ</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-phone-alt icon-title"></i>
                    <h5>Danh sách liên hệ</h5>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.contacts.index') }}" method="GET" class="filter-form">
                    <div class="filter-group">
                        <div class="filter-item">
                            <label for="contact_id">Mã liên hệ</label>
                            <input type="text" id="contact_id" name="contact_id" class="filter-input"
                                placeholder="Nhập mã liên hệ" value="{{ request('contact_id') }}">
                        </div>
                        <div class="filter-item">
                            <label for="customer_filter">Khách hàng</label>
                            <input type="text" id="customer_filter" name="customer" class="filter-input"
                                placeholder="Tên hoặc SĐT khách hàng" value="{{ request('customer') }}">
                        </div>
                        <div class="filter-item">
                            <label for="status_filter">Trạng thái</label>
                            <select id="status_filter" name="status" class="filter-input">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ liên hệ
                                </option>
                                <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Đã liên
                                    hệ</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy
                                </option>
                            </select>
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
                        <a href="{{ route('admin.contacts.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                

                @if (request('contact_id') || request('customer') || request('status') || request('date_from') || request('date_to'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('contact_id'))
                            <span class="filter-tag">
                                <span>Mã liên hệ: {{ request('contact_id') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('contact_id')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('customer'))
                            <span class="filter-tag">
                                <span>Khách hàng: {{ request('customer') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('customer')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('status'))
                            <span class="filter-tag">
                                <span>Trạng thái:
                                    @switch(request('status'))
                                        @case('pending')
                                            Chờ liên hệ
                                        @break

                                        @case('contacted')
                                            Đã liên hệ
                                        @break

                                        @case('cancelled')
                                            Đã hủy
                                        @break

                                        @default
                                            {{ request('status') }}
                                    @endswitch
                                </span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('status')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('date_from'))
                            <span class="filter-tag">
                                <span>Từ ngày: {{ date('d/m/Y', strtotime(request('date_from'))) }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('date_from')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('date_to'))
                            <span class="filter-tag">
                                <span>Đến ngày: {{ date('d/m/Y', strtotime(request('date_to'))) }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('date_to')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                    </div>
                @endif

                @if ($contacts->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        @if (request('contact_id') || request('customer') || request('status') || request('date_from') || request('date_to'))
                            <h4>Không tìm thấy liên hệ nào</h4>
                            <p>Không có liên hệ nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.contacts.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có liên hệ nào</h4>
                            <p>Hiện chưa có liên hệ nào trong hệ thống.</p>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">Mã liên hệ</th>
                                    <th class="column-medium">Khách hàng</th>
                                    <th class="column-small">Số điện thoại</th>
                                    <th class="column-small">Ngày liên hệ</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contacts as $contact)
                                    <tr>
                                        <td><span class="contact-code">{{ $contact->id }}</span></td>
                                        <td>
                                            <span class="customer-name">{{ $contact->full_name }}</span>
                                        </td>
                                        <td>{{ $contact->phone }}</td>
                                        <td>{{ $contact->created_at->format('d/m/Y') }}</td>
                                        <td class="text-center">
                                            <span class="status-badge status-{{ $contact->status }}">
                                                @switch($contact->status)
                                                    @case('pending')
                                                        Chờ liên hệ
                                                    @break

                                                    @case('contacted')
                                                        Đã liên hệ
                                                    @break

                                                    @case('cancelled')
                                                        Đã hủy
                                                    @break

                                                    @default
                                                        {{ $contact->status }}
                                                @endswitch
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.contacts.show', $contact->id) }}"
                                                    class="action-icon view-icon text-decoration-none"
                                                    title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @include('admin.components.delete-form', [
                                                    'id' => $contact->id,
                                                    'route' => route('admin.contacts.destroy', $contact),
                                                    'message' =>
                                                        'Bạn có chắc chắn muốn xóa liên hệ này?',
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
                            Hiển thị {{ $contacts->firstItem() ?? 0 }} đến {{ $contacts->lastItem() ?? 0 }} của
                            {{ $contacts->total() }} liên hệ
                        </div>
                        <div class="pagination-controls">
                            {{ $contacts->appends(request()->query())->links('admin.components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        // Khi thay đổi bộ lọc, tự động submit form
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
