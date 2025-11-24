@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Base de datos (actualización en tiempo real)</h1>

    <div id="last-updated" class="text-sm text-gray-600 mb-2">Última actualización: --</div>

    <table class="w-full table-auto border" id="tables">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 text-left">Tabla</th>
                <th class="p-2 text-right">Filas</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <div class="mt-4 text-right">
        <a href="{{ route('db.exportList') }}" class="inline-flex items-center gap-3 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-sm" style="box-shadow: 0 6px 18px rgba(239,68,68,0.12);">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h4a1 1 0 110 2H5v12h10V4h-3a1 1 0 110-2h4a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V3z" clip-rule="evenodd" />
                <path d="M9 7a1 1 0 012 0v4.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 011.414-1.414L9 11.586V7z" />
            </svg>
            <span class="font-semibold">Exportar lista (CSV)</span>
        </a>
    </div>
</div>

<script>
async function fetchTables() {
    try {
        const res = await fetch('{{ route('db.tables') }}');
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const data = await res.json();
        const tbody = document.querySelector('#tables tbody');
        tbody.innerHTML = '';
        data.forEach(row => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td class="p-2">${row.table}</td><td class="p-2 text-right">${row.count === null ? 'n/a' : row.count}</td>`;
            tbody.appendChild(tr);
        });
        document.getElementById('last-updated').textContent = 'Última actualización: ' + new Date().toLocaleString();
    } catch (err) {
        console.error(err);
    }
}

// Initial fetch and interval polling every 3 seconds
fetchTables();
setInterval(fetchTables, 3000);
</script>

@endsection
