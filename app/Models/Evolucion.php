<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evolucion extends Model
{
    use HasFactory;

    protected $table = 'Evolucion';
    protected $primaryKey = 'idEvolucion';
    public $incrementing = true;

    protected $fillable = [
        'idConsulta',
        'fecha',
        'descripcion',
        'proximaCita',
    ];

    protected $casts = [
        'fecha' => 'date',
        'proximaCita' => 'date',
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'idConsulta', 'idConsulta');
    }
}
