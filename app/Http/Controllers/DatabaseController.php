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

    public function tables(Request $request)
    {
        $driver = DB::connection()->getDriverName();
        $tables = [];

        if ($driver === 'sqlite') {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
        } elseif ($driver === 'mysql') {
            $dbName = DB::getDatabaseName();
            $tables = DB::select("SELECT table_name as name FROM information_schema.tables WHERE table_schema = ? ORDER BY table_name", [$dbName]);
        } else {
            // Fallback: attempt generic SHOW TABLES and map column to 'name'
            try {
                $raw = DB::select('SHOW TABLES');
                if (!empty($raw)) {
                    $first = (array) $raw[0];
                    $col = array_keys($first)[0];
                    $tables = array_map(function ($r) use ($col) {
                        return (object) ['name' => $r->$col];
                    }, $raw);
                }
            } catch (\Throwable $e) {
                $tables = [];
            }
        }

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
