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
