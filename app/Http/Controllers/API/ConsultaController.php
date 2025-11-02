<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Consulta;
use App\Models\Diagnostico;
use App\Models\Receta;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ConsultaController extends Controller
{
    public function index(Request $request)
    {
        $patientId = $request->input('patient_id');
        $query = Consulta::with(['paciente','profesional','servicio','diagnósticos','recetas']);

        if ($patientId) $query->where('idPaciente', $patientId);

        $perPage = (int) $request->input('per_page', 20);
        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'idPaciente','idServicioSalud','idProfesionalSalud','idTipoConsulta',
            'fechaIngreso','fechaEgreso','condicionEgreso','hora','motivo','observacion',
            'diagnosticos',
            'recetas' 
        ]);

        $validator = Validator::make($data, [
            'idPaciente' => 'required|integer|exists:Paciente,idPaciente',
            'idTipoConsulta' => 'sometimes|integer|exists:TipoConsulta,idTipoConsulta',
            'fechaIngreso'=> 'nullable|date',
            'diagnosticos' => 'nullable|array',
            'recetas'      => 'nullable|array',
        ]);

        if ($validator->fails()) return response()->json(['errors'=>$validator->errors()],422);

        $consulta = Consulta::create($request->only([
            'idPaciente','idServicioSalud','idProfesionalSalud','idTipoConsulta','fechaIngreso','fechaEgreso','condicionEgreso','hora','motivo','observacion'
        ]));

        if (!empty($data['diagnosticos'])) {
            foreach ($data['diagnosticos'] as $d) {
                if (is_array($d) && empty($d['idDiagnostico'])) {
                    $diag = Diagnostico::create($d);
                    $consulta->diagnósticos()->attach($diag->idDiagnostico);
                } elseif (is_numeric($d)) {
                    $consulta->diagnósticos()->attach($d);
                }
            }
        }

        // create recetas
        if (!empty($data['recetas'])) {
            foreach ($data['recetas'] as $r) {
                $r['idConsulta'] = $consulta->idConsulta;
                Receta::create($r);
            }
        }

        return response()->json($consulta->load(['diagnósticos','recetas']), 201);
    }

    public function show($id)
    {
        $consulta = Consulta::with(['diagnósticos','recetas','evoluciones','paciente'])->findOrFail($id);
        return response()->json($consulta);
    }

    public function update(Request $request, $id)
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->update($request->only(['fechaEgreso','condicionEgreso','motivo','observacion','hora']));
        return response()->json($consulta);
    }

    public function destroy($id)
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->delete();
        return response()->json(['deleted' => true]);
    }
}
