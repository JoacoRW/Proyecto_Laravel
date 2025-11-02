<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Models\Receta;
use Illuminate\Http\Request;

class MedicamentoController extends Controller
{
    /**
     * Obtener todos los medicamentos asociados a un paciente:
     * - Medicamentos crónicos (MedicamentoCronicoPaciente)
     * - Medicamentos recetados (últimos 6 meses)
     */
    public function obtenerMedicamentosPorPaciente($id)
    {
        $paciente = Paciente::find($id);

        if (!$paciente) {
            return response()->json([
                'success' => false,
                'message' => 'Paciente no encontrado.'
            ], 404);
        }

        //medicamentos crónicos (tabla MedicamentoCronicoPaciente)
        $medicamentosCronicos = \App\Models\MedicamentoCronicoPaciente::with('medicamento')
            ->where('idPaciente', $id)
            ->get()
            ->map(function ($m) {
                return [
                    'idMedicamento' => $m->medicamento->idMedicamento ?? null,
                    'nombreMedicamento' => $m->medicamento->nombreMedicamento ?? 'Desconocido',
                    'empresa' => $m->medicamento->empresa ?? null,
                    'tipo' => 'Crónico',
                    'fechaInicio' => $m->fechaInicio,
                    'fechaFin' => $m->fechaFin,
                    'cronico' => $m->cronico,
                ];
            });

        //medicamentos recetados (últimos 6 meses)
        $medicamentosRecetados = Receta::with('medicamento', 'consulta')
            ->whereHas('consulta', function ($q) use ($id) {
                $q->where('idPaciente', $id);
            })
            ->where('fecha', '>=', now()->subMonths(6))
            ->get()
            ->map(function ($r) {
                return [
                    'idMedicamento' => $r->medicamento->idMedicamento ?? null,
                    'nombreMedicamento' => $r->medicamento->nombreMedicamento ?? 'Desconocido',
                    'empresa' => $r->medicamento->empresa ?? null,
                    'tipo' => $r->cronico ? 'Crónico (Receta)' : 'Recetado',
                    'frecuencia' => $r->frecuencia,
                    'dosis' => $r->dosis,
                    'duracion' => $r->duracion,
                    'fecha' => $r->fecha,
                ];
            });

        //Combinar ambos conjuntos y eliminar duplicados
        $todos = $medicamentosCronicos
            ->merge($medicamentosRecetados)
            ->unique('idMedicamento')
            ->values();

        return response()->json([
            'success' => true,
            'paciente_id' => $id,
            'count' => $todos->count(),
            'data' => $todos
        ]);
    }
}
 