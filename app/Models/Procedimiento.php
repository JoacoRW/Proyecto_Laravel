<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procedimiento extends Model
{
    use HasFactory;

    protected $table = 'Procedimiento';
    protected $primaryKey = 'idProcedimiento';
    public $incrementing = true;

    protected $fillable = [
        'nombreProcedimiento',
        'fecha',
        'idTipoProcedimiento',
        'indicaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];
}
