<div class="filter-group" data-filter-type="categories">
    <h6 class="filter-title">
        <span>Categories</span>
        <i class="fas fa-chevron-up toggle-icon"></i>
    </h6>
    <div class="filter-content">
        <ul class="filter-category-list row">
            @foreach ($categories as $category)
                <li class="filter-category-item col-6">
                    <div class="custom-checkbox">
                        <input type="checkbox" id="category-{{ $category->id }}" name="categories[]" value="{{ $category->id }}">
                        <label for="category-{{ $category->id }}">
                            <span class="checkbox-indicator"></span>
                            <span class="checkbox-text">{{ $category->name }}</span>
                        </label>
                    </div>
                </li>
            @endforeach
        </ul>
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
            justify-content: space-between;
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

        .filter-category-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .filter-category-item {
            margin-bottom: 10px;
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
    </style>
@endpush

