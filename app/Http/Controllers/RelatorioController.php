<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    public function index(string $modulo)
    {
        $config = config("relatorios.$modulo");

        return view('relatorios.index', compact('modulo', 'config'));
    }

    public function modulos()
    {
        $modulos = config('relatorios');

        return view('relatorios.modulos', compact('modulos'));
    }
}
