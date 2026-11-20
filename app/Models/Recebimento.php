<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recebimento extends Model
{
    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_PAGO = 'pago';
    public const STATUS_CANCELADO = 'cancelado';

    /** Todos os status existentes (usados no filtro da listagem). */
    public const STATUS = [
        self::STATUS_PENDENTE,
        self::STATUS_PAGO,
        self::STATUS_CANCELADO,
    ];

    protected $fillable = [
        'venda_id',
        'forma_pagamento_id',
        'valor',
        'descricao',
        'data_vencimento',
        'data_pagamento',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'data_vencimento' => 'datetime',
            'data_pagamento' => 'datetime',
        ];
    }

    public function venda(): BelongsTo
    {
        return $this->belongsTo(Venda::class);
    }

    public function formaPagamento(): BelongsTo
    {
        return $this->belongsTo(FormaPagamento::class);
    }

    /**
     * Movimentações de pagamento (pagamentos parciais ou integral).
     */
    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class)->orderBy('data_pagamento')->orderBy('id');
    }

    /**
     * Soma, em centavos, dos pagamentos registrados.
     */
    public function totalRecebidoEmCentavos(): int
    {
        $total = $this->relationLoaded('pagamentos')
            ? $this->pagamentos->sum(fn (Pagamento $pagamento) => (int) round((float) $pagamento->valor * 100))
            : (int) round((float) $this->pagamentos()->sum('valor') * 100);

        return (int) $total;
    }

    /**
     * Saldo restante em centavos: valor original − pagamentos registrados.
     */
    public function saldoEmCentavos(): int
    {
        return (int) round((float) $this->valor * 100) - $this->totalRecebidoEmCentavos();
    }

    /**
     * Registra o pagamento integral (recebimento criado já como pago no PDV ou no lançamento).
     */
    public function registrarPagamentoIntegral(): void
    {
        $this->pagamentos()->create([
            'forma_pagamento_id' => $this->forma_pagamento_id,
            'valor' => $this->valor,
            'data_pagamento' => $this->data_pagamento ?? now(),
        ]);
    }
}
