@extends('admin.layouts.sidebar')

@section('title', 'Quản lý mạng xã hội')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Mạng xã hội</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-share-alt icon-title"></i>
                    <h5>Danh sách mạng xã hội</h5>
                </div>
                <a href="{{ route('admin.socials.create') }}" class="action-button">
                    <i class="fas fa-plus"></i> Thêm mạng xã hội
                </a>
            </div>
            <div class="card-content">

                @if ($socials->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-share-alt"></i>
                        </div>
                        <h4>Chưa có mạng xã hội nào</h4>
                        <p>Thêm mạng xã hội để hiển thị trên website của bạn</p>
                        <a href="{{ route('admin.socials.create') }}" class="action-button">
                            <i class="fas fa-plus"></i> Thêm mạng xã hội
                        </a>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">ID</th>
                                    <th class="column-small">Icon</th>
                                    <th class="column-medium">Tên</th>
                                    <th class="column-large">Đường dẫn</th>
                                    <th class="column-small">Key</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($socials as $social)
                                    <tr>
                                        <td>{{ $social->id }}</td>
                                        <td class="text-center">
                                            @if ($social->icon)
                                                <div class="social-icon-preview">
                                                    <img src="{{ asset($social->icon) }}" alt="{{ $social->name }}"
                                                        class="social-svg-icon">
                                                </div>
                                            @else
                                                <div class="social-icon-missing">
                                                    <i class="fas fa-question"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="item-name">{{ $social->name }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ $social->link }}" target="_blank" class="social-link">
                                                {{ $social->link }}
                                                <i class="fas fa-external-link-alt link-icon"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="item-slug">{{ $social->key }}</span>
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.socials.edit', $social->id) }}"
                                                    class="action-icon edit-icon" title="Chỉnh sửa">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                @include('admin.components.delete-form', [
                                                    'id' => $social->id,
                                                    'route' => route('admin.socials.destroy', $social),
                                                    'message' => "Bạn có chắc chắn muốn xóa social '{$social->name}'?",
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
        .social-icon-preview {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 8px;
        }

        .social-svg-icon {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .social-icon-missing {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            background-color: #dc3545;
            border-radius: 4px;
        }

        .social-link {
            color: var(--primary-color);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .social-link:hover {
            text-decoration: underline;
        }

        .link-icon {
            font-size: 0.8rem;
            margin-left: 5px;
        }

        .delete-form {
            display: inline;
        }
    </style>
@endpush
