@extends('admin.layouts.sidebar')

@section('title', 'Thêm gói nhượng quyền')

@section('main-content')
    <div class="category-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.franchise.index') }}">Gói nhượng quyền</a></li>
                <li class="breadcrumb-item current">Thêm mới</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-plus icon-title"></i>
                    <h5>Thêm gói nhượng quyền mới</h5>
                </div>
            </div>

            <div class="form-body">

                <form action="{{ route('admin.franchise.store') }}" method="POST" class="franchise-form" id="franchise-form">
                    @csrf

                    <div class="form-tabs">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label-custom">
                                        Tên gói <span class="required-mark">*</span>
                                    </label>
                                    <input type="text" class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}"
                                        id="name" name="name" value="{{ old('name') }}">
                                    <div class="error-message">
                                        @error('name')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name_package" class="form-label-custom">
                                        Tên hiển thị <span class="required-mark">*</span>
                                    </label>
                                    <input type="text" class="custom-input {{ $errors->has('name_package') ? 'input-error' : '' }}"
                                        id="name_package" name="name_package" value="{{ old('name_package') }}">
                                    <div class="error-message">
                                        @error('name_package')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code" class="form-label-custom">
                                        Mã gói <span class="required-mark">*</span>
                                    </label>
                                    <input type="text" class="custom-input {{ $errors->has('code') ? 'input-error' : '' }}"
                                        id="code" name="code" value="{{ old('code') }}" 
                                        placeholder="Nhập mã gói hoặc để trống để tự động tạo">
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Để trống để tự động tạo từ tên hiển thị</span>
                                    </div>
                                    <div class="error-message">
                                        @error('code')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sort_order" class="form-label-custom">
                                        Thứ tự hiển thị
                                    </label>
                                    <input type="number" class="custom-input {{ $errors->has('sort_order') ? 'input-error' : '' }}"
                                        id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                                    <div class="error-message">
                                        @error('sort_order')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label-custom">
                                Mô tả chi tiết <span class="required-mark">*</span>
                            </label>
                            <textarea id="description" name="description" class="editor">{{ old('description') }}</textarea>
                            <div class="error-message">
                                @error('description')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label-custom">
                                Đặc điểm gói nhượng quyền
                            </label>
                            <div class="details-container">
                                <div id="details-items-container">
                                    @if(old('detail_items'))
                                        @foreach(old('detail_items') as $index => $item)
                                            <div class="detail-item row mb-3">
                                                <div class="col-md-10">
                                                    <textarea class="detail-editor" id="detail_items_{{ $index }}" name="detail_items[{{ $index }}]">{{ $item }}</textarea>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-danger btn-sm remove-detail-btn">
                                                        <i class="fas fa-trash"></i> Xóa
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="detail-item row mb-3">
                                            <div class="col-md-10">
                                                <textarea class="detail-editor" id="detail_items_0" name="detail_items[0]"></textarea>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger btn-sm remove-detail-btn">
                                                    <i class="fas fa-trash"></i> Xóa
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-primary btn-sm mt-2" id="add-detail-btn">
                                    <i class="fas fa-plus"></i> Thêm đặc điểm
                                </button>
                            </div>
                            <div class="error-message">
                                @error('detail_items')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.franchise.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Lưu gói nhượng quyền
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Khởi tạo CKEditor cho mô tả chi tiết
            CKEDITOR.replace('description', {
                on: {
                    change: function(evt) {
                        this.updateElement();
                    }
                },
                height: 200,
                // Enable image resizing
                extraPlugins: 'image2,uploadimage',
                // Don't remove image2 plugin as it's needed for resizing
                removePlugins: 'uploadfile,filebrowser',
                image2_alignClasses: ['image-left', 'image-center', 'image-right'],
                image2_disableResizer: false,
            });
            
            // Khởi tạo CKEditor cho các đặc điểm hiện tại
            initDetailEditors();
            
            // Hàm khởi tạo CKEditor cho các trường đặc điểm
            function initDetailEditors() {
                $('.detail-editor').each(function() {
                    if (!$(this).hasClass('cke_initialized')) {
                        const editorId = $(this).attr('id');
                        CKEDITOR.replace(editorId, {
                            on: {
                                change: function(evt) {
                                    this.updateElement();
                                }
                            },
                            height: 80,
                            toolbar: [
                                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline'] },
                                { name: 'colors', items: ['TextColor', 'BGColor'] }
                            ],
                            removePlugins: 'elementspath',
                            resize_enabled: false
                        });
                        $(this).addClass('cke_initialized');
                    }
                });
            }

            // Xử lý thêm chi tiết đặc điểm
            let detailIndex = $('#details-items-container .detail-item').length;

            $('#add-detail-btn').click(function() {
                const newIndex = detailIndex;
                const newRow = `
                <div class="detail-item row mb-3">
                    <div class="col-md-10">
                        <textarea class="detail-editor" id="detail_items_${newIndex}" name="detail_items[${newIndex}]"></textarea>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm remove-detail-btn">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                    </div>
                </div>
                `;
                $('#details-items-container').append(newRow);
                
                // Khởi tạo CKEditor cho trường đặc điểm mới
                CKEDITOR.replace(`detail_items_${newIndex}`, {
                    on: {
                        change: function(evt) {
                            this.updateElement();
                        }
                    },
                    height: 80,
                    toolbar: [
                        { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline'] },
                        { name: 'colors', items: ['TextColor', 'BGColor'] }
                    ],
                    removePlugins: 'elementspath',
                    resize_enabled: false
                });
                
                detailIndex++;
            });

            // Xóa dòng chi tiết
            $(document).on('click', '.remove-detail-btn', function() {
                if ($('#details-items-container .detail-item').length > 1) {
                    // Lấy ID của editor trước khi xóa
                    const editorElement = $(this).closest('.detail-item').find('.detail-editor');
                    const editorId = editorElement.attr('id');
                    
                    // Hủy instance CKEditor nếu đã khởi tạo
                    if (CKEDITOR.instances[editorId]) {
                        CKEDITOR.instances[editorId].destroy();
                    }
                    
                    // Xóa phần tử
                    $(this).closest('.detail-item').remove();
                } else {
                    alert('Phải có ít nhất một đặc điểm!');
                }
            });

            // Xử lý gửi form
            $('#franchise-form').submit(function() {
                for (instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }
            });
        });
    </script>
@endpush