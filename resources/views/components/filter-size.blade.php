<div class="filter-group">
    <h6 class="filter-title">
        <span>Size</span>
        <i class="fas fa-chevron-up toggle-icon"></i>
    </h6>
    <div class="filter-content">
        <div class="size-options">
            @php
                $sizes = ['XX-Small', 'X-Small', 'Small', 'Medium', 'Large', 'X-Large', 'XX-Large', '3X-Large', '4X-Large'];
            @endphp

            @foreach($sizes as $size)
                <div class="size-option {{ $size === 'Large' ? 'active' : '' }}" data-size="{{ $size }}">
                    {{ $size }}
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
<style>
    .size-options {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
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

    .size-option:hover,
    .size-option.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    @media (max-width: 576px) {
        .size-options {
            grid-template-columns: repeat(2, 1fr);
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
    
    sizeOptions.forEach(option => {
        option.addEventListener('click', function() {
            sizeOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
</script>
@endpush