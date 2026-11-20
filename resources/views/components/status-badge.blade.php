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
            'base' => 'text-xs font-semibold',
            'neutro' => 'text-gray-700',
            'status' => [
                'pago' => ['Pago', 'text-primary'],
                'pendente' => ['Pendente', 'text-beige-700'],
                'cancelado' => ['Cancelado', 'text-danger-500'],
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