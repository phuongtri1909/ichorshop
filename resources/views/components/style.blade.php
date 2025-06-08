<section class="browse-style-section py-5 bg-primary-4">
    <div class="container bg-primary-2 rounded-4 p-2 p-md-5">
        <h2 class="fw-semibold text-center py-2 pt-md-0 pb-md-4">BROWSE BY dress STYLE</h2>

        <div class="style-grid">
            @foreach ($styles as $index => $style)
                @php
                    $mod = $index % 4;
                    $isTall = in_array($mod, [1, 2]);
                @endphp

                <div class="style-card {{ $isTall ? 'tall' : 'short' }} bg-white">
                    @if($style->banner)
                    <img src="{{ $style->banner ? Storage::url($style->banner) : asset('assets/images/default/style-default-' . ($index % 4 + 1) . '.jpg') }}" alt="{{ $style->name }}" class="img-fluid">
                    @endif
                    
                    <div class="style-label">{{ $style->name }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@push('styles')
    <style>
        .style-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-auto-rows: 180px;
            gap: 20px;
        }

        .style-card {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            height: 100%;
        }

        .style-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .style-label {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-color-1);
            padding: 6px 16px;
            border-radius: 8px;
            background: rgba(255,255,255,0.8);
        }

        .style-card.short {
            grid-row: span 1;
        }

        .style-card.tall {
            grid-row: span 2;
        }

        @media (max-width: 768px) {
            .style-grid {
                grid-template-columns: 1fr;
                grid-auto-rows: auto;
            }

            .style-card.short,
            .style-card.tall {
                grid-row: auto;
                height: 200px;
            }
        }
    </style>
@endpush

