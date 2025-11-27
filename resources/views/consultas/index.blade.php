@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-6rem)] flex items-start justify-center py-4 px-4">
            <div class="w-full max-w-3xl px-2">
        <style>
            /* Card entrance animation */
            @keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
            .animate-card { animation: fadeInUp 420ms ease-out both; }

            /* Table striping and hover for better readability */
            .table-striped tbody tr:nth-child(odd) { background: rgba(255,255,255,0.015); }
            .table-striped tbody tr:nth-child(even) { background: rgba(255,255,255,0.01); }
            /* stronger hover so the row stands out more on dark background */
            .table-striped tbody tr:hover { background: rgba(255,255,255,0.08) !important; }

            /* Slightly stronger header color inside the card */
            .table-striped thead th { color: #9aa6b8; }

                /* Table card appearance */
                /* Make the card noticeably shorter so the page does not scroll; keep inner area scrollable. */
                .table-card { border: 1px solid rgba(255,255,255,0.03); outline: none !important; display: flex; flex-direction: column; max-height: calc(100vh - 12rem); margin-bottom: 1.5rem; }
                .table-card .table-container { overflow-x: auto; overflow-y: auto; flex: 1 1 auto; padding-right: 0.5rem; }
                /* Make table occupy full container width and distribute columns
                    predictably so there is no empty gap on the right. */
                .table-card table { border: none !important; width: 100%; table-layout: fixed; }
                .table-card th, .table-card td { padding-left: 0.75rem; padding-right: 0.75rem; }
                /* Keep last column actions aligned to the right and avoid overflow */
                .table-card td:last-child { text-align: right; white-space: nowrap; }
                /* Truncate long text in columns to keep layout neat */
                .table-card td { overflow: hidden; text-overflow: ellipsis; }
        </style>

        <h1 class="text-2xl font-bold mb-4 text-gray-100">Consultas</h1>

        <div class="mb-6 flex items-center justify-between gap-4">
            <form method="GET" action="{{ route('consultas.index') }}" class="flex items-center gap-2">
                <input name="q" type="search" placeholder="Buscar por paciente..." value="{{ old('q', $q ?? request('q')) }}" class="p-2 border rounded themed-input" />
                <button type="submit" class="px-3 py-2 rounded text-white" style="background:var(--dashboard-primary)">Buscar</button>
                @if(request()->has('q') && request('q') !== '')
                    <a href="{{ route('consultas.index') }}" class="ml-2 text-sm text-gray-300">Limpiar</a>
                @endif
            </form>

            <div class="flex-shrink-0">
                <a href="{{ route('consultas.create') }}" class="px-4 py-2 bg-green-600 text-gray rounded">Crear Consulta</a>
            </div>
        </div>

        <!-- Centered, smaller card that contains only the table -->
        <div class="flex justify-center">
            <div class="w-full max-w-2xl bg-[#0f1724] rounded-lg p-2 shadow-md animate-card table-card">
                <div class="table-container">
                    <table class="w-full table-auto mb-4 table-striped" style="border:none">
        <thead>
            <tr class="thead-row">
                <th class="p-1">ID</th>
                <th class="p-1">Paciente</th>
                <th class="p-1">Tipo</th>
                <th class="p-1">Fecha Ingreso</th>
                <th class="p-1">Motivo</th>
                <th class="p-1"># Exámenes</th>
                <th class="p-1">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consultas as $c)
                <tr>
                    <td class="p-1">{{ $c->idConsulta }}</td>
                    <td class="p-1">{{ optional($c->paciente)->display_name ?? ('Paciente ' . $c->idPaciente) }}</td>
                    <td class="p-1">{{ optional($c->tipoConsulta)->nombreTipoConsulta ?? $c->idTipoConsulta }}</td>
                    <td class="p-1">
                        @php
                            $__f = data_get($c, 'fechaIngreso');
                            $__fval = '';
                            if ($__f) {
                                try {
                                    if (is_object($__f) && method_exists($__f, 'format')) {
                                        $__fval = $__f->format('Y-m-d');
                                    } else {
                                        $__fval = \Carbon\Carbon::parse($__f)->format('Y-m-d');
                                    }
                                } catch (\Exception $e) {
                                    $__fval = (string) $__f;
                                }
                            }
                        @endphp
                        {{ $__fval }}
                    </td>
                    <td class="p-1">{{ $c->motivo }}</td>
                    <td class="p-1">{{ $c->consultaExamenes->count() }}</td>
                    <td class="p-1">
                        <div class="flex items-center gap-2">
                            <button onclick="document.getElementById('ex-{{ $c->idConsulta }}').classList.toggle('hidden')" class="px-2 py-1 bg-blue-600 text-gray rounded">Exámenes</button>
                            <a href="{{ route('consultas.show', $c->idConsulta) }}" class="px-2 py-1 bg-gray-200 text-gray-800 rounded">Ver</a>
                            <a href="{{ route('consultas.edit', $c->idConsulta) }}" class="px-2 py-1 bg-yellow-400 text-gray rounded">Editar</a>
                            <form action="{{ route('consultas.destroy', $c->idConsulta) }}" method="POST" onsubmit="return confirm('¿Eliminar esta consulta?');" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button class="px-2 py-1 bg-red-600 text-white rounded">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <tr id="ex-{{ $c->idConsulta }}" class="hidden">
                    <td colspan="7" class="p-1 bg-gray-800 text-gray-100">
                        <strong class="block mb-2 text-white">Exámenes asociados</strong>
                        @if($c->consultaExamenes->isEmpty())
                            <div class="block mb-2 text-white">No hay exámenes registrados.</div>
                        @else
                            <table class="w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-700 text-gray-100">
                                        <th class="p-1 text-left text-white">idExamen</th>
                                        <th class="p-1 text-left text-white">fecha</th>
                                        <th class="p-1 text-left text-white">observacion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($c->consultaExamenes as $ce)
                                        <tr class="border-b border-gray-700">
                                            <td class="p-1 text-white">{{ $ce->idExamen }}</td>
                                            <td class="p-1 text-white">
                                                @php
                                                    $__fe = data_get($ce, 'fecha');
                                                    $__fe_v = '';
                                                    if ($__fe) {
                                                        try {
                                                            if (is_object($__fe) && method_exists($__fe, 'format')) {
                                                                $__fe_v = $__fe->format('Y-m-d');
                                                            } else {
                                                                $__fe_v = \Carbon\Carbon::parse($__fe)->format('Y-m-d');
                                                            }
                                                        } catch (\Exception $e) {
                                                            $__fe_v = (string) $__fe;
                                                        }
                                                    }
                                                @endphp
                                                {{ $__fe_v }}
                                            </td>
                                            <td class="p-1 text-white">{{ $ce->observacion }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
                    </table>
                </div>

                <div class="mt-3">{{ $consultas->links() }}</div>
            </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Auto-refresh the consultas list every 3 seconds by fetching the same page
    // and replacing the table body and pagination. This avoids changing the
    // controller or adding a new endpoint.
    async function refreshConsultasList() {
        try {
            const res = await fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) throw new Error('fetch failed ' + res.status);
            const text = await res.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');

            // find the new table and pagination in the fetched document
            const newTable = doc.querySelector('table');
            const newTbody = newTable ? newTable.querySelector('tbody') : null;
            const newPagination = doc.querySelector('.pagination, nav[role="navigation"], .w-5');

            const currentTable = document.querySelector('table');
            if (currentTable && newTbody) {
                const currentTbody = currentTable.querySelector('tbody');
                if (currentTbody) {
                    currentTbody.innerHTML = newTbody.innerHTML;
                }
            }

            // replace pagination links if present
            const currentPagination = document.querySelector('.pagination') || document.querySelector('nav[role="navigation"]');
            if (currentPagination && newPagination) {
                currentPagination.innerHTML = newPagination.innerHTML;
            }

        } catch (err) {
            console.debug('refreshConsultasList error', err);
        }
    }

    // start periodic refresh every 3 seconds
    refreshConsultasList();
    setInterval(refreshConsultasList, 3 * 1000);
</script>
@endpush
