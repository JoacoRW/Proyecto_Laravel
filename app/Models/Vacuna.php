<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacuna extends Model
{
    use HasFactory;

    protected $table = 'Vacuna';
    protected $primaryKey = 'idVacuna';
    public $incrementing = true;

    protected $fillable = [
        'nombre',
        'observacion',
    ];

    public function pacientes()
    {
        return $this->belongsToMany(Paciente::class, 'PacienteVacuna', 'idVacuna', 'idPaciente')
                    ->withPivot('fecha','observacion')->withTimestamps();
    }
}
