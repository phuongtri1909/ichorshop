@extends('admin.layouts.sidebar')

@section('title', 'Chi Tiết Liên Hệ Nhượng Quyền')

@section('main-content')
<div class="category-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.franchise-contacts.index') }}">Liên hệ nhượng quyền</a></li>
            <li class="breadcrumb-item current">Chi tiết liên hệ</li>
        </ol>
    </div>

    <div class="contact-details-container">
        <!-- Thông tin liên hệ -->
        <div class="content-card">
            <div class="card-top contact-info-header">
                <div class="contact-card-title">
                    <h5>
                        <i class="fas fa-handshake"></i>
                        Liên hệ nhượng quyền #{{ $franchiseContact->id }}
                    </h5>
                </div>
                <div class="contact-meta">
                    <span class="contact-date">
                        <i class="far fa-calendar-alt"></i>
                        {{ $franchiseContact->created_at->format('d/m/Y H:i') }}
                    </span>
                    <span class="status-badge status-{{ $franchiseContact->status }}">
                        @switch($franchiseContact->status)
                            @case('pending')
                                <i class="fas fa-clock"></i> Chờ liên hệ
                                @break
                            @case('contacted')
                                <i class="fas fa-phone-alt"></i> Đã liên hệ
                                @break
                            @case('cancelled')
                                <i class="fas fa-times-circle"></i> Đã hủy
                                @break
                            @default
                                <i class="fas fa-question-circle"></i> Không xác định
                        @endswitch
                    </span>
                </div>
            </div>

            <div class="card-content">
                <div class="contact-details">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-section">
                                <h6 class="section-title">Thông tin người liên hệ</h6>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <div class="info-label">Họ và tên:</div>
                                        <div class="info-value">{{ $franchiseContact->first_name }} {{ $franchiseContact->last_name }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Số điện thoại:</div>
                                        <div class="info-value">
                                            <a href="tel:{{ $franchiseContact->phone }}">{{ $franchiseContact->phone }}</a>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Email:</div>
                                        <div class="info-value">
                                            <a href="mailto:{{ $franchiseContact->email }}">{{ $franchiseContact->email }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="info-section">
                                <h6 class="section-title">Địa chỉ</h6>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <div class="info-label">Địa chỉ cụ thể:</div>
                                        <div class="info-value">{{ $franchiseContact->address }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Phường/Xã:</div>
                                        <div class="info-value">{{ $franchiseContact->wards_name }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Quận/Huyện:</div>
                                        <div class="info-value">{{ $franchiseContact->districts_name }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Tỉnh/Thành phố:</div>
                                        <div class="info-value">{{ $franchiseContact->provinces_name }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-section">
                                <h6 class="section-title">Thông tin gói nhượng quyền</h6>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <div class="info-label">Gói nhượng quyền:</div>
                                        <div class="info-value">
                                            @if($franchiseContact->franchise)
                                                <span class="franchise-package">{{ $franchiseContact->franchise->name }}</span>
                                                <span class="franchise-code">({{ $franchiseContact->franchise->code }})</span>
                                                <a href="{{ route('admin.franchise.edit', $franchiseContact->franchise->id) }}" class="edit-link" title="Chỉnh sửa gói nhượng quyền">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                            @else
                                                <span class="text-danger">Gói không tồn tại</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="info-section">
                                <h6 class="section-title">Ghi chú</h6>
                                <div class="note-content">
                                    {{ $franchiseContact->note ?? 'Không có ghi chú' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cập nhật trạng thái -->
                <div class="status-update-section">
                    <h6 class="section-title">Cập nhật trạng thái</h6>
                    <form action="{{ route('admin.franchise-contacts.update-status', $franchiseContact) }}" method="POST" class="status-update-form">
                        @csrf
                        @method('PATCH')
                        <div class="form-group mb-0">
                            <select name="status" class="form-control status-select">
                                <option value="pending" {{ $franchiseContact->status == 'pending' ? 'selected' : '' }}>Chờ liên hệ</option>
                                <option value="contacted" {{ $franchiseContact->status == 'contacted' ? 'selected' : '' }}>Đã liên hệ</option>
                                <option value="cancelled" {{ $franchiseContact->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-update">Cập nhật trạng thái</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Nút quay lại -->
        <div class="actions-container">
            <a href="{{ route('admin.franchise-contacts.index') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
            
            <!-- Nút xóa -->
            <form action="{{ route('admin.franchise-contacts.destroy', $franchiseContact) }}" method="POST" class="delete-form" 
                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa liên hệ nhượng quyền này?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="delete-btn">
                    <i class="fas fa-trash"></i> Xóa liên hệ
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
