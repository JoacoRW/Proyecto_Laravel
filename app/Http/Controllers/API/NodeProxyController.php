<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NodeProxyController extends Controller
{
    protected $base;

    public function __construct()
    {
        $this->base = config('services.node_api.url') ?? env('NODE_API_URL');
    }

    public function index()
    {
        if (! $this->base) return response()->json(['error' => 'NODE_API_URL not configured'], 500);
        $res = Http::timeout(10)->get(rtrim($this->base, '/') . '/api/pacientes');
        return response()->json($res->json(), $res->status());
    }

    public function show($id)
    {
        if (! $this->base) return response()->json(['error' => 'NODE_API_URL not configured'], 500);
        $res = Http::timeout(10)->get(rtrim($this->base, '/') . '/api/pacientes/' . intval($id));
        return response()->json($res->json(), $res->status());
    }
}
