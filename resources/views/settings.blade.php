@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-2xl bg-[#0f1724]/95 border border-white/5 rounded-lg p-6 shadow-lg">
        <h1 class="text-2xl font-bold mb-4 text-gray-100">Configuración</h1>

        @if(session('status'))
            <div class="mb-4 p-3 bg-green-800/60 text-green-200 rounded">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('settings.update') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-2">Color primario del Dashboard</label>
                <input id="dashboard_color_primary" type="color" name="dashboard_color_primary" value="{{ old('dashboard_color_primary', optional($user)->dashboard_color_primary ?? '#4d7cff') }}" class="w-16 h-10 p-0 border-0 rounded" />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-2">Color secundario / acento</label>
                <input id="dashboard_color_secondary" type="color" name="dashboard_color_secondary" value="{{ old('dashboard_color_secondary', optional($user)->dashboard_color_secondary ?? '#5b8fff') }}" class="w-16 h-10 p-0 border-0 rounded" />
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 rounded text-white" style="background:var(--dashboard-primary)">Guardar</button>
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-300">← Volver al dashboard</a>
            </div>
        </form>

        <div class="mt-6">
            <h3 class="text-sm font-medium mb-2 text-gray-200">Vista previa del Dashboard</h3>
            <div id="settings-preview" class="p-4 rounded" style="background:var(--dashboard-card-bg, #0b1220); color:#fff; max-width:420px">
                <div style="display:flex; justify-content:space-between; align-items:center">
                    <div>
                        <div style="font-size:12px; opacity:0.9">Pacientes activos (30d)</div>
                        <div style="font-weight:700; font-size:20px">17</div>
                    </div>
                    <div id="preview-accent" style="width:44px;height:44px;border-radius:8px;background:var(--dashboard-primary)"></div>
                </div>
            </div>
        </div>

        <script>
            // Live preview: update CSS vars on the page as user picks colors
            (function(){
                const pInput = document.getElementById('dashboard_color_primary');
                const sInput = document.getElementById('dashboard_color_secondary');
                const preview = document.getElementById('settings-preview');
                const previewAccent = document.getElementById('preview-accent');

                function apply() {
                    const p = pInput.value || '#4d7cff';
                    const s = sInput.value || '#5b8fff';
                    // apply to document so layout preview uses same vars
                    document.documentElement.style.setProperty('--dashboard-primary', p);
                    document.documentElement.style.setProperty('--dashboard-secondary', s);
                    // preview accent
                    previewAccent.style.background = `linear-gradient(135deg, ${p}, ${s})`;
                }

                pInput?.addEventListener('input', apply);
                sInput?.addEventListener('input', apply);
                // initial apply
                apply();
            })();
        </script>
    </div>
</div>
@endsection
