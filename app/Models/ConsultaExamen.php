<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultaExamen extends Model
{
    use HasFactory;

    protected $table = 'ConsultaExamen';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'idExamen',
        'idConsulta',
        'fecha',
        'observacion',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'idConsulta', 'idConsulta');
    }

    public function examen()
    {
        return $this->belongsTo(Examen::class, 'idExamen', 'idExamen');
    }
}
