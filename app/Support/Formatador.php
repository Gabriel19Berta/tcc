<?php
namespace App\Support;

class Formatador
{
    public static function moeda(?float $valor): string
    {
        return number_format($valor ?? 0, 2, ',', '.');
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
