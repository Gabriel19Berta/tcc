@props([
    'modulos',
    'moduloSelecionado',
    'relatorio',
])

<div class="space-y-2">
    @foreach($modulos as $nomeModulo => $relatorios)
        <x-relatorios.sidebar-item :titulo="$nomeModulo" :relatorios="$relatorios" :aberto="$nomeModulo === $moduloSelecionado" :relatorio="$relatorio"/>
    @endforeach
</div>

