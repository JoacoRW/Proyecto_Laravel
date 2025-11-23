
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Crear Consulta</h1>

    @if(session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif

    <form action="{{ route('consultas.store') }}" method="POST">
        @include('consultas._form')

        <div class="mt-4">
            <button class="px-4 py-2 bg-blue-600 text-gray rounded">Guardar</button>
            <a href="{{ route('consultas.index') }}" class="ml-2 text-gray-500">Cancelar</a>
        </div>
    </form>
</div>

@endsection
