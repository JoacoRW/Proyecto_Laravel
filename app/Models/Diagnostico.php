<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    use HasFactory;

    protected $table = 'Diagnostico';
    protected $primaryKey = 'idDiagnostico';
    public $incrementing = true;

    protected $fillable = [
        'urgencia',
        'cie10',
        'comentarios',
    ];

    protected $casts = [
        'urgencia' => 'boolean',
    ];

    public function consultas()
    {
        return $this->belongsToMany(Consulta::class, 'ConsultaDiagnostico', 'idDiagnostico', 'idConsulta')
                    ->withTimestamps();
    }
}
