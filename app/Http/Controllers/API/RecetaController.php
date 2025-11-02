<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Receta;
use Illuminate\Support\Facades\Validator;

class RecetaController extends Controller
{
    public function index(Request $request)
    {
        $consultaId = $request->input('consulta_id');
        $query = Receta::query();
        if ($consultaId) $query->where('idConsulta', $consultaId);
        return response()->json($query->paginate(25));
    }

    public function store(Request $request)
    {
        $data = $request->only(['idConsulta','idMedicamento','frecuencia','dosis','fecha','duracion','cronico']);

        $validator = Validator::make($data, [
            'idConsulta' => 'required|integer',
            'idMedicamento' => 'required|integer',
        ]);

        if ($validator->fails()) return response()->json(['errors'=>$validator->errors()], 422);

        $receta = Receta::create($data);
        return response()->json($receta, 201);
    }

    public function show($idConsulta, $idMedicamento)
    {
        $receta = Receta::where('idConsulta', $idConsulta)->where('idMedicamento', $idMedicamento)->firstOrFail();
        return response()->json($receta);
    }

    public function update(Request $request, $idConsulta, $idMedicamento)
    {
        $receta = Receta::where('idConsulta', $idConsulta)->where('idMedicamento', $idMedicamento)->firstOrFail();
        $receta->update($request->only(['frecuencia','dosis','fecha','duracion','cronico']));
        return response()->json($receta);
    }

    public function destroy($idConsulta, $idMedicamento)
    {
        $receta = Receta::where('idConsulta', $idConsulta)->where('idMedicamento', $idMedicamento)->firstOrFail();
        $receta->delete();
        return response()->json(['deleted' => true]);
    }
}
