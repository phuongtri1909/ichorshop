<div class="filter-group">
    <h6 class="filter-title">
        <span>Colors</span>
        <i class="fas fa-chevron-up toggle-icon"></i>
    </h6>
    <div class="filter-content">
        <div class="color-options">
            @php
                $colors = [
                    ['name' => 'Green', 'value' => 'green', 'hex' => '#22c55e'],
                    ['name' => 'Red', 'value' => 'red', 'hex' => '#ef4444'],
                    ['name' => 'Yellow', 'value' => 'yellow', 'hex' => '#eab308'],
                    ['name' => 'Orange', 'value' => 'orange', 'hex' => '#f97316'],
                    ['name' => 'Cyan', 'value' => 'cyan', 'hex' => '#06b6d4'],
                    ['name' => 'Blue', 'value' => 'blue', 'hex' => '#3b82f6'],
                    ['name' => 'Purple', 'value' => 'purple', 'hex' => '#a855f7'],
                    ['name' => 'Pink', 'value' => 'pink', 'hex' => '#ec4899'],
                    ['name' => 'White', 'value' => 'white', 'hex' => '#ffffff'],
                    ['name' => 'Black', 'value' => 'black', 'hex' => '#000000']
                ];
            @endphp

            @foreach($colors as $color)
                <div class="color-option" data-color="{{ $color['value'] }}">
                    <div class="color-circle" style="background-color: {{ $color['hex'] }}; {{ $color['value'] === 'white' ? 'border: 1px solid #e5e5e5;' : '' }}"></div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
<style>
    .color-options {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
    }

    .color-option {
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        padding: 5px;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .color-option:hover {
        transform: scale(1.1);
    }

    .color-option.active {
        outline: 2px solid var(--primary-color);
        outline-offset: 2px;
    }

    .color-circle {
        width: 37px;
        height: 37px;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .color-option.active .color-circle {
        transform: scale(0.8);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorOptions = document.querySelectorAll('.color-option');
    
    colorOptions.forEach(option => {
        option.addEventListener('click', function() {
            this.classList.toggle('active');
        });
    });
});
</script>
@endpush