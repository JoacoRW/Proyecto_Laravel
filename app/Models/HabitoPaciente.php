<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HabitoPaciente extends Model
{
    use HasFactory;

    protected $table = 'HabitoPaciente';
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'idHabito',
        'idPaciente',
        'observacion',
    ];

    public function habito()
    {
        return $this->belongsTo(Habito::class, 'idHabito', 'idHabito');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'idPaciente', 'idPaciente');
    }
}
