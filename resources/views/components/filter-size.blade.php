<div class="filter-group">
    <h6 class="filter-title">
        <span>Size</span>
        <i class="fas fa-chevron-up toggle-icon"></i>
    </h6>
    <div class="filter-content">
        <div class="size-options">
            @foreach($sizes as $size)
                <div class="size-option" data-size="{{ $size['value'] }}">
                    {{ $size['name'] ? $size['name'] : 'Default' }}
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
<style>
    .size-options {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 8px;
    }

    .size-option {
        padding: 12px 8px;
        border: 1px solid #e5e5e5;
        border-radius: 62px;
        background: var(--primary-color-2);
        color: var(--primary-color-5);
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
        text-align: center;
        font-weight: 400;
    }

    .size-option:hover {
        border-color: var(--primary-color);
    }
    
    .size-option.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    @media (max-width: 576px) {
        .size-options {
            grid-template-columns: repeat(7, 1fr);
        }
        
        .size-option {
            padding: 10px 6px;
            font-size: 12px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sizeOptions = document.querySelectorAll('.size-option');
    
    // Khởi tạo mảng lưu các kích thước đã chọn
    let selectedSizes = [];
    
    sizeOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Toggle active class cho phần tử được click
            this.classList.toggle('active');
            
            // Lấy giá trị size
            const sizeValue = this.getAttribute('data-size');
            
            // Cập nhật mảng các kích thước đã chọn
            if (this.classList.contains('active')) {
                // Thêm size vào danh sách nếu chưa có
                if (!selectedSizes.includes(sizeValue)) {
                    selectedSizes.push(sizeValue);
                }
            } else {
                // Xóa size khỏi danh sách nếu đã bỏ chọn
                selectedSizes = selectedSizes.filter(size => size !== sizeValue);
            }
            
            // Sự kiện tùy chỉnh thông báo thay đổi bộ lọc
            // Các component khác có thể lắng nghe sự kiện này
            document.dispatchEvent(new CustomEvent('filter:changed', { 
                detail: { 
                    type: 'size',
                    values: selectedSizes
                } 
            }));
        });
    });
});
</script>
@endpush