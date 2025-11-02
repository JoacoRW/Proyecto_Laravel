<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AlergiaPaciente extends Pivot
{
    protected $table = 'AlergiaPaciente';
    public $incrementing = false;
    protected $primaryKey = null;
    public $timestamps = false;

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