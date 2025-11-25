@csrf

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block">Paciente</label>
        <select name="idPaciente" class="w-full p-2 border rounded themed-input">
            <option value="">-- Selecciona paciente --</option>
            @foreach($pacientes as $p)
                @php
                    $pk = $p->{$p->getKeyName()};
                    $label = $p->display_name;
                @endphp
                <option value="{{ $pk }}" {{ (old('idPaciente', $consulta->idPaciente ?? '') == $pk) ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('idPaciente')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="block">Tipo de consulta</label>
        <select name="idTipoConsulta" class="w-full p-2 border rounded themed-input">
            <option value="">-- Selecciona tipo --</option>
            @foreach($tipos as $t)
                <option value="{{ $t->idTipoConsulta }}" {{ (old('idTipoConsulta', $consulta->idTipoConsulta ?? '') == $t->idTipoConsulta) ? 'selected' : '' }}>{{ $t->nombreTipoConsulta }}</option>
            @endforeach
        </select>
        @error('idTipoConsulta')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="block">Fecha Ingreso</label>
        <input type="date" name="fechaIngreso" value="{{ old('fechaIngreso', isset($consulta) && $consulta->fechaIngreso ? $consulta->fechaIngreso->format('Y-m-d') : '') }}" class="w-full p-2 border rounded themed-input">
        @error('fechaIngreso')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="block">Hora (HH:MM:SS)</label>
        <input type="text" name="hora" value="{{ old('hora', isset($consulta) ? $consulta->hora : '') }}" class="w-full p-2 border rounded themed-input" placeholder="14:30:00">
        @error('hora')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
    </div>

    <div class="col-span-2">
        <label class="block">Motivo</label>
        <textarea name="motivo" class="w-full p-2 border rounded themed-input" rows="3">{{ old('motivo', $consulta->motivo ?? '') }}</textarea>
        @error('motivo')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
    </div>

    <div class="col-span-2">
        <label class="block">Observación</label>
        <textarea name="observacion" class="w-full p-2 border rounded themed-input" rows="4">{{ old('observacion', $consulta->observacion ?? '') }}</textarea>
        @error('observacion')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
    </div>
</div>
