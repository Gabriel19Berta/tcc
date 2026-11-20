<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Movimentação de pagamento de um recebimento.
 * Um recebimento pode ser pago em várias movimentações até quitar seu valor.
 */
class Pagamento extends Model
{
    protected $fillable = [
        'recebimento_id',
        'forma_pagamento_id',
        'estorno_id',
        'valor',
        'data_pagamento',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'data_pagamento' => 'datetime',
        ];
    }

    public function recebimento(): BelongsTo
    {
        return $this->belongsTo(Recebimento::class);
    }

    public function formaPagamento(): BelongsTo
    {
        return $this->belongsTo(FormaPagamento::class);
    }

    /**
     * Estorno que desfez este pagamento (null = pagamento ainda válido).
     */
    public function estorno(): BelongsTo
    {
        return $this->belongsTo(Estorno::class);
    }
}
