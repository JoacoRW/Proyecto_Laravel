<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paciente;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class PacienteController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $query = Paciente::query();

        if ($q) {
            $query->where('nombrePaciente', 'like', "%{$q}%")
                  ->orWhere('correo', 'like', "%{$q}%");
        }

        $perPage = (int) $request->input('per_page', 20);
        $patients = $query->paginate($perPage);

        return response()->json($patients);
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'nombrePaciente','fotoPerfil','fechaNacimiento','correo','telefono',
            'direccion','sexo','nacionalidad','ocupacion','prevision','tipoSangre'
        ]);

        $validator = Validator::make($data, [
            'nombrePaciente' => 'required|string|max:100',
            'correo'         => 'nullable|email|max:100|unique:Paciente,correo',
            'fechaNacimiento'=> 'required|date',
            'sexo'           => ['required', Rule::in(['masculino','femenino','otro'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $patient = Paciente::create($data);

        return response()->json($patient, 201);
    }

    public function show($id)
    {
        try {
            $paciente = Paciente::with([
                'consultas',
                'alergias',
                'habitos',
                'vacunas'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $paciente
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Paciente no encontrado.',
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $patient = Paciente::findOrFail($id);

        $data = $request->only([
            'nombrePaciente','fotoPerfil','fechaNacimiento','correo','telefono',
            'direccion','sexo','nacionalidad','ocupacion','prevision','tipoSangre'
        ]);

        $validator = Validator::make($data, [
            'nombrePaciente' => 'sometimes|required|string|max:100',
            'correo' => 'nullable|email|max:100|unique:' . (new \App\Models\Paciente)->getTable() . ',correo',
            'fechaNacimiento'=> 'sometimes|date',
            'sexo'           => ['sometimes', Rule::in(['masculino','femenino','otro'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $patient->update($data);

        return response()->json($patient);
    }

    public function destroy($id)
    {
        $patient = Paciente::findOrFail($id);
        $patient->delete();
        return response()->json(['deleted' => true]);
    }
}