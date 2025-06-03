@include('client.layouts.partials.header')

<body data-auth="{{ auth()->check() ? 'true' : 'false' }}">
    <div class="mt-88">
        @include('components.sweetalert')
        @include('components.toast-main')
        @include('components.toast')

        @yield('content')
        @include('components.top_button')
    </div>
</body>

@include('client.layouts.partials.footer')
