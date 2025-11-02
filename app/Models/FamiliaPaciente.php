<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamiliaPaciente extends Model
{
    use HasFactory;

    protected $table = 'FamiliaPaciente';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'idFamilia',
        'idPaciente',
        'fechaAgregado',
        'rol',
    ];

    public function familia()
    {
        return $this->belongsTo(Familia::class, 'idFamilia', 'idFamilia');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'idPaciente', 'idPaciente');
    }
}
