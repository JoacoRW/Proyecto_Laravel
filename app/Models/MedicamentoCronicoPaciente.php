<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicamentoCronicoPaciente extends Model
{
    use HasFactory;

    protected $table = 'MedicamentoCronicoPaciente';
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'idPaciente',
        'idMedicamento',
        'fechaInicio',
        'fechaFin',
        'cronico',
    ];

    protected $casts = [
        'fechaInicio' => 'date',
        'fechaFin' => 'date',
        'cronico' => 'boolean',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'idPaciente', 'idPaciente');
    }

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class, 'idMedicamento', 'idMedicamento');
    }
}
