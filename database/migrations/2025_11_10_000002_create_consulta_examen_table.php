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
        Schema::create('ConsultaExamen', function (Blueprint $table) {
            $table->unsignedBigInteger('idExamen');
            $table->unsignedBigInteger('idConsulta');
            // Some MySQL versions do not accept CURRENT_DATE as a default for DATE columns.
            $table->date('fecha')->nullable();
            $table->string('observacion', 200)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->primary(['idExamen', 'idConsulta']);
            $table->index('idConsulta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ConsultaExamen');
    }
};
