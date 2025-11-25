
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Editar Consulta #{{ $consulta->idConsulta }}</h1>

    @if(session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif

    <form action="{{ route('consultas.update', $consulta->idConsulta) }}" method="POST">
        @method('PUT')
        @include('consultas._form')

        <div class="mt-4">
            <button class="px-4 py-2 rounded text-white" style="background:var(--dashboard-primary)">Actualizar</button>
            <a href="{{ route('consultas.show', $consulta->idConsulta) }}" class="ml-2 text-gray-300">Cancelar</a>
        </div>
    </form>
</div>

@endsection
