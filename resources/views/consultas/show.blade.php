
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Consulta #{{ $consulta->idConsulta }}</h1>

    @if(session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif

    <div class="mb-4 p-4 border rounded themed-card">
        <div><strong>Paciente:</strong> {{ optional($consulta->paciente)->display_name ?? ('Paciente ' . $consulta->idPaciente) }}</div>
        <div><strong>Tipo:</strong> {{ optional($consulta->tipoConsulta)->nombreTipoConsulta ?? $consulta->idTipoConsulta }}</div>
        <div><strong>Fecha ingreso:</strong> {{ $consulta->fechaIngreso?->format('Y-m-d') }}</div>
        <div><strong>Motivo:</strong> {{ $consulta->motivo }}</div>
        <div><strong>Observación:</strong> {{ $consulta->observacion }}</div>
    </div>

    <div class="mb-6">
        <a href="{{ route('consultas.edit', $consulta->idConsulta) }}" class="px-3 py-2 bg-yellow-500 text-gray rounded">Editar</a>
        <form action="{{ route('consultas.destroy', $consulta->idConsulta) }}" method="POST" style="display:inline" onsubmit="return confirm('Eliminar esta consulta?');">
            @method('DELETE')
            @csrf
            <button class="px-3 py-2 bg-red-600 text-white rounded">Eliminar</button>
        </form>
        <a href="{{ route('consultas.index') }}" class="ml-2 text-gray-500">Volver</a>
    </div>

    <h2 class="text-xl font-semibold mb-2">Exámenes</h2>
    @if($consulta->consultaExamenes->isEmpty())
        <div>No hay exámenes registrados.</div>
    @else
        <table class="w-full table-auto border table-container">
            <thead>
                <tr class="thead-row">
                    <th class="p-2">idExamen</th>
                    <th class="p-2">Fecha</th>
                    <th class="p-2">Observación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($consulta->consultaExamenes as $ce)
                    <tr>
                        <td class="p-2">{{ $ce->idExamen }}</td>
                        <td class="p-2">{{ $ce->fecha?->format('Y-m-d') }}</td>
                        <td class="p-2">{{ $ce->observacion }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</div>

@endsection
