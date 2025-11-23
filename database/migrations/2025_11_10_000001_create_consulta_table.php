<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('Consulta', function (Blueprint $table) {
            $table->increments('idConsulta');
            $table->unsignedBigInteger('idPaciente');
            $table->unsignedBigInteger('idServicioSalud')->nullable();
            $table->unsignedBigInteger('idProfesionalSalud')->nullable();
            $table->unsignedBigInteger('idTipoConsulta')->nullable();
            // Some MySQL versions do not accept CURRENT_DATE as a default for DATE columns.
            // Use a nullable date and set the value in application logic or seeders instead.
            $table->date('fechaIngreso')->nullable();
            $table->date('fechaEgreso')->nullable();
            $table->string('condicionEgreso', 100)->nullable();
            // Use nullable time; set in application logic if you need current time on insert.
            $table->time('hora')->nullable();
            $table->string('motivo', 200)->nullable();
            $table->string('observacion', 200)->nullable();
            $table->timestamps();

            // Indexes
            $table->index('idServicioSalud');
            $table->index('idProfesionalSalud');
            $table->index('idTipoConsulta');
            $table->index('fechaIngreso', 'idx_consulta_fecha');
            $table->index('idPaciente', 'idx_consulta_paciente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Consulta');
    }
};
