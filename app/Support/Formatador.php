<?php
namespace App\Support;

class Formatador
{
    public static function moeda(?float $valor): string
    {
        return number_format($valor ?? 0, 2, ',', '.');
    }

    /**
     * Valor monetário com símbolo, no padrão brasileiro: "R$ 1.234,56".
     */
    public static function reais(?float $valor): string
    {
        return 'R$ ' . self::moeda($valor);
    }

    public static function data($data): string
    {
        return $data?->format('d/m/Y H:i:s') ?? '';
    }

    public static function status(bool $status): string
    {
        return $status ? 'Ativo' : 'Inativo';
    }
}
