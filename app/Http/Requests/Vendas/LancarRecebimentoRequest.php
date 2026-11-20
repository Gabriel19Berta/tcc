<?php

namespace App\Http\Requests\Vendas;

use Illuminate\Support\Arr;

/**
 * Lançamento de recebimento para uma venda finalizada (fora do PDV).
 *
 * Reaproveita de AdicionarRecebimentoRequest a conversão do valor com máscara,
 * as regras de forma de pagamento ativa e de status (pago ou pendente, como no
 * PDV) e as mensagens. Venda e data de pagamento são definidas pelo sistema.
 */
class LancarRecebimentoRequest extends AdicionarRecebimentoRequest
{
    public function rules(): array
    {
        return Arr::only(parent::rules(), ['forma_pagamento_id', 'valor', 'data_vencimento', 'status']);
    }

    public function messages(): array
    {
        return parent::messages() + [
            'data_vencimento.required' => 'Informe a data de vencimento.',
        ];
    }
}
