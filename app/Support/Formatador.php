<?php

namespace App\Support;

use Carbon\Carbon;
use DateTimeInterface;

/**
 * Formatações de apresentação (telas e relatórios).
 *
 * Não valida nem altera dados: apenas devolve o texto para exibição.
 * Documentos e telefones com quantidade de caracteres inesperada são
 * devolvidos como estão, para não esconder nem "inventar" informação.
 */
class Formatador
{
    /**
     * Valor monetário sem símbolo: "1.234,56".
     */
    public static function moeda(?float $valor): string
    {
        return number_format($valor ?? 0, 2, ',', '.');
    }

    /**
     * Valor monetário com símbolo: "R$ 1.234,56".
     */
    public static function reais(?float $valor): string
    {
        return 'R$ ' . self::moeda($valor);
    }

    /**
     * Data formatada. Aceita objetos de data (Carbon, DateTime) ou texto
     * reconhecido pelo Carbon (ex.: "2026-11-20"). Vazio retorna "".
     *
     * Padrão "d/m/Y H:i:s"; use "d/m/Y" para somente a data.
     */
    public static function data(DateTimeInterface|string|null $data, string $formato = 'd/m/Y H:i:s'): string
    {
        if ($data === null || $data === '') {
            return '';
        }

        $data = $data instanceof DateTimeInterface ? $data : Carbon::parse($data);

        return $data->format($formato);
    }

    /**
     * Booleano de status cadastral: "Ativo" / "Inativo".
     * (Status de vendas e recebimentos usam o componente <x-status-badge>.)
     */
    public static function status(bool $status): string
    {
        return $status ? 'Ativo' : 'Inativo';
    }

    /**
     * CPF com 11 dígitos: "123.456.789-00".
     */
    public static function cpf(?string $cpf): string
    {
        return self::aplicarMascara($cpf, 11, '/^(\d{3})(\d{3})(\d{3})(\d{2})$/', '$1.$2.$3-$4');
    }

    /**
     * CNPJ numérico ou alfanumérico: "12.345.678/0001-00" ou "12.ABC.345/01DE-35".
     *
     * Remove somente os caracteres da máscara (. / - e espaços), preservando
     * as letras. A expressão é a mesma usada anteriormente no Model Pessoa;
     * a validação continua sendo feita pela biblioteca geekcom/validator-docs.
     */
    public static function cnpj(?string $cnpj): string
    {
        if ($cnpj === null || trim($cnpj) === '') {
            return '';
        }

        $limpo = strtoupper(preg_replace('/[.\/\-\s]/', '', $cnpj));

        if (!preg_match('/^([A-Z0-9]{2})([A-Z0-9]{3})([A-Z0-9]{3})([A-Z0-9]{4})(\d{2})$/', $limpo, $partes)) {
            return $cnpj;
        }

        return "{$partes[1]}.{$partes[2]}.{$partes[3]}/{$partes[4]}-{$partes[5]}";
    }

    /**
     * Telefone fixo com 10 dígitos: "(44) 3333-4444".
     */
    public static function telefone(?string $telefone): string
    {
        return self::aplicarMascara($telefone, 10, '/^(\d{2})(\d{4})(\d{4})$/', '($1) $2-$3');
    }

    /**
     * Celular com 11 dígitos: "(44) 99999-8888".
     */
    public static function celular(?string $celular): string
    {
        return self::aplicarMascara($celular, 11, '/^(\d{2})(\d{5})(\d{4})$/', '($1) $2-$3');
    }

    /**
     * CEP com 8 dígitos: "87000-000".
     */
    public static function cep(?string $cep): string
    {
        return self::aplicarMascara($cep, 8, '/^(\d{5})(\d{3})$/', '$1-$2');
    }

    /**
     * Mantém somente os dígitos e aplica a máscara se a quantidade for a esperada.
     * Caso contrário, devolve o valor original.
     */
    private static function aplicarMascara(?string $valor, int $digitos, string $padrao, string $mascara): string
    {
        if ($valor === null || trim($valor) === '') {
            return '';
        }

        $numeros = preg_replace('/\D/', '', $valor);

        return strlen($numeros) === $digitos
            ? preg_replace($padrao, $mascara, $numeros)
            : $valor;
    }
}
