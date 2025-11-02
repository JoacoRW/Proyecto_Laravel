<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    use HasFactory;

    protected $table = 'Consulta';
    protected $primaryKey = 'idConsulta';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'idPaciente',
        'idServicioSalud',
        'idProfesionalSalud',
        'idTipoConsulta',
        'fechaIngreso',
        'fechaEgreso',
        'condicionEgreso',
        'hora',
        'motivo',
        'observacion',
    ];

    protected $casts = [
        'fechaIngreso' => 'date',
        'fechaEgreso' => 'date',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'idPaciente', 'idPaciente');
    }

    public function servicio()
    {
        return $this->belongsTo(ServicioSalud::class, 'idServicioSalud', 'idServicioSalud');
    }

    public function profesional()
    {
        return $this->belongsTo(ProfesionalSalud::class, 'idProfesionalSalud', 'idProfesionalSalud');
    }

    public function diagnósticos()
    {
        return $this->belongsToMany(Diagnostico::class, 'ConsultaDiagnostico', 'idConsulta', 'idDiagnostico')
                    ->withTimestamps();
    }

    public function recetas()
    {
        return $this->hasMany(Receta::class, 'idConsulta', 'idConsulta');
    }

    public function evoluciones()
    {
        return $this->hasMany(Evolucion::class, 'idConsulta', 'idConsulta');
    }
}
