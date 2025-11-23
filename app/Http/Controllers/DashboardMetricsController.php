<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Consulta;
use App\Models\ConsultaExamen;
use App\Models\TipoConsulta;
use App\Models\Paciente;

class DashboardMetricsController extends Controller
{
    public function dashboard(Request $request)
    {
        $to = $request->query('to') ? Carbon::parse($request->query('to')) : Carbon::now();
        $from = $request->query('from') ? Carbon::parse($request->query('from')) : $to->copy()->subDays(29);

        // actividad de consultas por día
        $period = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $period[$cursor->format('Y-m-d')] = 0;
            $cursor->addDay();
        }

        $consultas = Consulta::selectRaw("date(fechaIngreso) as day, count(*) as total")
            ->whereBetween('fechaIngreso', [$from->startOfDay(), $to->endOfDay()])
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        foreach ($consultas as $r) {
            $period[$r->day] = (int)$r->total;
        }

        // top examenes
        $examenes = ConsultaExamen::select('idExamen', DB::raw('count(*) as total'))
            ->whereBetween('fecha', [$from->startOfDay(), $to->endOfDay()])
            ->groupBy('idExamen')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // distribucion por tipo de consulta
        $tipos = TipoConsulta::select('idTipoConsulta', 'nombreTipoConsulta')->get();
        $tipoCounts = Consulta::select('idTipoConsulta', DB::raw('count(*) as total'))
            ->whereBetween('fechaIngreso', [$from->startOfDay(), $to->endOfDay()])
            ->groupBy('idTipoConsulta')
            ->get()->keyBy('idTipoConsulta');

        $pieLabels = [];
        $pieData = [];
        foreach ($tipos as $t) {
            $pieLabels[] = $t->nombreTipoConsulta;
            $pieData[] = isset($tipoCounts[$t->idTipoConsulta]) ? (int)$tipoCounts[$t->idTipoConsulta]->total : 0;
        }

        // KPIs
        $totalConsultas = Consulta::whereBetween('fechaIngreso', [$from->startOfDay(), $to->endOfDay()])->count();
        $totalExamenes = ConsultaExamen::whereBetween('fecha', [$from->startOfDay(), $to->endOfDay()])->count();
        $pacientesActivos = Consulta::whereBetween('fechaIngreso', [$from->startOfDay(), $to->endOfDay()])->distinct('idPaciente')->count('idPaciente');

        // pacientes nuevos por dia
        $patientsPeriod = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $patientsPeriod[$cursor->format('Y-m-d')] = 0;
            $cursor->addDay();
        }
        $patients = Paciente::selectRaw("date(created_at) as day, count(*) as total")
            ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->groupBy('day')
            ->orderBy('day')
            ->get();
        foreach ($patients as $p) {
            $patientsPeriod[$p->day] = (int)$p->total;
        }

        // top pacientes (por número de consultas)
        $topIds = Consulta::select('idPaciente', DB::raw('count(*) as total'))
            ->whereBetween('fechaIngreso', [$from->startOfDay(), $to->endOfDay()])
            ->groupBy('idPaciente')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $topPacientesLabels = [];
        $topPacientesData = [];
        foreach ($topIds as $row) {
            $pid = $row->idPaciente;
            $pac = Paciente::find($pid);
            if ($pac) {
                $name = $pac->nombrePaciente ?? $pac->nombre ?? $pac->name ?? ('Paciente ' . $pid);
            } else {
                $name = 'Paciente ' . $pid;
            }
            $topPacientesLabels[] = (string)$name;
            $topPacientesData[] = (int)$row->total;
        }

        $response = [
            'pacientes' => [
                'value' => $pacientesActivos,
                'change' => 0
            ],
            'indicador' => [ 'value' => $totalConsultas, 'change' => 0 ],
            'indicador2' => [ 'value' => array_sum($patientsPeriod), 'subtitle' => 'Nuevos pacientes' ],
            // 'ventas' se utiliza en la vista para mostrar número de exámenes
            'ventas' => [ 'value' => $totalExamenes, 'change' => 0 ],
            'actividadMedica' => [
                'labels' => array_keys($period),
                'data' => array_values($period)
            ],
            'examenes' => [
                'labels' => $examenes->pluck('idExamen')->map(function($v){ return (string)$v; })->toArray(),
                'data' => $examenes->pluck('total')->map(function($v){ return (int)$v; })->toArray()
            ],
            'pieChart' => [
                'labels' => $pieLabels,
                'data' => $pieData,
                'colors' => $this->generateColors(count($pieLabels))
            ],
            'topCentros' => [],
            'topPacientes' => [
                'labels' => $topPacientesLabels,
                'data' => $topPacientesData
            ]
        ];

        return response()->json($response);
    }

    private function generateColors($n)
    {
        $palette = ['#5b8fff', '#ff8c42', '#4ecdc4', '#3d5a80', '#f59e0b', '#ef4444', '#7c3aed', '#10b981'];
        $out = [];
        for ($i=0;$i<$n;$i++) {
            $out[] = $palette[$i % count($palette)];
        }
        return $out;
    }
}
