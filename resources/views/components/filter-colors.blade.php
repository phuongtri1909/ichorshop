<div class="filter-group">
    <h6 class="filter-title">
        <span>Colors</span>
        <i class="fas fa-chevron-up toggle-icon"></i>
    </h6>
    <div class="filter-content">
        <div class="color-options">
            @foreach($colors as $color)
                <div class="color-option" 
                     data-color="{{ $color['value'] }}"
                     data-color-name="{{ $color['name'] }}"
                     data-bs-toggle="tooltip"
                     data-bs-placement="top"
                     title="{{ $color['name'] }}">
                    @if($color['value'] === null || $color['value'] === '')
                        <div class="color-circle color-null">
                            <i class="fa-solid fa-paint-roller position-absolute" style="top: 5px; left: 5px"></i>
                        </div>
                        <span class="color-name-tooltip">{{ $color['name'] }}</span>
                    @else
                        <div class="color-circle" style="background-color: {{ $color['value'] }}; {{ $color['value'] === 'white' ? 'border: 1px solid #e5e5e5;' : '' }}"></div>
                        <span class="color-name-tooltip">{{ $color['name'] }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
<style>
    .color-options {
        display: grid;
        grid-template-columns: repeat(8, 1fr);
    }

    .color-option {
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        padding: 5px;
        border-radius: 50%;
        transition: all 0.3s ease;
        position: relative;
    }

    .color-option:hover {
        transform: scale(1.1);
    }

    .color-option.active {
        outline: 2px solid var(--primary-color);
        outline-offset: -3px;
    }

    .color-circle {
        width: 25px;
        height: 25px;
        border-radius: 50%;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .color-circle.color-null {
        background: linear-gradient(45deg, #f5f5f5, #e0e0e0);
        border: 1px dashed #ccc;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #888;
    }

    .color-option.active .color-circle {
        transform: scale(0.8);
    }

    /* Custom tooltip styles */
    .color-name-tooltip {
        position: absolute;
        background-color: rgba(0, 0, 0, 0.75);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        top: -30px;
        left: 50%;
        transform: translateX(-50%);
        visibility: hidden;
        opacity: 0;
        transition: opacity 0.3s, visibility 0.3s;
        z-index: 100;
        pointer-events: none;
    }

    .color-name-tooltip::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: rgba(0, 0, 0, 0.75) transparent transparent transparent;
    }

    .color-option:hover .color-name-tooltip {
        visibility: visible;
        opacity: 1;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorOptions = document.querySelectorAll('.color-option');
    
    colorOptions.forEach(option => {
        // Xử lý khi click
        option.addEventListener('click', function() {
            this.classList.toggle('active');
            
            // Hiển thị tên màu khi click (tuỳ chọn)
            const colorName = this.getAttribute('data-color-name');
            // Bạn có thể thêm code hiển thị tên màu bằng cách khác nếu muốn
        });
        
        // Xử lý tooltip với Bootstrap nếu có sẵn
        if (typeof bootstrap !== 'undefined') {
            new bootstrap.Tooltip(option);
        }
    });
});
</script>
@endpush