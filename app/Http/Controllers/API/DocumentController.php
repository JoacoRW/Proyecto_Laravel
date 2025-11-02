<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessDocumentJob;
use App\Models\Paciente;

class DocumentController extends Controller
{
    public function index($patientId)
    {

        $prefix = "patients/{$patientId}/documents/";
        $objects = Storage::disk('s3')->files($prefix);
        return response()->json(['files' => $objects]);
    }

    public function store(Request $request, $patientId)
    {
        $request->validate([
            'file' => 'required|file|max:10240' // 10MB
        ]);

        $file = $request->file('file');
        $path = "patients/{$patientId}/documents/" . time() . "_" . $file->getClientOriginalName();
        Storage::disk('s3')->put($path, fopen($file->getPathname(), 'r+'));

        //Registrar metadatos en DB si deseas (tabla Document)
        ProcessDocumentJob::dispatch($patientId, $path);

        return response()->json(['path' => $path], 201);
    }

    public function process($id)
    {

        return response()->json(['processed' => true]);
    }
}
