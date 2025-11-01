<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PacienteVacuna extends Model
{
    use HasFactory;

    protected $table = 'PacienteVacuna';
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'idPaciente',
        'idVacuna',
        'fecha',
        'dosis',
        'observacion',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'idPaciente', 'idPaciente');
    }

    public function vacuna()
    {
        return $this->belongsTo(Vacuna::class, 'idVacuna', 'idVacuna');
    }
}
