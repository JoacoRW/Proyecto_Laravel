
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
        <div><strong>Fecha ingreso:</strong>
            @php
                $__fecha = data_get($consulta, 'fechaIngreso');
                $__fechaFmt = '';
                if ($__fecha) {
                    try {
                        if (is_object($__fecha) && method_exists($__fecha, 'format')) {
                            $__fechaFmt = $__fecha->format('Y-m-d');
                        } else {
                            $__fechaFmt = \Carbon\Carbon::parse($__fecha)->format('Y-m-d');
                        }
                    } catch (\Exception $e) {
                        $__fechaFmt = (string) $__fecha;
                    }
                }
            @endphp
            {{ $__fechaFmt }}
        </div>
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
                            <td class="p-2">
                                @php
                                    $__d = data_get($ce, 'fecha');
                                    $__d_v = '';
                                    if ($__d) {
                                        try {
                                            if (is_object($__d) && method_exists($__d, 'format')) {
                                                $__d_v = $__d->format('Y-m-d');
                                            } else {
                                                $__d_v = \Carbon\Carbon::parse($__d)->format('Y-m-d');
                                            }
                                        } catch (\Exception $e) {
                                            $__d_v = (string) $__d;
                                        }
                                    }
                                @endphp
                                {{ $__d_v }}
                            </td>
                            <td class="p-2">{{ $ce->observacion }}</td>
                        </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</div>

@endsection
