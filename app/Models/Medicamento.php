<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    use HasFactory;

    protected $table = 'Medicamento';
    protected $primaryKey = 'idMedicamento';
    public $incrementing = true;

    protected $fillable = [
        'nombreMedicamento',
        'empresa',
    ];
}
