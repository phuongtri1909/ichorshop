<div class="filter-group" data-filter-type="price">
    <h6 class="filter-title">
        <span>Price</span>
        <i class="fas fa-chevron-up toggle-icon"></i>
    </h6>
    <div class="filter-content">
        <div class="price-range-slider">
            <div class="range-slider">
                <input type="range" class="range-input" id="priceMin" min="{{ $priceRange['min'] }}" max="{{ $priceRange['max'] }}" value="{{ $priceRange['min'] }}" step="10">
                <input type="range" class="range-input" id="priceMax" min="{{ $priceRange['min'] }}" max="{{ $priceRange['max'] }}" value="{{ $priceRange['max'] }}" step="10">
            </div>
            <div class="price-display d-flex justify-content-between mt-3">
                <span class="price-value">$<span id="minPrice">{{ $priceRange['min'] }}</span></span>
                <span class="price-value">$<span id="maxPrice">{{ $priceRange['max'] }}</span></span>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .filter-group {
        margin-bottom: 25px;
        border-bottom: 1px solid #e5e5e5;
        padding-bottom: 20px;
    }

    .filter-title {
        display: flex;
        justify-content: between;
        align-items: center;
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 15px;
        cursor: pointer;
        user-select: none;
    }

    .toggle-icon {
        font-size: 12px;
        transition: transform 0.3s ease;
    }

    .filter-group.collapsed .toggle-icon {
        transform: rotate(180deg);
    }

    .filter-content {
        transition: all 0.3s ease;
    }

    .filter-group.collapsed .filter-content {
        display: none;
    }

    .range-slider {
        position: relative;
        height: 5px;
        background: #e5e5e5;
        border-radius: 5px;
        margin: 20px 0;
    }

    .range-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: none;
        pointer-events: none;
        -webkit-appearance: none;
        appearance: none;
    }

    .range-input::-webkit-slider-thumb {
        height: 20px;
        width: 20px;
        border-radius: 50%;
        background: var(--primary-color);
        pointer-events: auto;
        -webkit-appearance: none;
        cursor: pointer;
        border: 2px solid white;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .price-value {
        font-weight: 600;
        color: var(--primary-color);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const minSlider = document.getElementById('priceMin');
    const maxSlider = document.getElementById('priceMax');
    const minPriceDisplay = document.getElementById('minPrice');
    const maxPriceDisplay = document.getElementById('maxPrice');

    function updatePriceDisplay() {
        let minVal = parseInt(minSlider.value);
        let maxVal = parseInt(maxSlider.value);

        if (minVal >= maxVal) {
            minVal = maxVal - 10;
            minSlider.value = minVal;
        }

        minPriceDisplay.textContent = minVal;
        maxPriceDisplay.textContent = maxVal;
    }

    minSlider.addEventListener('input', updatePriceDisplay);
    maxSlider.addEventListener('input', updatePriceDisplay);
});
</script>
@endpush