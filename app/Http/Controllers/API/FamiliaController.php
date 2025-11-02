<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Familia;
use App\Models\Paciente;
use Illuminate\Http\Request;

class FamiliaController extends Controller
{
    public function index($idPaciente)
    {
        $familias = Familia::where('idOwner', $idPaciente)
            ->with('miembros')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $familias
        ]);
    }

    public function store(Request $request)
    {
        $familia = Familia::create($request->only('nombre', 'descripcion', 'idOwner'));
        return response()->json(['success' => true, 'data' => $familia]);
    }

    public function addMiembro(Request $request, $idFamilia)
    {
        $familia = Familia::findOrFail($idFamilia);
        $familia->miembros()->attach($request->idPaciente, [
            'rol' => $request->rol ?? 'miembro',
            'fechaAgregado' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Miembro agregado correctamente']);
    }

    public function removeMiembro($idFamilia, $idPaciente)
    {
        $familia = Familia::findOrFail($idFamilia);
        $familia->miembros()->detach($idPaciente);

        return response()->json(['success' => true, 'message' => 'Miembro eliminado correctamente']);
    }
}
