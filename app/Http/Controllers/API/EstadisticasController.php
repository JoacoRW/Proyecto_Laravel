<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\Familia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EstadisticasController extends Controller
{
    /**
     * Obtener resumen de estadísticas del paciente
     * GET /api/patients/{id}/estadisticas
     */
    public function getEstadisticasPaciente($idPaciente)
    {
        try {
            $paciente = Paciente::findOrFail($idPaciente);
            
            // Métricas básicas
            $totalConsultas = $paciente->consultas()->count();
            $consultasUltimoMes = $paciente->consultas()
                ->where('fechaIngreso', '>=', Carbon::now()->subMonth())
                ->count();
            
            // Alergias y hábitos
            $totalAlergias = $paciente->alergias()->count();
            $totalHabitos = $paciente->habitos()->count();
            $totalVacunas = $paciente->vacunas()->count();
            
            // Medicamentos activos
            $medicamentosActivos = $paciente->medicamentosCronicos()
                ->wherePivot('cronico', true)
                ->orWhere(function($query) {
                    $query->whereNull('medicamentoCronicoPaciente.fechaFin')
                          ->orWhere('medicamentoCronicoPaciente.fechaFin', '>=', Carbon::now());
                })
                ->count();
            
            // Recetas del último mes
            $recetasRecientes = $paciente->recetas()
                ->where('fechaReceta', '>=', Carbon::now()->subMonth())
                ->count();
            
            // Miembros del grupo familiar
            $grupoFamiliar = Familia::whereHas('miembros', function($query) use ($idPaciente) {
                $query->where('idPaciente', $idPaciente);
            })->with('miembros')->get();
            
            $totalMiembrosFamilia = 0;
            foreach ($grupoFamiliar as $familia) {
                $totalMiembrosFamilia += $familia->miembros->count();
            }
            
            // Última consulta
            $ultimaConsulta = $paciente->consultas()
                ->orderBy('fechaIngreso', 'desc')
                ->first();
            
            // Próxima consulta (simulada - puedes agregar campo en DB)
            $proximaConsulta = null;
            
            // Consultas por mes (últimos 6 meses)
            $consultasPorMes = $paciente->consultas()
                ->select(
                    DB::raw('DATE_FORMAT(fechaIngreso, "%Y-%m") as mes'),
                    DB::raw('COUNT(*) as total')
                )
                ->where('fechaIngreso', '>=', Carbon::now()->subMonths(6))
                ->groupBy('mes')
                ->orderBy('mes', 'asc')
                ->get();
            
            // Tipos de consulta más frecuentes
            $tiposConsultaFrecuentes = $paciente->consultas()
                ->select('idTipoConsulta', DB::raw('COUNT(*) as total'))
                ->groupBy('idTipoConsulta')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->with('tipoConsulta')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'resumen' => [
                        'totalConsultas' => $totalConsultas,
                        'consultasUltimoMes' => $consultasUltimoMes,
                        'totalAlergias' => $totalAlergias,
                        'totalHabitos' => $totalHabitos,
                        'totalVacunas' => $totalVacunas,
                        'medicamentosActivos' => $medicamentosActivos,
                        'recetasRecientes' => $recetasRecientes,
                        'totalMiembrosFamilia' => $totalMiembrosFamilia,
                    ],
                    'ultimaConsulta' => $ultimaConsulta ? [
                        'fecha' => $ultimaConsulta->fechaIngreso->format('d/m/Y'),
                        'motivo' => $ultimaConsulta->motivo,
                        'profesional' => $ultimaConsulta->profesional ? $ultimaConsulta->profesional->nombreCompleto : 'N/A',
                    ] : null,
                    'proximaConsulta' => $proximaConsulta,
                    'consultasPorMes' => $consultasPorMes,
                    'tiposConsultaFrecuentes' => $tiposConsultaFrecuentes,
                ]
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Paciente no encontrado.',
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Obtener métricas de salud simuladas
     * GET /api/patients/{id}/metricas-salud
     */
    public function getMetricasSalud($idPaciente)
    {
        try {
            $paciente = Paciente::findOrFail($idPaciente);
            
            // Aquí puedes conectar con tabla de signos vitales si existe
            // Por ahora, devolvemos datos simulados realistas
            
            return response()->json([
                'success' => true,
                'data' => [
                    'presionArterial' => [
                        'valor' => '120/80',
                        'unidad' => 'mmHg',
                        'estado' => 'normal',
                        'ultimaActualizacion' => Carbon::now()->subDays(2)->toDateTimeString(),
                    ],
                    'frecuenciaCardiaca' => [
                        'valor' => 72,
                        'unidad' => 'bpm',
                        'estado' => 'normal',
                        'ultimaActualizacion' => Carbon::now()->subDays(2)->toDateTimeString(),
                    ],
                    'temperatura' => [
                        'valor' => 36.6,
                        'unidad' => '°C',
                        'estado' => 'normal',
                        'ultimaActualizacion' => Carbon::now()->subDays(2)->toDateTimeString(),
                    ],
                    'peso' => [
                        'valor' => 70,
                        'unidad' => 'kg',
                        'estado' => 'normal',
                        'ultimaActualizacion' => Carbon::now()->subWeeks(1)->toDateTimeString(),
                    ],
                    'imc' => [
                        'valor' => 22.5,
                        'unidad' => 'kg/m²',
                        'estado' => 'normal',
                        'ultimaActualizacion' => Carbon::now()->subWeeks(1)->toDateTimeString(),
                    ],
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener métricas de salud: ' . $e->getMessage(),
            ], 500);
        }
    }
}