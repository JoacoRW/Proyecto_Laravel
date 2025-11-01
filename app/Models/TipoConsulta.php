<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoConsulta extends Model
{
    use HasFactory;

    protected $table = 'TipoConsulta';
    protected $primaryKey = 'idTipoConsulta';

    protected $fillable = [
        'nombreTipoConsulta',
    ];

    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'idTipoConsulta', 'idTipoConsulta');
    }
}
