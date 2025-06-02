<div class="filter-group">
    <h6 class="filter-title">
        <span>Dress Style</span>
        <i class="fas fa-chevron-up toggle-icon"></i>
    </h6>
    <div class="filter-content">
        <div class="dress-style-options">
            @php
                $styles = ['Casual', 'Formal', 'Party', 'Gym'];
            @endphp

            @foreach($styles as $style)
                <div class="dress-style-option" data-style="{{ strtolower($style) }}">
                    <span>{{ $style }}</span>
                    <i class="fas fa-chevron-right"></i>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
<style>
    .dress-style-options {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .dress-style-option {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        color: var(--primary-color-5);
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 8px;
    }

    .dress-style-option:hover {
        background: var(--primary-color-2);
        color: var(--primary-color);
    }

    .dress-style-option.active {
        background: var(--primary-color);
        color: white;
    }

    .dress-style-option i {
        font-size: 12px;
        transition: transform 0.3s ease;
    }

    .dress-style-option:hover i {
        transform: translateX(3px);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const styleOptions = document.querySelectorAll('.dress-style-option');
    
    styleOptions.forEach(option => {
        option.addEventListener('click', function() {
            styleOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
</script>
@endpush