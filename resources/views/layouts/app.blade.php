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
        <script>
            // SSE client: listens to /events and dispatches a DOM event 'sse-message'
            (function(){
                if (typeof EventSource === 'undefined') return;
                try {
                    const nodeBase = @json(env('NODE_API_URL')) || '';
                    const base = (typeof nodeBase === 'string' && nodeBase.length) ? nodeBase.replace(/\/$/, '') : '';
                    // If NODE_API_URL is set in Laravel .env, use it. Otherwise prefer localhost:3001 (Node default)
                    const defaultNode = 'http://localhost:3001';
                    const url = base ? base + '/events' : ((location.hostname === 'localhost' || location.hostname === '127.0.0.1') ? defaultNode + '/events' : '/events');
                    const es = new EventSource(url);
                    es.addEventListener('message', function(e){
                        // generic message (no event name)
                        try { const data = JSON.parse(e.data); window.dispatchEvent(new CustomEvent('sse-message', { detail: data })); } catch (err) { console.log('sse message', e.data); }
                    });
                    es.addEventListener('paciente_created', function(e){
                        try { const data = JSON.parse(e.data); window.dispatchEvent(new CustomEvent('sse-paciente-created', { detail: data })); } catch (err) { console.log('sse paciente_created', e.data); }
                    });
                    es.onopen = function(){ console.debug('SSE connected'); };
                    es.onerror = function(err){ console.debug('SSE error', err); };
                } catch (e) {
                    console.warn('SSE not available', e);
                }
            })();
        </script>
        <script>
            // UI handler for incoming paciente events: show toast and update dashboard counter if present
            (function(){
                function formatNumber(n){ return n.toLocaleString ? n.toLocaleString() : String(n); }

                function incrementCounter(id, delta){
                    const el = document.getElementById(id);
                    if (!el) return false;
                    const raw = el.textContent.replace(/,/g, '').trim();
                    const num = parseInt(raw === '' ? '0' : raw, 10) || 0;
                    el.textContent = formatNumber(num + delta);
                    return true;
                }

                function showToast(msg){
                    try {
                        let container = document.getElementById('sse-toast-container');
                        if (!container){
                            container = document.createElement('div');
                            container.id = 'sse-toast-container';
                            container.style.position = 'fixed';
                            container.style.top = '1rem';
                            container.style.right = '1rem';
                            container.style.zIndex = 99999;
                            document.body.appendChild(container);
                        }
                        const t = document.createElement('div');
                        t.textContent = msg;
                        t.style.background = 'rgba(0,0,0,0.8)';
                        t.style.color = '#fff';
                        t.style.padding = '8px 12px';
                        t.style.marginTop = '8px';
                        t.style.borderRadius = '6px';
                        t.style.boxShadow = '0 2px 6px rgba(0,0,0,0.3)';
                        container.appendChild(t);
                        setTimeout(()=>{ t.style.transition = 'opacity 400ms'; t.style.opacity = '0'; setTimeout(()=>t.remove(), 500); }, 3500);
                    } catch(e){ console.debug('toast error', e); }
                }

                window.addEventListener('sse-paciente-created', function(ev){
                    const p = ev && ev.detail ? ev.detail : null;
                    const name = p && p.nombrePaciente ? p.nombrePaciente : ('Paciente ' + (p && p.idPaciente ? p.idPaciente : 'nuevo'));
                    showToast('Paciente creado: ' + name);
                    // update dashboard counter if present
                    incrementCounter('pacientes-value', 1);
                });
            })();
        </script>
    </body>
</html>
