<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    use HasFactory;

    protected $table = 'Receta';
    public $timestamps = true;

    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'idConsulta',
        'idMedicamento',
        'frecuencia',
        'dosis',
        'fecha',
        'duracion',
        'cronico',
    ];

    protected $casts = [
        'fecha' => 'date',
        'cronico' => 'boolean',
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'idConsulta', 'idConsulta');
    }

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class, 'idMedicamento', 'idMedicamento');
    }
}
