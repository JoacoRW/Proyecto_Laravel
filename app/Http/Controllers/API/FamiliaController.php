<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Familia;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FamiliaController extends Controller
{
    public function index($idPaciente)
    {
        try {
            Log::info("Buscando familias para paciente: {$idPaciente}");
            
            $familias = Familia::where('idOwner', $idPaciente)
                ->with(['miembros']) 
                ->get();

            Log::info("Familias encontradas: " . $familias->count());

            return response()->json([
                'success' => true,
                'data' => $familias
            ]);

        } catch (\Exception $e) {
            Log::error("Error en FamiliaController@index: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'idOwner' => 'required|integer|exists:Paciente,idPaciente'
            ]);

            $familia = Familia::create($validated);

            return response()->json([
                'success' => true,
                'data' => $familia
            ]);

        } catch (\Exception $e) {
            Log::error("Error en FamiliaController@store: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear familia: ' . $e->getMessage()
            ], 500);
        }
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

            Log::info("Agregando miembro: familia={$idFamilia}, paciente={$idPaciente}, rol={$rol}");

            $familia = Familia::find($idFamilia);

            if (!$familia && $idOwner) {
                Log::info("Familia no encontrada, creando nueva para owner: {$idOwner}");
                
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
                
                Log::info("Nueva familia creada: {$familia->idFamilia}");
            }

            if (!$familia) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró o no se pudo crear la familia.'
                ], 400);
            }

            // Verificar si ya existe la relación
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

            // Insertar usando DB::table para evitar problemas con Eloquent
            DB::table('FamiliaPaciente')->insert([
                'idFamilia' => $familia->idFamilia,
                'idPaciente' => $idPaciente,
                'rol' => $rol,
                'fechaAgregado' => now()
            ]);

            DB::commit();

            // Recargar la familia con los miembros
            $familia->load('miembros');

            return response()->json([
                'success' => true,
                'message' => 'Miembro agregado correctamente',
                'familia' => $familia
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en FamiliaController@addMiembro: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error al agregar miembro: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeMiembro($idFamilia, $idPaciente)
    {
        try {
            $familia = Familia::findOrFail($idFamilia);
            
            DB::table('FamiliaPaciente')
                ->where('idFamilia', $idFamilia)
                ->where('idPaciente', $idPaciente)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Miembro eliminado correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error("Error en FamiliaController@removeMiembro: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar miembro: ' . $e->getMessage()
            ], 500);
        }
    }
}