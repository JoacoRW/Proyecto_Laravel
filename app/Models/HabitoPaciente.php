<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class HabitoPaciente extends Pivot
{
    protected $table = 'HabitoPaciente';
    public $incrementing = false;
    protected $primaryKey = null;
    public $timestamps = false;

    protected $fillable = [
        'idHabito',
        'idPaciente',
        'observacion',
    ];
}