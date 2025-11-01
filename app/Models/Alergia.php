<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alergia extends Model
{
    use HasFactory;

    protected $table = 'Alergia';
    protected $primaryKey = 'idAlergia';
    public $incrementing = true;

    protected $fillable = [
        'alergia'
    ];

    public function pacientes()
    {
        return $this->belongsToMany(Paciente::class, 'AlergiaPaciente', 'idAlergia', 'idPaciente')
                    ->withTimestamps();
    }
}
