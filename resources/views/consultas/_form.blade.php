@csrf

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block">Paciente</label>
        <select name="idPaciente" class="w-full p-2 border rounded themed-input">
            <option value="">-- Selecciona paciente --</option>
            @foreach($pacientes as $p)
                @php
                    // Support both Eloquent models and plain arrays/stdClass from the Node API
                    $pk = data_get($p, 'idPaciente') ?? data_get($p, 'id') ?? (is_object($p) && method_exists($p, 'getKeyName') ? $p->{$p->getKeyName()} : null);
                    $label = data_get($p, 'display_name') ?? data_get($p, 'nombre') ?? data_get($p, 'nombrePaciente') ?? (is_object($p) ? ($p->nombrePaciente ?? 'Paciente') : ($p['nombre'] ?? 'Paciente'));
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
                @php
                    $tipoId = data_get($t, 'idTipoConsulta');
                    $tipoLabel = data_get($t, 'nombreTipoConsulta') ?? data_get($t, 'nombre');
                    $selected = (string) old('idTipoConsulta', data_get($consulta, 'idTipoConsulta', '')) === (string) $tipoId;
                @endphp
                <option value="{{ $tipoId }}" {{ $selected ? 'selected' : '' }}>{{ $tipoLabel }}</option>
            @endforeach
        </select>
        @error('idTipoConsulta')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="block">Fecha Ingreso</label>
        @php
            $fechaRaw = old('fechaIngreso', data_get($consulta, 'fechaIngreso', ''));
            $fechaValue = '';
            if ($fechaRaw) {
                if (is_object($fechaRaw) && method_exists($fechaRaw, 'format')) {
                    $fechaValue = $fechaRaw->format('Y-m-d');
                } else {
                    try {
                        $fechaValue = \Carbon\Carbon::parse($fechaRaw)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $fechaValue = (string) $fechaRaw;
                    }
                }
            }
        @endphp
        <input type="date" name="fechaIngreso" value="{{ $fechaValue }}" class="w-full p-2 border rounded themed-input">
        @error('fechaIngreso')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="block">Hora (HH:MM:SS)</label>
        <input type="text" name="hora" value="{{ old('hora', data_get($consulta, 'hora', '')) }}" class="w-full p-2 border rounded themed-input" placeholder="14:30:00">
        @error('hora')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
    </div>

    <div class="col-span-2">
        <label class="block">Motivo</label>
        <textarea name="motivo" class="w-full p-2 border rounded themed-input" rows="3">{{ old('motivo', data_get($consulta, 'motivo', '')) }}</textarea>
        @error('motivo')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
    </div>

    <div class="col-span-2">
        <label class="block">Observación</label>
        <textarea name="observacion" class="w-full p-2 border rounded themed-input" rows="4">{{ old('observacion', data_get($consulta, 'observacion', '')) }}</textarea>
        @error('observacion')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
    </div>
</div>
