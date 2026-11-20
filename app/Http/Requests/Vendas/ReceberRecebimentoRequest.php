<?php

namespace App\Http\Requests\Vendas;

use Illuminate\Support\Arr;

/**
 * Registro de um pagamento (parcial ou total) de um recebimento pendente.
 *
 * O usuário informa a forma de pagamento utilizada (ativa ou a forma atual do
 * recebimento — mesma regra da edição) e o valor recebido (maior que zero,
 * com a mesma conversão de máscara monetária). O limite pelo saldo restante,
 * a data, o status e a venda são definidos/validados pelo sistema.
 */
class ReceberRecebimentoRequest extends AtualizarRecebimentoRequest
{
    public function rules(): array
    {
        return Arr::only(parent::rules(), ['forma_pagamento_id', 'valor']) + [
            // Data e horário efetivos do pagamento, informados pelo usuário (passado ou futuro).
            // date_format rejeita datas/horários inexistentes (ex.: 30/02, 25:00).
            'data_pagamento' => ['required', 'date_format:Y-m-d'],
            'hora_pagamento' => ['required', 'date_format:H:i'],
        ];
    }

    public function messages(): array
    {
        return [
            'valor.required' => 'Informe o valor recebido.',
            'data_pagamento.required' => 'Informe a data do pagamento.',
            'data_pagamento.date_format' => 'Informe uma data de pagamento válida.',
            'hora_pagamento.required' => 'Informe o horário do pagamento.',
            'hora_pagamento.date_format' => 'Informe um horário de pagamento válido (HH:MM).',
        ] + parent::messages();
    }
}
