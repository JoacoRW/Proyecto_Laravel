<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Paciente;
use App\Models\TipoConsulta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Http\Requests\StoreConsultaRequest;
use App\Http\Requests\UpdateConsultaRequest;

class ConsultaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $query = Consulta::with(['paciente', 'tipoConsulta', 'consultaExamenes']);

        if ($q !== '') {
            // detect paciente name column like in create/edit
            $pacienteModel = new Paciente();
            $pacienteTable = $pacienteModel->getTable();
            if (Schema::hasColumn($pacienteTable, 'nombrePaciente')) {
                $nameCol = 'nombrePaciente';
            } elseif (Schema::hasColumn($pacienteTable, 'nombre')) {
                $nameCol = 'nombre';
            } elseif (Schema::hasColumn($pacienteTable, 'name')) {
                $nameCol = 'name';
            } else {
                $nameCol = $pacienteModel->getKeyName();
            }

            $query->whereHas('paciente', function ($qwhere) use ($nameCol, $q) {
                $qwhere->where($nameCol, 'like', '%' . $q . '%');
            });
        }

        $consultas = $query->orderByDesc('idConsulta')
            ->paginate(15)
            ->appends(['q' => $q]);

        return view('consultas.index', compact('consultas', 'q'));
    }

    public function create()
    {
        // Determine an existing name column on the Paciente table to order by
        $pacienteModel = new Paciente();
        $pacienteTable = $pacienteModel->getTable();
        if (Schema::hasColumn($pacienteTable, 'nombrePaciente')) {
            $orderBy = 'nombrePaciente';
        } elseif (Schema::hasColumn($pacienteTable, 'nombre')) {
            $orderBy = 'nombre';
        } elseif (Schema::hasColumn($pacienteTable, 'name')) {
            $orderBy = 'name';
        } else {
            $orderBy = $pacienteModel->getKeyName();
        }

        $pacientes = Paciente::orderBy($orderBy)->limit(200)->get();
        $tipos = TipoConsulta::orderBy('nombreTipoConsulta')->get();
        return view('consultas.create', compact('pacientes', 'tipos'));
    }

    public function store(StoreConsultaRequest $request)
    {
        $data = $request->validated();
        $consulta = Consulta::create($data);
        return redirect()->route('consultas.show', $consulta->idConsulta)
            ->with('success', 'Consulta creada correctamente.');
    }

    public function show(Consulta $consulta)
    {
        $consulta->load(['paciente', 'tipoConsulta', 'consultaExamenes']);
        return view('consultas.show', compact('consulta'));
    }

    public function edit(Consulta $consulta)
    {
        $pacienteModel = new Paciente();
        $pacienteTable = $pacienteModel->getTable();
        if (Schema::hasColumn($pacienteTable, 'nombrePaciente')) {
            $orderBy = 'nombrePaciente';
        } elseif (Schema::hasColumn($pacienteTable, 'nombre')) {
            $orderBy = 'nombre';
        } elseif (Schema::hasColumn($pacienteTable, 'name')) {
            $orderBy = 'name';
        } else {
            $orderBy = $pacienteModel->getKeyName();
        }

        $pacientes = Paciente::orderBy($orderBy)->limit(200)->get();
        $tipos = TipoConsulta::orderBy('nombreTipoConsulta')->get();
        return view('consultas.edit', compact('consulta', 'pacientes', 'tipos'));
    }

    public function update(UpdateConsultaRequest $request, Consulta $consulta)
    {
        $consulta->update($request->validated());
        return redirect()->route('consultas.show', $consulta->idConsulta)
            ->with('success', 'Consulta actualizada correctamente.');
    }

    public function destroy(Consulta $consulta)
    {
        $consulta->delete();
        return redirect()->route('consultas.index')->with('success', 'Consulta eliminada.');
    }
}

