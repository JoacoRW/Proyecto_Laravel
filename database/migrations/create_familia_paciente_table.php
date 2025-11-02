<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFamiliaPacienteTable extends Migration
{
    public function up()
    {
        Schema::create('FamiliaPaciente', function (Blueprint $table) {
            $table->unsignedInteger('idFamilia');
            $table->unsignedInteger('idPaciente');
            $table->date('fechaAgregado')->nullable()->useCurrent();
            $table->string('rol', 50)->nullable();

            $table->primary(['idFamilia','idPaciente']);
            $table->index('idPaciente');

            $table->foreign('idFamilia')->references('idFamilia')->on('Familia')->onDelete('cascade');
            $table->foreign('idPaciente')->references('idPaciente')->on('Paciente')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('FamiliaPaciente');
    }
}
