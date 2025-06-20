@extends('admin.layouts.sidebar')

@section('title', 'Gửi mã giảm giá cho người dùng')

@section('main-content')
    <div class="send-coupon-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Mã giảm giá</a></li>
                <li class="breadcrumb-item current">Gửi mã giảm giá</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-paper-plane icon-title"></i>
                    <h5>Gửi mã giảm giá cho người dùng</h5>
                </div>
            </div>
            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <div class="coupon-info card mb-4">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-3">Thông tin mã giảm giá</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th>Mã giảm giá:</th>
                                        <td><span class="badge bg-light text-dark">{{ $coupon->code }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Giá trị:</th>
                                        <td>{{ $coupon->display_value }}</td>
                                    </tr>
                                    <tr>
                                        <th>Hiệu lực:</th>
                                        <td>
                                            @if ($coupon->start_date && $coupon->end_date)
                                                {{ $coupon->start_date->format('d/m/Y') }} -
                                                {{ $coupon->end_date->format('d/m/Y') }}
                                            @elseif($coupon->start_date)
                                                Từ {{ $coupon->start_date->format('d/m/Y') }}
                                            @elseif($coupon->end_date)
                                                Đến {{ $coupon->end_date->format('d/m/Y') }}
                                            @else
                                                Không giới hạn
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th>Giới hạn sử dụng:</th>
                                        <td>
                                            @if ($coupon->usage_limit)
                                                {{ $coupon->usage_count }}/{{ $coupon->usage_limit }}
                                            @else
                                                Không giới hạn
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Đơn hàng tối thiểu:</th>
                                        <td>
                                            @if ($coupon->min_order_amount > 0)
                                                ${{ number_format($coupon->min_order_amount, 2) }}
                                            @else
                                                Không giới hạn
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Trạng thái:</th>
                                        <td>
                                            @if ($coupon->isValid())
                                                <span class="badge bg-success">Hoạt động</span>
                                            @else
                                                <span class="badge bg-danger">Không hoạt động</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.coupons.send', $coupon) }}" method="POST" id="send-coupon-form">
                    @csrf

                    <div class="form-section">
                        <h6 class="section-title">Chọn người dùng để gửi</h6>
                        <div class="user-selection">
                            <div class="form-group">
                                <label class="form-label">Chọn người dùng</label>

                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" id="user-search"
                                        placeholder="Tìm kiếm người dùng theo tên hoặc email">
                                    <button class="btn btn-outline-secondary" type="button" id="search-users-btn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>

                                <div class="alert alert-info mb-3 {{ empty($selectedUserIds ?? []) ? 'd-none' : '' }}">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Đã chọn <span id="selected-users-count">{{ count($selectedUserIds ?? []) }}</span>
                                    người dùng.
                                </div>

                                <div class="users-container" id="users-container">
                                    <div class="text-center py-4" id="users-loading">
                                        <div class="spinner-border text-dark" role="status">
                                            <span class="visually-hidden">Đang tải...</span>
                                        </div>
                                        <div class="mt-2">Đang tải người dùng...</div>
                                    </div>
                                    <div id="users-list" class="row"></div>
                                    <div class="text-center py-3 d-none" id="load-more-users">
                                        <button class="btn btn-pry btn-sm" type="button">
                                            <i class="fas fa-sync-alt me-1"></i> Tải thêm người dùng
                                        </button>
                                    </div>
                                    <div class="text-center py-3 d-none" id="no-users-found">
                                        <div class="text-muted">
                                            <i class="fas fa-users me-2"></i>Không tìm thấy người dùng nào
                                        </div>
                                    </div>
                                </div>
                                <small class="form-text">Không chọn nếu áp dụng cho tất cả người dùng</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="assign" name="assign" value="1" checked>
                            <label class="form-check-label" for="assign">
                                Gán mã giảm giá cho người dùng được chọn
                            </label>
                            <small class="form-text text-muted d-block">
                                Nếu chọn tùy chọn này, người dùng sẽ được phép sử dụng mã giảm giá này ngay cả khi không nằm trong danh sách người dùng đã được gán trước đó.
                            </small>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="email-preview card p-3 border">
                            <h6 class="card-title">Xem trước email</h6>
                            <div class="email-preview-content">
                                <div class="email-preview-heading">Xin chào {Tên người dùng}!</div>
                                <p>Chúng tôi muốn gửi tặng bạn một mã giảm giá đặc biệt!</p>
                                <div class="email-preview-coupon">Mã giảm giá: {{ $coupon->code }}</div>
                                <div>Giảm giá: {{ $coupon->display_value }}</div>
                                @if($coupon->min_order_amount > 0)
                                    <div>Đơn hàng tối thiểu: ${{ number_format($coupon->min_order_amount, 2) }}</div>
                                @endif
                                <div>Hiệu lực đến: {{ $coupon->end_date ? $coupon->end_date->format('d/m/Y') : 'Không giới hạn' }}</div>
                                @if($coupon->description)
                                    <div>Ghi chú: {{ $coupon->description }}</div>
                                @endif
                            </div>
                            <div class="email-preview-button">
                                <span class="btn btn-sm btn-primary disabled">Mua sắm ngay</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.coupons.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" id="send-button" class="save-button">
                            <i class="fas fa-paper-plane"></i> Gửi mã giảm giá
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Form Layout */
        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }

        .form-section:last-child {
            border-bottom: none;
        }

        .section-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            color: #495057;
            position: relative;
            padding-left: 15px;
        }

        .section-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 5px;
            height: 18px;
            background-color: var(--primary-color, #D1A66E);
            border-radius: 3px;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
        }

        .required-asterisk {
            color: #dc3545;
            margin-left: 2px;
        }

        .form-text {
            color: #6c757d;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* Buttons */
        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e9ecef;
        }

        .back-button {
            padding: 0.5rem 1rem;
            background-color: #f8f9fa;
            color: #495057;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s;
        }

        .back-button:hover {
            background-color: #e9ecef;
            color: #212529;
        }

        .save-button {
            padding: 0.5rem 1.5rem;
            background-color: var(--primary-color, #D1A66E);
            color: #fff;
            border: none;
            border-radius: 0.25rem;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s;
        }

        .save-button:hover {
            opacity: 0.9;
        }

        .save-button i,
        .back-button i {
            margin-right: 0.5rem;
        }

        /* User Selection */
        .users-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            position: relative;
        }

        .users-container::-webkit-scrollbar {
            width: 6px;
        }

        .users-container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .users-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .users-container::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        .user-item {
            background-color: #fff;
            border: 1px solid #eee;
            border-radius: 4px;
            padding: 8px 12px;
            transition: background-color 0.2s;
        }

        .user-item:hover {
            background-color: #f8f9fa;
        }

        .user-name {
            font-weight: 500;
        }

        .user-email {
            font-size: 0.85rem;
            color: #6c757d;
        }

        /* Email Preview */
        .email-preview {
            background-color: #fafafa;
        }

        .email-preview-heading {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .email-preview-coupon {
            font-size: 1.1rem;
            font-weight: 700;
            color: #D1A66E;
            margin: 10px 0;
        }

        .email-preview-button {
            margin-top: 15px;
        }

        /* Loading and empty states */
        .users-loading {
            background-color: rgba(255, 255, 255, 0.8);
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .loading-text {
            margin-top: 10px;
            color: #666;
        }

        .no-users-message {
            padding: 20px;
            text-align: center;
            color: #666;
        }

        /* Selected item badges */
        .badge.bg-primary {
            background-color: var(--primary-color, #D1A66E) !important;
        }

        .alert-info {
            background-color: #e8f4f8;
            border-color: #d6e9f0;
            color: #0c5460;
        }

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .form-actions {
                flex-direction: column;
                gap: 1rem;
            }

            .back-button,
            .save-button {
                width: 100%;
                justify-content: center;
            }

            .users-container {
                max-height: 300px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // ===== VARIABLES =====
            let userPage = 1;
            let hasMoreUsers = true;
            let isUserLoading = false;
            let userSearchTerm = '';
            let selectedUserIds = @json($selectedUserIds ?? []);
            let loadedUserIds = [];

            // ===== INITIALIZATION =====
            initializeUsersSection();

            // ===== EVENT HANDLERS =====
            // User search handlers
            $('#search-users-btn').on('click', function() {
                searchUsers();
            });

            $('#user-search').on('keypress', function(e) {
                if (e.which === 13) {
                    searchUsers();
                    e.preventDefault();
                }
            });

            // Infinite scroll for users
            $('#users-container').on('scroll', function() {
                handleUserScroll($(this));
            });

            // Load more users button handler
            $('#load-more-users button').on('click', function() {
                loadMoreUsers();
            });

            // Form submission
            $('#send-coupon-form').on('submit', function(e) {
                e.preventDefault();
                submitForm();
            });

            // ===== FUNCTIONS =====
            // --- USERS SECTION ---
            function initializeUsersSection() {
                loadInitialUsers();
            }

            function loadInitialUsers() {
                isUserLoading = true;
                $('#no-users-found').addClass('d-none');
                showUserLoading();

                $.ajax({
                    url: '{{ route("admin.coupons.initial-users") }}',
                    type: 'GET',
                    data: {
                        selected_user_ids: selectedUserIds
                    },
                    success: function(response) {
                        hideUserLoading();
                        isUserLoading = false;

                        if (response.total === 0) {
                            showNoUsersFound();
                            return;
                        }

                        $('#users-list').html(response.html);

                        // Lưu lại ID của các user đã load
                        response.users.forEach(user => {
                            if (!loadedUserIds.includes(user.id)) {
                                loadedUserIds.push(user.id);
                            }
                        });

                        // Hiển thị nút load more nếu còn user chưa load
                        if (loadedUserIds.length < response.total) {
                            $('#load-more-users').removeClass('d-none');
                            hasMoreUsers = true;
                        } else {
                            $('#load-more-users').addClass('d-none');
                            hasMoreUsers = false;
                        }

                        // Khởi tạo sự kiện cho user checkboxes
                        initializeUserCheckboxes();

                        // Đánh dấu các user đã chọn
                        markSelectedUsers();
                    },
                    error: function(xhr) {
                        hideUserLoading();
                        isUserLoading = false;
                        showToast('Không thể tải danh sách người dùng. Vui lòng thử lại.', 'error');
                    }
                });
            }

            function searchUsers() {
                userSearchTerm = $('#user-search').val();
                userPage = 1;
                $('#users-list').empty();
                loadedUserIds = []; // Reset danh sách user đã tải
                showUserLoading();
                loadUsers();
            }

            function loadMoreUsers() {
                userPage++;
                loadUsers();
            }

            function handleUserScroll(container) {
                if (hasMoreUsers && !isUserLoading &&
                    container.scrollTop() + container.innerHeight() >= container[0].scrollHeight - 100) {
                    loadMoreUsers();
                }
            }

            function loadUsers() {
                if (isUserLoading) return;

                isUserLoading = true;
                $('#no-users-found').addClass('d-none');
                showUserLoading();

                $.ajax({
                    url: '{{ route("admin.coupons.load-users") }}',
                    data: {
                        page: userPage,
                        search: userSearchTerm,
                        format: 'html',
                        selected_user_ids: selectedUserIds,
                        exclude_ids: loadedUserIds
                    },
                    type: 'GET',
                    success: function(response) {
                        hideUserLoading();
                        isUserLoading = false;

                        if (userPage === 1 && response.total === 0) {
                            showNoUsersFound();
                            return;
                        }

                        $('#users-list').append(response.html);

                        // Cập nhật danh sách ID đã tải
                        $(response.html).find('.user-checkbox').each(function() {
                            const userId = parseInt($(this).val());
                            if (!loadedUserIds.includes(userId)) {
                                loadedUserIds.push(userId);
                            }
                        });

                        if (response.hasMore) {
                            $('#load-more-users').removeClass('d-none');
                        } else {
                            $('#load-more-users').addClass('d-none');
                        }

                        hasMoreUsers = response.hasMore;
                        initializeUserCheckboxes();

                        // Đánh dấu các user đã chọn
                        markSelectedUsers();
                    },
                    error: function(xhr) {
                        hideUserLoading();
                        isUserLoading = false;
                        showToast('Không thể tải người dùng. Vui lòng thử lại.', 'error');
                    }
                });
            }

            function showUserLoading() {
                $('#users-loading').removeClass('d-none');
            }

            function hideUserLoading() {
                $('#users-loading').addClass('d-none');
            }

            function showNoUsersFound() {
                $('#no-users-found').removeClass('d-none');
                $('#load-more-users').addClass('d-none');
            }

            function markSelectedUsers() {
                if (selectedUserIds.length > 0) {
                    selectedUserIds.forEach(id => {
                        const userCheckbox = $(`#user_${id}`);
                        if (userCheckbox.length) {
                            userCheckbox.prop('checked', true);
                        }
                    });
                }
            }

            function initializeUserCheckboxes() {
                $('.user-checkbox:not(.initialized)').each(function() {
                    $(this).addClass('initialized');
                    $(this).change(function() {
                        const userId = parseInt($(this).val());
                        const isChecked = $(this).prop('checked');

                        if (isChecked) {
                            if (!selectedUserIds.includes(userId)) {
                                selectedUserIds.push(userId);
                            }
                            updateSelectedUserCount();
                        } else {
                            selectedUserIds = selectedUserIds.filter(id => id !== userId);
                            updateSelectedUserCount();
                        }
                    });
                });
            }

            function updateSelectedUserCount() {
                const count = selectedUserIds.length;
                if (count > 0) {
                    $('#selected-users-count').text(count).parent().removeClass('d-none');
                    $('#send-button').prop('disabled', false);
                } else {
                    $('#selected-users-count').parent().addClass('d-none');
                    $('#send-button').prop('disabled', true);
                }
            }

            // --- FORM SUBMISSION ---
            function submitForm() {
                if (selectedUserIds.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Không có người dùng nào được chọn',
                        text: 'Vui lòng chọn ít nhất một người dùng để gửi mã giảm giá.'
                    });
                    return;
                }

                // Get assigned users
                const assignedUserIds = @json($coupon->users->pluck('id'));
                const alreadyAssignedCount = selectedUserIds.filter(id => assignedUserIds.includes(id)).length;

                let confirmMessage = `Bạn đang gửi mã giảm giá cho ${selectedUserIds.length} người dùng.`;
                if (alreadyAssignedCount > 0) {
                    confirmMessage += `\n${alreadyAssignedCount} người dùng đã được gán trước đó và sẽ không nhận được email mới.`;
                }

                Swal.fire({
                    title: 'Xác nhận gửi mã giảm giá',
                    text: confirmMessage,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Gửi ngay',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Clear any previous inputs
                        $('#send-coupon-form').find('input[name="users[]"]').remove();

                        // Add selected users to form
                        selectedUserIds.forEach(userId => {
                            $('#send-coupon-form').append(`<input type="hidden" name="users[]" value="${userId}">`);
                        });

                        // Show loading
                        const submitBtn = $('.save-button');
                        const originalBtnText = submitBtn.html();
                        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Đang gửi...');
                        submitBtn.prop('disabled', true);

                        // Submit form
                        document.getElementById('send-coupon-form').submit();
                    }
                });
            }
            
        });
    </script>
@endpush