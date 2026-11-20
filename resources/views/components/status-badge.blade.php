{{--
    Badge de status de venda ou de recebimento.

    Uso:
        <x-status-badge :status="$venda->status" />
        <x-status-badge :status="$recebimento->status" type="recebimento" />
--}}
@props([
    'status',
    'type' => 'venda',
])

@php
    $estilos = [
        'venda' => [
            'base' => 'btn',
            'neutro' => 'bg-gray-500',
            'status' => [
                'aberta' => ['Aberta', 'btn-warning'],
                'finalizada' => ['Finalizada', 'btn-info'],
                'quitada' => ['Quitada', 'bg-primary'],
                'cancelada' => ['Cancelada', 'btn-danger'],
            ],
        ],
        'recebimento' => [
            'base' => 'btn',
            'neutro' => 'bg-gray-500',
            'status' => [
                'pago' => ['Pago', 'bg-primary'],
                'pendente' => ['Pendente', 'btn-warning'],
                'cancelado' => ['Cancelado', 'btn-danger'],
            ],
        ],
    ];

    $estilo = $estilos[$type] ?? $estilos['venda'];

    [$texto, $cor] = $estilo['status'][$status]
        ?? [ucfirst((string) $status), $estilo['neutro']];
@endphp

<span {{ $attributes->merge(['class' => "{$estilo['base']} {$cor}"]) }}>
    {{ $texto }}
</span>