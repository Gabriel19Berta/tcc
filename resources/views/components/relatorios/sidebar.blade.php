@props(['modulos'])

<div class="space-y-2">
    @foreach($modulos as $nomeModulo => $relatorios)
        <x-relatorios.sidebar-item :titulo="$nomeModulo" :relatorios="$relatorios" />
    @endforeach
</div>

