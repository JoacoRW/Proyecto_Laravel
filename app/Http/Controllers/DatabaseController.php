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

    /**
     * Export the contents of a table as CSV (Excel-compatible).
     * Example: /db/export?table=pacientes
     */
    public function export(Request $request)
    {
        $table = $request->query('table');
        if (!$table) {
            return redirect()->back()->with('error', 'Tabla no especificada');
        }

        // Basic safety: allow only alphanumeric, underscore and dash in table name
        if (!preg_match('/^[A-Za-z0-9_\-]+$/', $table)) {
            return redirect()->back()->with('error', 'Nombre de tabla inválido');
        }

        try {
            $rows = DB::table($table)->get();
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'No se puede leer la tabla: ' . $e->getMessage());
        }

        $filename = $table . '-' . date('Ymd_His') . '.csv';

        $callback = function() use ($rows) {
            $out = fopen('php://output', 'w');
            if ($rows->isEmpty()) {
                // write empty header
                fputcsv($out, ['(empty)']);
            } else {
                // get columns from first row
                $first = (array) $rows->first();
                $headers = array_keys($first);
                fputcsv($out, $headers);
                foreach ($rows as $r) {
                    $arr = [];
                    $r = (array) $r;
                    foreach ($headers as $h) {
                        $val = $r[$h] ?? null;
                        // convert objects/arrays to JSON
                        if (is_array($val) || is_object($val)) $val = json_encode($val, JSON_UNESCAPED_UNICODE);
                        $arr[] = $val;
                    }
                    fputcsv($out, $arr);
                }
            }
            fclose($out);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export the current tables list (table name + count) as CSV.
     */
    public function exportList(Request $request)
    {
        // Reuse the same logic as tables() to get list of tables + counts
        $driver = DB::connection()->getDriverName();
        $tables = [];

        if ($driver === 'sqlite') {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
        } elseif ($driver === 'mysql') {
            $dbName = DB::getDatabaseName();
            $tables = DB::select("SELECT table_name as name FROM information_schema.tables WHERE table_schema = ? ORDER BY table_name", [$dbName]);
        } else {
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

        $rows = [];
        foreach ($tables as $row) {
            $t = $row->name;
            try {
                $count = DB::table($t)->count();
            } catch (\Throwable $e) {
                $count = null;
            }
            $rows[] = ['table' => $t, 'count' => $count];
        }

        $filename = 'db-tables-' . date('Ymd_His') . '.csv';
        $callback = function() use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['table', 'count']);
            foreach ($rows as $r) {
                fputcsv($out, [$r['table'], $r['count'] === null ? '' : $r['count']]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
