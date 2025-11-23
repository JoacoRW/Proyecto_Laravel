<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --dashboard-primary: {{ auth()->check() && auth()->user()->dashboard_color_primary ? auth()->user()->dashboard_color_primary : '#4d7cff' }};
                --dashboard-secondary: {{ auth()->check() && auth()->user()->dashboard_color_secondary ? auth()->user()->dashboard_color_secondary : '#5b8fff' }};
            }
        </style>
        @if(session()->has('dashboard_color_primary') || session()->has('dashboard_color_secondary'))
            <script>
                // Apply flashed dashboard colors immediately for the current request
                try {
                    const p = @json(session('dashboard_color_primary'));
                    const s = @json(session('dashboard_color_secondary'));
                    if (p) document.documentElement.style.setProperty('--dashboard-primary', p);
                    if (s) document.documentElement.style.setProperty('--dashboard-secondary', s);
                } catch (e) { console.error(e); }
            </script>
        @endif
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @if (isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </main>
        </div>
    </body>
</html>
