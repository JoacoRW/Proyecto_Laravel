<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $table = 'Paciente';
    protected $primaryKey = 'idPaciente';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nombrePaciente',
        'fotoPerfil',
        'fechaNacimiento',
        'correo',
        'telefono',
        'direccion',
        'sexo',
        'nacionalidad',
        'ocupacion',
        'prevision',
        'tipoSangre',
    ];

    protected $casts = [
        'fechaNacimiento' => 'date',
    ];

    //relaciones
    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'idPaciente', 'idPaciente');
    }

    public function alergias()
    {
        return $this->belongsToMany(Alergia::class, 'AlergiaPaciente', 'idPaciente', 'idAlergia')
                    ->withPivot('observacion', 'fechaRegistro');

    }

    public function habitos()
    {
        return $this->belongsToMany(Habito::class, 'HabitoPaciente', 'idPaciente', 'idHabito')
                    ->withPivot('observacion');

    }

    public function vacunas()
    {
        return $this->belongsToMany(Vacuna::class, 'PacienteVacuna', 'idPaciente', 'idVacuna')
                    ->withPivot('fecha', 'observacion');
    }
}