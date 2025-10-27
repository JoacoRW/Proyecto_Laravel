<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Mostrar la página de configuración.
     */
    public function index()
    {
        return view('settings');
    }
}
