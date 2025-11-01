<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicioSalud extends Model
{
    use HasFactory;

    protected $table = 'ServicioSalud';
    protected $primaryKey = 'idServicioSalud';
    public $incrementing = true;

    protected $fillable = [
        'nombreServicioSalud',
        'direccion',
        'idTipoServicioSalud',
    ];

    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'idServicioSalud', 'idServicioSalud');
    }
}
