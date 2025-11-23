<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;
use Carbon\Carbon;
use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\TipoConsulta;
use App\Models\ConsultaExamen;

class GenerateDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:demo
                            {--patients=20 : Number of patients to create}
                            {--consultas=100 : Number of consultas to create}
                            {--days=90 : How many days back to spread the consultas}
                            {--exams=2 : Max exams per consulta (random 0..exams)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate random demo data for dashboard (pacientes, consultas, consulta_examen)';

    public function handle()
    {
        $faker = Faker::create();

        $patients = (int)$this->option('patients');
        $consultas = (int)$this->option('consultas');
        $days = (int)$this->option('days');
        $maxExams = (int)$this->option('exams');

        $this->info("Generando datos demo: {$patients} pacientes, {$consultas} consultas, últimos {$days} días");

        // Create some TipoConsulta if none exist
        $tipoNombres = ['Consulta general', 'Pediatría', 'Cardiología', 'Control prenatal', 'Urgencias'];
        foreach ($tipoNombres as $nombre) {
            TipoConsulta::firstOrCreate(['nombreTipoConsulta' => $nombre]);
        }

        // Create patients — detect which columns exist in the pacientes table
        $pacienteModel = new Paciente();
        $pacienteTable = $pacienteModel->getTable();
        $pacientePk = $pacienteModel->getKeyName();

        DB::transaction(function () use ($faker, $patients, $pacienteTable) {
            for ($i = 0; $i < $patients; $i++) {
                $name = $faker->name();
                $email = $faker->unique()->safeEmail();

                $data = [
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (Schema::hasColumn($pacienteTable, 'nombrePaciente')) {
                    $data['nombrePaciente'] = $name;
                } elseif (Schema::hasColumn($pacienteTable, 'nombre')) {
                    $data['nombre'] = $name;
                } elseif (Schema::hasColumn($pacienteTable, 'name')) {
                    $data['name'] = $name;
                }

                if (Schema::hasColumn($pacienteTable, 'fechaNacimiento')) {
                    $data['fechaNacimiento'] = $faker->date('Y-m-d', '-18 years');
                } elseif (Schema::hasColumn($pacienteTable, 'birth_date')) {
                    $data['birth_date'] = $faker->date('Y-m-d', '-18 years');
                }

                if (Schema::hasColumn($pacienteTable, 'correo')) {
                    $data['correo'] = $email;
                } elseif (Schema::hasColumn($pacienteTable, 'email')) {
                    $data['email'] = $email;
                }

                if (Schema::hasColumn($pacienteTable, 'telefono')) {
                    $data['telefono'] = $faker->phoneNumber();
                }

                if (Schema::hasColumn($pacienteTable, 'direccion')) {
                    $data['direccion'] = $faker->address();
                }

                DB::table($pacienteTable)->insert($data);
            }
        });

        $this->info("Pacientes creados: " . Paciente::count());

        $tipoIds = TipoConsulta::pluck('idTipoConsulta')->toArray();
        // pluck using the model primary key name in case it's not "id"
        $patientIds = Paciente::pluck($pacientePk)->toArray();

        if (empty($patientIds)) {
            $this->error('No hay pacientes para crear consultas. Ejecuta con --patients > 0');
            return 1;
        }

        // Create consultas distributed in the last N days
        DB::transaction(function () use ($faker, $consultas, $days, $maxExams, $patientIds, $tipoIds) {
            for ($i = 0; $i < $consultas; $i++) {
                $patientId = $faker->randomElement($patientIds);
                $tipoId = $faker->randomElement($tipoIds);

                $fecha = Carbon::now()->subDays(rand(0, $days))->toDateString();
                $hora = Carbon::createFromTimestamp($faker->unixTime())->format('H:i:s');

                $consulta = Consulta::create([
                    'idPaciente' => $patientId,
                    'idTipoConsulta' => $tipoId,
                    'fechaIngreso' => $fecha,
                    'fechaEgreso' => null,
                    'hora' => $hora,
                    'motivo' => $faker->sentence(6),
                    'observacion' => $faker->paragraph(2),
                ]);

                // Add some random exams
                $numExams = $maxExams > 0 ? rand(0, $maxExams) : 0;
                if ($numExams > 0) {
                    $pool = range(101, 140);
                    shuffle($pool);
                    $selected = array_slice($pool, 0, $numExams);
                    foreach ($selected as $idEx) {
                        $examData = [
                            'idExamen' => $idEx,
                            'idConsulta' => $consulta->idConsulta,
                            'fecha' => $fecha,
                            'observacion' => $faker->sentence(6),
                        ];

                        if (Schema::hasColumn('ConsultaExamen', 'created_at')) {
                            $examData['created_at'] = now();
                        }
                        if (Schema::hasColumn('ConsultaExamen', 'updated_at')) {
                            $examData['updated_at'] = now();
                        }

                        DB::table('ConsultaExamen')->insert($examData);
                    }
                }
            }
        });

        $this->info("Consultas creadas: " . Consulta::count());
        $this->info("Exámenes creados: " . ConsultaExamen::count());

        $this->info('Demo data generation complete.');
        return 0;
    }
}
