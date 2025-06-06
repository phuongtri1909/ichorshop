@props([
    'items' => [],
    'title' => null,
    'subtitle' => null,
    'background' => '#f2f0f1',
])

@if ($items || $title)
    <div class="py-4" style="background: {{ $background }};">
        <div class="container">
            <div class="mx-5">
                @if ($title)
                    <div class="page-title-section">
                        <h2 class="page-title-breadcrumb fw-semibold">{{ $title }}</h2>
                        @if ($subtitle)
                            <p class="page-subtitle">{{ $subtitle }}</p>
                        @endif
                    </div>
                @endif
                
                @if ($items && count($items) > 0)
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb-custom">
                            @foreach ($items as $item)
                                @if ($loop->last)
                                    <li class="breadcrumb-item-custom active color-primary-hover">{{ $item['title'] }}</li>
                                @else
                                    <li class="breadcrumb-item-custom">
                                        <a href="{{ $item['url'] }}" class="color-primary-hover">{{ $item['title'] }}</a>
                                    </li>
                                @endif
                                @if (!$loop->last)
                                    <i class="fa-solid fa-chevron-right color-primary-5"></i>
                                @endif
                            @endforeach
                        </ol>
                    </nav>
                @endif
            </div>
        </div>
    </div>
@endif
