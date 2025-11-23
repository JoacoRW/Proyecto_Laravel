<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConsultaSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $types = ['Consulta General', 'Control', 'Emergencia'];
        $tipoIds = [];
        foreach ($types as $t) {
            $tipoIds[] = DB::table('TipoConsulta')->insertGetId([
                'nombreTipoConsulta' => $t,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        for ($i = 1; $i <= 3; $i++) {
            $idConsulta = DB::table('Consulta')->insertGetId([
                'idPaciente' => 1,
                'idServicioSalud' => null,
                'idProfesionalSalud' => null,
                'idTipoConsulta' => $tipoIds[array_rand($tipoIds)],
                'fechaIngreso' => $now->toDateString(),
                'fechaEgreso' => null,
                'condicionEgreso' => null,
                'hora' => $now->format('H:i:s'),
                'motivo' => "Motivo {$i}",
                'observacion' => "Observación {$i}",
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('ConsultaExamen')->insert([
                [
                    'idExamen' => 100 + $i,
                    'idConsulta' => $idConsulta,
                    'fecha' => $now->toDateString(),
                    'observacion' => "Observación examen A {$i}",
                    'created_at' => $now,
                ],
                [
                    'idExamen' => 200 + $i,
                    'idConsulta' => $idConsulta,
                    'fecha' => $now->toDateString(),
                    'observacion' => "Observación examen B {$i}",
                    'created_at' => $now,
                ],
            ]);
        }
    }
}
