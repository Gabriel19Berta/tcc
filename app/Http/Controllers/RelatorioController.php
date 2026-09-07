<?php

namespace App\Http\Controllers;

use App\Exports\MarcasExport;
use App\Relatorios\ExportadorManager;
use App\Relatorios\RelatorioManager;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class RelatorioController extends Controller
{
    public function index(Request $request, RelatorioManager $manager)
    {
        $relatorio = null;
        $dados = collect();

        if ($request->filled('relatorio')) {

            $relatorio = $manager->buscar($request->relatorio);

            // Se houver filtros enviados, gera o relatório
            if ($request->hasAny(['nome'])) {
                $dados = $relatorio->gerar($request->all());
            }
        }

        return view('relatorios.index', [
            'modulos' => $manager->modulos(),
            'relatorio' => $relatorio,
            'dados' => $dados,
            'moduloSelecionado' => $relatorio?->modulo(),
        ]);
    }

    public function gerar(Request $request, RelatorioManager $relatorios, ExportadorManager $exportadores) 
    {
        $relatorio = $relatorios->buscar($request->relatorio);

        $exportador = $exportadores->obter($request->formato);

        return $exportador->exportar($relatorio, $request->all());
    }

    public function excel(Request $request, RelatorioManager $manager)
    {
        $relatorio = $manager->buscar($request->slug);
        $dados = $relatorio->gerar($request->all());

        return Excel::download(new MarcasExport($dados), 'marcas.xlsx');
    }
}
