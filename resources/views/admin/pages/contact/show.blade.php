@extends('admin.layouts.sidebar')

@section('title', 'Chi Tiết Liên Hệ')

@section('main-content')
<div class="category-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.contacts.index') }}">Liên hệ</a></li>
            <li class="breadcrumb-item current">Chi tiết liên hệ</li>
        </ol>
    </div>

    <div class="contact-details-container">
        <!-- Thông tin liên hệ -->
        <div class="content-card">
            <div class="card-top contact-info-header">
                <div class="contact-card-title">
                    <h5>
                        <i class="fas fa-phone-alt"></i>
                        Liên hệ #{{ $contact->id }}
                    </h5>
                </div>
                <div class="contact-meta">
                    <span class="contact-date">
                        <i class="far fa-calendar-alt"></i>
                        {{ $contact->created_at->format('d/m/Y H:i') }}
                    </span>
                    <span class="status-badge status-{{ $contact->status }}">
                        @switch($contact->status)
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
                                        <div class="info-value">{{ $contact->full_name }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Số điện thoại:</div>
                                        <div class="info-value">
                                            <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-section">
                                <h6 class="section-title">Ghi chú</h6>
                                <div class="note-content">
                                    {{ $contact->note ?? 'Không có ghi chú' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cập nhật trạng thái -->
                <div class="status-update-section">
                    <h6 class="section-title">Cập nhật trạng thái</h6>
                    <form action="{{ route('admin.contacts.update-status', $contact) }}" method="POST" class="status-update-form">
                        @csrf
                        @method('PATCH')
                        <div class="form-group mb-0">
                            <select name="status" class="form-control status-select">
                                <option value="pending" {{ $contact->status == 'pending' ? 'selected' : '' }}>Chờ liên hệ</option>
                                <option value="contacted" {{ $contact->status == 'contacted' ? 'selected' : '' }}>Đã liên hệ</option>
                                <option value="cancelled" {{ $contact->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-update">Cập nhật trạng thái</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Nút quay lại -->
        <div class="actions-container">
            <a href="{{ route('admin.contacts.index') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
            
            <!-- Nút xóa -->
            <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" class="delete-form" 
                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa liên hệ này?')">
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