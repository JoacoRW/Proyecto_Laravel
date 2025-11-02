<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFamiliasTable extends Migration
{
    public function up()
    {
        Schema::create('Familia', function (Blueprint $table) {
            $table->increments('idFamilia');
            $table->string('nombre', 150)->nullable();
            $table->text('descripcion')->nullable();
            $table->unsignedInteger('idOwner');
            $table->timestamps();

            $table->index('idOwner');
            $table->foreign('idOwner')->references('idPaciente')->on('Paciente')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('Familia');
    }
}
