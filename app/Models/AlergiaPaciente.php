<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlergiaPaciente extends Model
{
    use HasFactory;

    protected $table = 'AlergiaPaciente';
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'idPaciente',
        'idAlergia',
        'observacion',
        'fechaRegistro',
    ];

    protected $casts = [
        'fechaRegistro' => 'date',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'idPaciente', 'idPaciente');
    }

    public function alergia()
    {
        return $this->belongsTo(Alergia::class, 'idAlergia', 'idAlergia');
    }
}
