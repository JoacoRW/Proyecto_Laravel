<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Familia;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FamiliaController extends Controller
{
    public function index($idPaciente)
    {
        $familias = Familia::where('idOwner', $idPaciente)
            ->with(['miembros.paciente'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $familias
        ]);
    }

    public function store(Request $request)
    {
        $familia = Familia::create($request->only('nombre', 'descripcion', 'idOwner'));

        return response()->json([
            'success' => true,
            'data' => $familia
        ]);
    }

    /**
     * Agrega un miembro a una familia.
     * Si la familia no existe, la crea automáticamente.
     */
    public function addMiembro(Request $request, $idFamilia)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'idPaciente' => 'required|integer|exists:Paciente,idPaciente',
                'rol' => 'nullable|string|max:50',
                'idOwner' => 'nullable|integer|exists:Paciente,idPaciente'
            ]);

            $rol = $validated['rol'] ?? 'familiar';
            $idPaciente = $validated['idPaciente'];
            $idOwner = $validated['idOwner'] ?? null;

            $familia = Familia::find($idFamilia);

            if (!$familia && $idOwner) {
                $owner = Paciente::find($idOwner);

                if (!$owner) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se pudo crear la familia: paciente propietario no encontrado.'
                    ], 404);
                }

                $familia = Familia::create([
                    'nombre' => 'Familia de ' . $owner->nombrePaciente,
                    'descripcion' => 'Grupo familiar creado automáticamente.',
                    'idOwner' => $owner->idPaciente
                ]);
            }

            if (!$familia) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró o no se pudo crear la familia.'
                ], 400);
            }

            $yaExiste = DB::table('FamiliaPaciente')
                ->where('idFamilia', $familia->idFamilia)
                ->where('idPaciente', $idPaciente)
                ->exists();

            if ($yaExiste) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'El paciente ya pertenece a esta familia.'
                ]);
            }

            $familia->miembros()->attach($idPaciente, [
                'rol' => $rol,
                'fechaAgregado' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Miembro agregado correctamente',
                'familia' => $familia->load('miembros.paciente')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al agregar miembro: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeMiembro($idFamilia, $idPaciente)
    {
        $familia = Familia::findOrFail($idFamilia);
        $familia->miembros()->detach($idPaciente);

        return response()->json([
            'success' => true,
            'message' => 'Miembro eliminado correctamente'
        ]);
    }
}
