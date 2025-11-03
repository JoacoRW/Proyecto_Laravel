<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    public function index()
    {
        return view('db.index');
    }

    /**
     * Return JSON with tables and counts.
     */
    public function tables(Request $request)
    {
        $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
        $result = [];
        foreach ($tables as $row) {
            $t = $row->name;
            try {
                $count = DB::table($t)->count();
            } catch (\Throwable $e) {
                $count = null;
            }
            $result[] = ['table' => $t, 'count' => $count];
        }

        return response()->json($result);
    }
}
