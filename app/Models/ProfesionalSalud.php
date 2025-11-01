<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfesionalSalud extends Model
{
    use HasFactory;

    protected $table = 'ProfesionalSalud';
    protected $primaryKey = 'idProfesionalSalud';
    public $incrementing = true;

    protected $fillable = [
        'nombre',
        'especialidad',
    ];

    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'idProfesionalSalud', 'idProfesionalSalud');
    }
}
