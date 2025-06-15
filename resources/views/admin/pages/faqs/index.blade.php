@extends('admin.layouts.sidebar')

@section('title', 'Quản lý câu hỏi thường gặp')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Câu hỏi thường gặp</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-question-circle icon-title"></i>
                    <h5>Danh sách câu hỏi thường gặp</h5>
                </div>
                <a href="{{ route('admin.faqs.create') }}" class="action-button">
                    <i class="fas fa-plus"></i> Thêm câu hỏi
                </a>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.faqs.index') }}" method="GET" class="filter-form">
                    <div class="row g-3">
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label for="question_filter" class="form-label">Câu hỏi</label>
                                <input type="text" id="question_filter" name="question" class="form-control filter-input"
                                    placeholder="Nhập từ khóa" value="{{ request('question') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label for="order_filter" class="form-label">Thứ tự</label>
                                <input type="number" id="order_filter" name="order" class="form-control filter-input"
                                    placeholder="Thứ tự hiển thị" value="{{ request('order') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label for="date_from" class="form-label">Từ ngày</label>
                                <input type="date" id="date_from" name="date_from" class="form-control filter-input"
                                    value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label for="date_to" class="form-label">Đến ngày</label>
                                <input type="date" id="date_to" name="date_to" class="form-control filter-input"
                                    value="{{ request('date_to') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12 d-flex justify-content-end">
                            <div class="filter-actions">
                                <button type="submit" class="filter-btn btn btn-primary me-2">
                                    <i class="fas fa-filter"></i> Lọc
                                </button>
                                <a href="{{ route('admin.faqs.index') }}"
                                    class="filter-clear-btn btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Xóa bộ lọc
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('question') || request('order') || request('date_from') || request('date_to'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('question'))
                            <span class="filter-tag">
                                <span>Câu hỏi: {{ request('question') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('question')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('order'))
                            <span class="filter-tag">
                                <span>Thứ tự: {{ request('order') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('order')) }}"
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

                @if ($faqs->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        @if (request('question') || request('order') || request('date_from') || request('date_to'))
                            <h4>Không tìm thấy câu hỏi nào</h4>
                            <p>Không có câu hỏi nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.faqs.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có câu hỏi nào</h4>
                            <p>Bắt đầu bằng cách thêm câu hỏi thường gặp đầu tiên.</p>
                            <a href="{{ route('admin.faqs.create') }}" class="action-button">
                                <i class="fas fa-plus"></i> Thêm câu hỏi mới
                            </a>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small text-center">Thứ tự</th>
                                    <th class="column-large">Câu hỏi</th>
                                    <th class="column-large">Câu trả lời</th>
                                    <th class="column-medium">Ngày tạo</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($faqs as $index => $faq)
                                    <tr>
                                        <td class="text-center">{{ $faq->order }}</td>
                                        <td class="faq-question truncate-text" title="{{ $faq->question }}">
                                            {{ Str::limit($faq->question, 80) }}
                                        </td>
                                        <td class="faq-answer truncate-text" title="{{ strip_tags($faq->answer) }}">
                                            {{ Str::limit(strip_tags($faq->answer), 80) }}
                                        </td>
                                        <td>{{ $faq->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.faqs.edit', $faq) }}"
                                                    class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @include('components.delete-form', [
                                                    'id' => $faq->id,
                                                    'route' => route('admin.faqs.destroy', $faq),
                                                    'message' => 'Bạn có chắc chắn muốn xóa câu hỏi này?',
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
                            Hiển thị {{ $faqs->firstItem() ?? 0 }} đến {{ $faqs->lastItem() ?? 0 }} của
                            {{ $faqs->total() }} câu hỏi
                        </div>
                        <div class="pagination-controls">
                            {{ $faqs->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* FAQ specific styles */
        .faq-question {
            font-weight: 500;
            color: #333;
        }

        .faq-answer {
            color: #555;
        }

        .truncate-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            display: inline-block;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Khi thay đổi bộ lọc, tự động submit form
        document.getElementById('question_filter').addEventListener('change', function() {
            if (this.value.length >= 3 || this.value.length === 0) {
                document.querySelector('.filter-form').submit();
            }
        });

        document.getElementById('order_filter').addEventListener('change', function() {
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
