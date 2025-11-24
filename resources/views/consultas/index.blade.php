@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Consultas</h1>

    <div class="mb-4 flex justify-end">
        <a href="{{ route('consultas.create') }}" class="px-4 py-2 bg-green-600 text-gray rounded">Crear Consulta</a>
    </div>

    <table class="w-full table-auto border mb-4">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2">ID</th>
                <th class="p-2">Paciente</th>
                <th class="p-2">Tipo</th>
                <th class="p-2">Fecha Ingreso</th>
                <th class="p-2">Motivo</th>
                <th class="p-2"># Exámenes</th>
                <th class="p-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consultas as $c)
                <tr>
                    <td class="p-2">{{ $c->idConsulta }}</td>
                    <td class="p-2">{{ optional($c->paciente)->display_name ?? ('Paciente ' . $c->idPaciente) }}</td>
                    <td class="p-2">{{ optional($c->tipoConsulta)->nombreTipoConsulta ?? $c->idTipoConsulta }}</td>
                    <td class="p-2">{{ $c->fechaIngreso?->format('Y-m-d') }}</td>
                    <td class="p-2">{{ $c->motivo }}</td>
                    <td class="p-2">{{ $c->consultaExamenes->count() }}</td>
                    <td class="p-2">
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
                    <td colspan="7" class="p-2 bg-gray-800 text-gray-100">
                        <strong class="block mb-2 text-white">Exámenes asociados</strong>
                        @if($c->consultaExamenes->isEmpty())
                            <div class="block mb-2 text-white">No hay exámenes registrados.</div>
                        @else
                            <table class="w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-700 text-gray-100">
                                        <th class="p-2 text-left text-white">idExamen</th>
                                        <th class="p-2 text-left text-white">fecha</th>
                                        <th class="p-2 text-left text-white">observacion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($c->consultaExamenes as $ce)
                                        <tr class="border-b border-gray-700">
                                            <td class="p-2 text-white">{{ $ce->idExamen }}</td>
                                            <td class="p-2 text-white">{{ $ce->fecha?->format('Y-m-d') }}</td>
                                            <td class="p-2 text-white">{{ $ce->observacion }}</td>
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

    {{ $consultas->links() }}
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
