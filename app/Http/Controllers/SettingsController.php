<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('settings', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'dashboard_color_primary' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'dashboard_color_secondary' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $user = Auth::user();
        $user->update($request->only(['dashboard_color_primary', 'dashboard_color_secondary']));

        return redirect()->route('dashboard')
            ->with('status', 'Configuración guardada.')
            ->with('dashboard_color_primary', $user->dashboard_color_primary)
            ->with('dashboard_color_secondary', $user->dashboard_color_secondary);
    }
}

