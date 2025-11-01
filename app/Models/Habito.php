<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habito extends Model
{
    use HasFactory;

    protected $table = 'Habito';
    protected $primaryKey = 'idHabito';
    public $incrementing = true;

    protected $fillable = [
        'habito'
    ];

    public function pacientes()
    {
        return $this->belongsToMany(Paciente::class, 'HabitoPaciente', 'idHabito', 'idPaciente')
                    ->withTimestamps();
    }
}
