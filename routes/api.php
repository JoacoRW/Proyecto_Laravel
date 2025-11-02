<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PacienteController;
use App\Http\Controllers\API\ConsultaController;
use App\Http\Controllers\API\RecetaController;
use App\Http\Controllers\API\DocumentController;
use App\Http\Controllers\API\DatasetController;
use App\Http\Controllers\API\DiagnosticoController;
use App\Http\Controllers\API\AlergiaController;
use App\Http\Controllers\API\MedicamentoController;
use App\Http\Controllers\API\VacunaController;
use App\Http\Controllers\API\ServicioSaludController;
use App\Http\Controllers\API\ProfesionalSaludController;
use App\Http\Controllers\API\TipoConsultaController;
use App\Http\Controllers\API\TipoProcedimientoController;

Route::post('auth/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
Route::post('auth/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);


//protected API
//Route::middleware('auth:sanctum')->group(function () {
    //Route::apiResource('patients', PacienteController::class);
    //Route::apiResource('consultations', ConsultaController::class);
    //Route::apiResource('prescriptions', RecetaController::class);
    //Route::apiResource('diagnostics', DiagnosticoController::class);
    //Route::apiResource('allergies', AlergiaController::class);
    //Route::apiResource('medicines', MedicamentoController::class);
    //Route::apiResource('vaccines', VacunaController::class);
    //Route::apiResource('services', ServicioSaludController::class);
    //Route::apiResource('professionals', ProfesionalSaludController::class);
    //Route::apiResource('types/consult', TipoConsultaController::class);
    //Route::apiResource('types/procedure', TipoProcedimientoController::class);
    //Route::post('patients/{patient}/documents', [DocumentController::class, 'store']);
    //Route::get('patients/{patient}/documents', [DocumentController::class, 'index']);
    //Route::post('documents/{id}/process', [DocumentController::class, 'process']);
    //Route::post('datasets/request', [DatasetController::class, 'requestExport']);
    //Route::get('datasets/{id}/download', [DatasetController::class, 'download']);
//});

//Endpoints públicos temporales
Route::group([], function () {

    //Endpoints personalizados
    Route::get('patients/{id}/medicines', [MedicamentoController::class, 'obtenerMedicamentosPorPaciente']);

    //Rutas API estándar
    Route::apiResource('patients', PacienteController::class);
    Route::apiResource('consultations', ConsultaController::class);
    Route::apiResource('prescriptions', RecetaController::class);
    Route::apiResource('diagnostics', DiagnosticoController::class);
    Route::apiResource('allergies', AlergiaController::class);
    Route::apiResource('medicines', MedicamentoController::class);
    Route::apiResource('vaccines', VacunaController::class);
    Route::apiResource('services', ServicioSaludController::class);
    Route::apiResource('professionals', ProfesionalSaludController::class);
    Route::apiResource('types/consult', TipoConsultaController::class);
    Route::apiResource('types/procedure', TipoProcedimientoController::class);
});