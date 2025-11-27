<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Paciente;
use App\Models\TipoConsulta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Http\Requests\StoreConsultaRequest;
use App\Http\Requests\UpdateConsultaRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class ConsultaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $page = (int) $request->query('page', 1);

        // Proxy a Node API
        $base = env('NODE_API_URL', 'http://127.0.0.1:3001');
        $response = Http::get($base . '/api/consultas', ['q' => $q, 'page' => $page]);

        if (! $response->ok()) {
            // Fallback: mostrar mensaje ligero y una colección vacía
            $consultas = new LengthAwarePaginator([], 0, 25, $page, ['path' => url()->current(), 'query' => $request->query()]);
            return view('consultas.index', compact('consultas', 'q'));
        }

        $payload = $response->json();
        $items = $payload['data'] ?? [];

        // Ensure each item is an object (Blade views expect objects with properties like ->idConsulta)
            // Ensure each item is an object and shape it like an Eloquent model so Blade works
            $items = array_map(function ($it) {
                $it = is_object($it) ? $it : (object) $it;

                // Attach a `paciente` object with `display_name` to match views
                $nombrePaciente = $it->nombrePaciente ?? null;
                $it->paciente = (object) [
                    'idPaciente' => $it->idPaciente ?? null,
                    'display_name' => $nombrePaciente ?: ('Paciente ' . ($it->idPaciente ?? '')),
                ];

                // Attach tipoConsulta object
                $it->tipoConsulta = (object) [
                    'idTipoConsulta' => $it->idTipoConsulta ?? null,
                    'nombreTipoConsulta' => $it->nombreTipoConsulta ?? null,
                ];

                // consultaExamenes as a collection-like object
                $examenes = $it->consultaExamenes ?? [];
                $it->consultaExamenes = collect($examenes);

                // fechaIngreso as Carbon instance if possible, to support ->format()
                if (!empty($it->fechaIngreso)) {
                    try {
                        $it->fechaIngreso = Carbon::parse($it->fechaIngreso);
                    } catch (\Exception $e) {
                        // leave as-is if parsing fails
                    }
                }

                return $it;
            }, $items);
        $meta = $payload['meta'] ?? ['total' => count($items), 'perPage' => 25, 'page' => $page];

        $consultas = new LengthAwarePaginator($items, $meta['total'], $meta['perPage'], $meta['page'], [
            'path' => url()->current(),
            'query' => $request->query()
        ]);

        return view('consultas.index', compact('consultas', 'q'));
    }

    public function create()
    {
        $base = env('NODE_API_URL', 'http://127.0.0.1:3001');
        $response = Http::get($base . '/api/consultas/create');

        if (! $response->ok()) {
            $pacientes = collect();
            $tipos = collect();
            $consulta = null;
        } else {
            $data = $response->json('data', []);
            $pacientes = collect($data['pacientes'] ?? []);
            $tipos = collect($data['tipos'] ?? []);
            $consulta = null;
        }

        return view('consultas.create', compact('pacientes', 'tipos', 'consulta'));
    }

    public function store(StoreConsultaRequest $request)
    {
        $data = $request->validated();
        $base = env('NODE_API_URL', 'http://127.0.0.1:3001');
        $response = Http::post($base . '/api/consultas', $data);

        if (! $response->ok()) {
            return back()->withInput()->with('error', 'No se pudo crear la consulta (error en API)');
        }

        $created = $response->json('data');
        $id = $created['idConsulta'] ?? $created['id'] ?? null;

        return redirect()->route('consultas.show', $id)->with('success', 'Consulta creada correctamente.');
    }

    public function show(Consulta $consulta)
    {
        $base = env('NODE_API_URL', 'http://127.0.0.1:3001');
        $response = Http::get($base . '/api/consultas/' . $consulta->idConsulta);
        if (! $response->ok()) {
            // fallback to model
            $consulta->load(['paciente', 'tipoConsulta', 'consultaExamenes']);
            return view('consultas.show', compact('consulta'));
        }

        $payload = $response->json('data');

        // Normalize payload into an object shaped like the Eloquent model expected by views
        $it = is_object($payload) ? $payload : (object) ($payload ?? []);

        // paciente
        $nombrePaciente = $it->nombrePaciente ?? ($it->paciente->nombrePaciente ?? null ?? null);
        $it->paciente = (object) [
            'idPaciente' => $it->idPaciente ?? ($it->paciente->idPaciente ?? null),
            'display_name' => $it->paciente->display_name ?? $nombrePaciente ?? ('Paciente ' . ($it->idPaciente ?? '')),
        ];

        // tipoConsulta
        $it->tipoConsulta = (object) [
            'idTipoConsulta' => $it->idTipoConsulta ?? ($it->tipoConsulta->idTipoConsulta ?? null),
            'nombreTipoConsulta' => $it->nombreTipoConsulta ?? ($it->tipoConsulta->nombreTipoConsulta ?? null),
        ];

        // consultaExamenes as a collection-like object
        $examenes = $it->consultaExamenes ?? [];
        $examenes = array_map(function ($e) {
            return is_object($e) ? $e : (object) $e;
        }, is_array($examenes) ? $examenes : (array) $examenes);
        $it->consultaExamenes = collect($examenes);

        // fechaIngreso to Carbon if present
        if (! empty($it->fechaIngreso)) {
            try {
                $it->fechaIngreso = Carbon::parse($it->fechaIngreso);
            } catch (\Exception $e) {
                // keep original
            }
        }

        return view('consultas.show', ['consulta' => $it]);
    }

    public function edit(Consulta $consulta)
    {
        $base = env('NODE_API_URL', 'http://127.0.0.1:3001');
        $response = Http::get($base . '/api/consultas/' . $consulta->idConsulta . '/edit');

        if (! $response->ok()) {
            $pacientes = collect();
            $tipos = collect();
            return view('consultas.edit', compact('consulta', 'pacientes', 'tipos'));
        }

        $data = $response->json('data', []);
        $consultaData = $data['consulta'] ?? null;
        $pacientes = collect($data['pacientes'] ?? []);
        $tipos = collect($data['tipos'] ?? []);

        // If the node API returned a raw consulta, map it into the $consulta variable expected by the view
        if ($consultaData) {
            $consulta = (object) $consultaData;
        }

        return view('consultas.edit', compact('consulta', 'pacientes', 'tipos'));
    }

    public function update(UpdateConsultaRequest $request, Consulta $consulta)
    {
        $data = $request->validated();
        $base = env('NODE_API_URL', 'http://127.0.0.1:3001');
        $response = Http::put($base . '/api/consultas/' . $consulta->idConsulta, $data);

        if (! $response->ok()) {
            return back()->withInput()->with('error', 'No se pudo actualizar la consulta (error en API)');
        }

        return redirect()->route('consultas.show', $consulta->idConsulta)->with('success', 'Consulta actualizada correctamente.');
    }

    public function destroy(Consulta $consulta)
    {
        $base = env('NODE_API_URL', 'http://127.0.0.1:3001');
        $response = Http::delete($base . '/api/consultas/' . $consulta->idConsulta);

        if (! $response->ok()) {
            return redirect()->route('consultas.index')->with('error', 'No se pudo eliminar la consulta (error en API)');
        }

        return redirect()->route('consultas.index')->with('success', 'Consulta eliminada.');
    }
}

