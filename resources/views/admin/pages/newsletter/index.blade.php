@extends('admin.layouts.sidebar')

@section('title', 'Danh sách đăng ký bản tin')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Danh sách đăng ký bản tin</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-envelope-open-text icon-title"></i>
                    <h5>Danh sách đăng ký bản tin</h5>
                </div>
                <form action="{{ route('admin.newsletter.export') }}" method="POST">
                    @csrf
                    <button type="submit" class="action-button">
                        <i class="fas fa-file-export"></i> Xuất CSV
                    </button>
                </form>
            </div>
            <div class="card-content">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($subscriptions->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-envelope-open-text"></i>
                        </div>
                        <h4>Chưa có email đăng ký nhận bản tin</h4>
                        <p>Khi khách hàng đăng ký nhận bản tin, thông tin sẽ hiển thị ở đây</p>
                    </div>
                @else
                    <div class="filter-controls mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm theo email...">
                                    <button class="btn btn-pry" type="button" id="searchBtn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">ID</th>
                                    <th class="column-large">Email</th>
                                    <th class="column-medium">Ngày đăng ký</th>
                                    <th class="column-small">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subscriptions as $subscription)
                                    <tr>
                                        <td>{{ $subscription->id }}</td>
                                        <td>{{ $subscription->email }}</td>
                                        <td>{{ $subscription->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                @include('components.delete-form', [
                                                    'id' => $subscription->id,
                                                    'route' => route('admin.newsletter.destroy', $subscription),
                                                    'message' => "Bạn có chắc chắn muốn xóa email '{$subscription->email}' khỏi danh sách đăng ký?",
                                                ])
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-container mt-4">
                        {{ $subscriptions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
      
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Xử lý tìm kiếm
            $('#searchBtn').click(function() {
                const searchValue = $('#searchInput').val().trim();
                if (searchValue) {
                    window.location.href = '{{ route("admin.newsletter.index") }}?search=' + encodeURIComponent(searchValue);
                }
            });

            // Cho phép nhấn Enter để tìm kiếm
            $('#searchInput').keypress(function(e) {
                if (e.which === 13) {
                    $('#searchBtn').click();
                }
            });

            // Điền giá trị tìm kiếm từ URL vào ô tìm kiếm
            const urlParams = new URLSearchParams(window.location.search);
            const searchParam = urlParams.get('search');
            if (searchParam) {
                $('#searchInput').val(searchParam);
            }
        });
    </script>
@endpush