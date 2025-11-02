<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('FamiliaPaciente', function (Blueprint $table) {
            $table->id(); //id autoincremental
            $table->unsignedBigInteger('idFamilia');
            $table->unsignedBigInteger('idPaciente');
            $table->string('rol', 50)->nullable(); // Ej: Padre, Hijo, Madre
            $table->timestamp('fechaAgregado')->useCurrent();

            $table->foreign('idFamilia')->references('idFamilia')->on('Familia')->onDelete('cascade');
            $table->foreign('idPaciente')->references('idPaciente')->on('Paciente')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('FamiliaPaciente');
    }
};
