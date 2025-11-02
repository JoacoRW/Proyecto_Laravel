<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Familia extends Model
{
    use HasFactory;

    protected $table = 'Familia';
    protected $primaryKey = 'idFamilia';

    protected $fillable = [
        'nombre',
        'descripcion',
        'idOwner',
    ];

    // Relaciones
    public function owner()
    {
        return $this->belongsTo(Paciente::class, 'idOwner', 'idPaciente');
    }

    public function miembros()
    {
        return $this->belongsToMany(Paciente::class, 'FamiliaPaciente', 'idFamilia', 'idPaciente')
                    ->withPivot('fechaAgregado', 'rol')
                    ->withTimestamps();
    }
}
