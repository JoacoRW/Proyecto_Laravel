<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Paciente;
use Illuminate\Validation\Rule;

class StoreConsultaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $paciente = new Paciente();
        $pacienteTable = $paciente->getTable();
        $pacientePk = $paciente->getKeyName();

        return [
            'idPaciente' => ['required', 'integer', Rule::exists($pacienteTable, $pacientePk)],
            'idTipoConsulta' => ['required', 'integer', 'exists:TipoConsulta,idTipoConsulta'],
            'fechaIngreso' => ['required', 'date'],
            'fechaEgreso' => ['nullable', 'date'],
            'hora' => ['nullable', 'date_format:H:i:s'],
            'motivo' => ['nullable', 'string', 'max:1000'],
            'observacion' => ['nullable', 'string'],
        ];
    }
}
