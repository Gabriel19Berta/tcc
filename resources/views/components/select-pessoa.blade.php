@props([
    'name',
    'tipo',
    'textoLivre' => false,
])

@php
    $valor = request($name);
    $texto = $valor;

    // Mantém a seleção após o envio: mostra o nome da pessoa escolhida.
    if ($valor && preg_match('/^pessoa:(\d+)$/', $valor, $partes)) {
        $texto = \App\Models\Pessoa::find($partes[1])?->descricaoBusca() ?? $valor;
    }
@endphp

<select id="{{ $name }}" name="{{ $name }}"
    {{ $attributes->merge(['class' => 'select-pessoa']) }}
    data-url="{{ route('pessoas.buscar', ['tipo' => $tipo]) }}"
    data-texto-livre="{{ $textoLivre ? '1' : '0' }}">
    <option value=""></option>

    @if ($valor)
        <option value="{{ $valor }}" selected>{{ $texto }}</option>
    @endif
</select>
