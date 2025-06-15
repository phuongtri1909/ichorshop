<div class="filter-group">
    <h6 class="filter-title">
        <span>Dress Style</span>
        <i class="fas fa-chevron-up toggle-icon"></i>
    </h6>
    <div class="filter-content">
        <ul class="filter-style-list row">
            @foreach($dressStyles as $style)
                <li class="filter-style-item col-6">
                    <div class="custom-checkbox">
                        <input type="checkbox" id="style-{{ $style->id }}" name="styles[]" value="{{ $style->id }}">
                        <label for="style-{{ $style->id }}">
                            <span class="checkbox-indicator"></span>
                            <span class="checkbox-text">{{ $style->name }}</span>
                        </label>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>

@push('styles')
<style>
    .filter-style-list {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    .filter-style-item {
        margin-bottom: 5px;
    }

    /* Custom Checkbox Styling */
    .custom-checkbox {
        position: relative;
        display: flex;
        align-items: center;
    }

    .custom-checkbox input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    .custom-checkbox label {
        display: flex;
        align-items: center;
        margin: 0;
        cursor: pointer;
        color: var(--primary-color-5);
        font-size: 14px;
        transition: color 0.2s ease;
        width: 100%;
    }

    .checkbox-indicator {
        position: relative;
        display: inline-block;
        width: 18px;
        height: 18px;
        margin-right: 10px;
        border: 1.5px solid #d8d8d8;
        border-radius: 3px;
        background-color: #fff;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .checkbox-indicator:after {
        content: '';
        position: absolute;
        display: none;
        left: 6px;
        top: 2px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    .custom-checkbox input[type="checkbox"]:checked ~ label .checkbox-indicator {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .custom-checkbox input[type="checkbox"]:checked ~ label .checkbox-indicator:after {
        display: block;
    }

    .custom-checkbox input[type="checkbox"]:checked ~ label {
        color: var(--primary-color);
        font-weight: 500;
    }

    .custom-checkbox:hover .checkbox-indicator {
        border-color: var(--primary-color);
    }

    .checkbox-text {
        flex-grow: 1;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Lấy tất cả các checkbox style
    const styleCheckboxes = document.querySelectorAll('input[name="styles[]"]');
    
    // Khởi tạo mảng lưu các style đã chọn
    let selectedStyles = [];
    
    styleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Cập nhật mảng các style đã chọn
            if (this.checked) {
                // Thêm style vào danh sách nếu chưa có
                if (!selectedStyles.includes(this.value)) {
                    selectedStyles.push(this.value);
                }
            } else {
                // Xóa style khỏi danh sách nếu đã bỏ chọn
                selectedStyles = selectedStyles.filter(style => style !== this.value);
            }
            
            console.log('Selected styles:', selectedStyles);
            
            // Sự kiện tùy chỉnh thông báo thay đổi bộ lọc
            document.dispatchEvent(new CustomEvent('filter:changed', { 
                detail: { 
                    type: 'style',
                    values: selectedStyles
                } 
            }));
        });
    });
});
</script>
@endpush