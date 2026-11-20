<?php

namespace App\Http\Requests\Vendas;

use App\Models\Recebimento;
use Illuminate\Validation\Rule;

/**
 * Edição de recebimento pendente: somente forma de pagamento, valor e vencimento.
 *
 * Reaproveita de AdicionarRecebimentoRequest a conversão do valor com máscara
 * ("R$ 1.234,56" → "1234.56") e as mensagens. Venda, status e data de
 * pagamento não fazem parte das regras e, portanto, nunca são aceitos.
 */
class AtualizarRecebimentoRequest extends AdicionarRecebimentoRequest
{
    public function rules(): array
    {
        /** @var Recebimento $recebimento */
        $recebimento = $this->route('recebimento');

        return [
            // Formas ativas, ou a forma atual do recebimento (mesmo que tenha sido desativada).
            'forma_pagamento_id' => [
                'required',
                'integer',
                Rule::exists('forma_pagamentos', 'id')->where(function ($query) use ($recebimento) {
                    $query->where(function ($q) use ($recebimento) {
                        $q->where('status', true)->orWhere('id', $recebimento->forma_pagamento_id);
                    });
                }),
            ],
            'valor' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'data_vencimento' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return parent::messages() + [
            'data_vencimento.required' => 'Informe a data de vencimento.',
        ];
    }
}
