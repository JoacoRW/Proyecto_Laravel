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
            // Robust SSE client with multi-host attempts and polling fallback.
            (function(){
                if (typeof EventSource === 'undefined') return;

                // Helper: start polling fallback that fetches a minimal endpoint every 3s
                function startPollingFallback() {
                    if (window.__ssePollIntervalId) return; // already polling
                    console.debug('SSE fallback: starting polling');
                    window.__ssePollIntervalId = setInterval(async function(){
                        try {
                            // try a small lightweight endpoint if available, otherwise metrics dashboard
                            const url = '/metrics/dashboard';
                            const r = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                            if (!r.ok) return;
                            const d = await r.json();
                            window.dispatchEvent(new CustomEvent('sse-message', { detail: d }));
                        } catch (e) { /* ignore transient errors */ }
                    }, 3000);
                }

                function stopPollingFallback() {
                    if (window.__ssePollIntervalId) {
                        clearInterval(window.__ssePollIntervalId);
                        window.__ssePollIntervalId = null;
                        console.debug('SSE fallback: stopped polling');
                    }
                }

                // Attach handlers to an EventSource instance
                function attachSseHandlers(es, url) {
                    es.addEventListener('message', function(e){
                        try { const data = JSON.parse(e.data); window.dispatchEvent(new CustomEvent('sse-message', { detail: data })); } catch (err) { console.log('sse message', e.data); }
                    });
                    es.addEventListener('paciente_created', function(e){
                        try { const data = JSON.parse(e.data); window.dispatchEvent(new CustomEvent('sse-paciente-created', { detail: data })); } catch (err) { console.log('sse paciente_created', e.data); }
                    });
                    es.onopen = function(){ console.debug('SSE connected', url); stopPollingFallback(); };
                    es.onerror = function(err){ console.debug('SSE error', url, err); };
                }

                // Try multiple candidate URLs in sequence. If none succeed, start polling.
                (async function tryHosts() {
                    const nodeEnv = @json(env('NODE_API_URL')) || '';
                    const baseFromEnv = (typeof nodeEnv === 'string' && nodeEnv.length) ? nodeEnv.replace(/\/$/, '') : '';
                    const candidates = [];
                    if (baseFromEnv) candidates.push(baseFromEnv.replace(/\/$/, '') + '/events');
                    // try 127.0.0.1 and localhost explicitly
                    candidates.push('http://127.0.0.1:3001/events');
                    candidates.push('http://localhost:3001/events');
                    // fallback to same origin /events (Laravel could proxy)
                    candidates.push(location.origin + '/events');

                    for (let i = 0; i < candidates.length; i++) {
                        const url = candidates[i];
                        try {
                            // Try to open an EventSource and wait briefly for open/error
                            const es = new EventSource(url);
                            let settled = false;
                            const prom = new Promise((resolve, reject) => {
                                const onOpen = () => { settled = true; es.removeEventListener('open', onOpen); es.removeEventListener('error', onError); resolve('open'); };
                                const onError = (e) => { if (!settled) { settled = true; es.removeEventListener('open', onOpen); es.removeEventListener('error', onError); reject(e); } };
                                es.addEventListener('open', onOpen);
                                es.addEventListener('error', onError);
                                // safety timeout: if neither open nor error in 2500ms, treat as failure
                                setTimeout(()=>{ if (!settled) { settled = true; es.removeEventListener('open', onOpen); es.removeEventListener('error', onError); try { es.close(); } catch(e){}; reject(new Error('timeout')); } }, 2500);
                            });
                            try {
                                await prom; // opened
                                // attach handlers and set global ref
                                window.__sseEventSource = es;
                                attachSseHandlers(es, url);
                                console.debug('SSE connected (candidate):', url);
                                return; // success
                            } catch (err) {
                                try { es.close(); } catch(e){}
                                console.debug('SSE candidate failed:', url, err && err.message ? err.message : err);
                                // try next
                            }
                        } catch (err) {
                            console.debug('SSE attempt error for', url, err && err.message ? err.message : err);
                        }
                    }

                    // all candidates failed -> start polling fallback
                    startPollingFallback();
                })();
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
    @stack('scripts')
</html>
