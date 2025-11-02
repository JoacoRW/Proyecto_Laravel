<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PacienteVacuna extends Pivot
{
    protected $table = 'PacienteVacuna';
    public $incrementing = false;
    protected $primaryKey = null;
    public $timestamps = false;

    protected $fillable = [
        'idPaciente',
        'idVacuna',
        'fecha',
        'observacion',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];
}