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

    /**
     * Consultas del paciente 
     */
    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'idPaciente', 'idPaciente');
    }

    /**
     * Alergias 
     */
    public function alergias()
    {
        return $this->belongsToMany(Alergia::class, 'AlergiaPaciente', 'idPaciente', 'idAlergia')
                    ->withPivot('observacion', 'fechaRegistro');
    }

    /**
     * Habitos 
     */
    public function habitos()
    {
        return $this->belongsToMany(Habito::class, 'HabitoPaciente', 'idPaciente', 'idHabito')
                    ->withPivot('observacion');
    }

    /**
     * Vacunas
     */
    public function vacunas()
    {
        return $this->belongsToMany(Vacuna::class, 'PacienteVacuna', 'idPaciente', 'idVacuna')
                    ->withPivot('fecha', 'observacion');
    }

    /**
     * Medicamentos crónicos
     */
    public function medicamentosCronicos()
    {
        return $this->belongsToMany(Medicamento::class, 'MedicamentoCronicoPaciente', 'idPaciente', 'idMedicamento')
                    ->withPivot('fechaInicio', 'fechaFin', 'cronico')
                    ->withTimestamps();
    }


    public function recetas()
    {
        return $this->hasManyThrough(
            \App\Models\Receta::class,  
            \App\Models\Consulta::class, 
            'idPaciente',                
            'idConsulta',              
            'idPaciente',               
            'idConsulta'               
        );
    }
}
