<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoProcedimiento extends Model
{
    use HasFactory;

    protected $table = 'TipoProcedimiento';
    protected $primaryKey = 'idTipoProcedimiento';

    protected $fillable = [
        'tipoProcedimiento',
    ];

    public function procedimientos()
    {
        return $this->hasMany(Procedimiento::class, 'idTipoProcedimiento', 'idTipoProcedimiento');
    }
}
