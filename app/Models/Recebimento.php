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
     * Estornos do recebimento (um por ciclo de pagamentos estornados).
     */
    public function estornos(): HasMany
    {
        return $this->hasMany(Estorno::class)->orderBy('data_estorno')->orderBy('id');
    }

    /**
     * Soma, em centavos, de todos os pagamentos já registrados (inclui os estornados).
     */
    public function totalPagamentosEmCentavos(): int
    {
        return $this->somarCentavos($this->relationLoaded('pagamentos')
            ? $this->pagamentos
            : $this->pagamentos()->get());
    }

    /**
     * Soma, em centavos, de todos os estornos realizados.
     */
    public function valorEstornadoEmCentavos(): int
    {
        return $this->somarCentavos($this->relationLoaded('estornos')
            ? $this->estornos
            : $this->estornos()->get());
    }

    /**
     * Total efetivamente recebido hoje, em centavos: pagamentos ainda não estornados.
     */
    public function totalRecebidoEmCentavos(): int
    {
        return $this->somarCentavos($this->relationLoaded('pagamentos')
            ? $this->pagamentos->whereNull('estorno_id')
            : $this->pagamentos()->whereNull('estorno_id')->get());
    }

    /**
     * Pode ser estornado: pendente ou pago, com valor recebido ainda não estornado.
     */
    public function podeSerEstornado(): bool
    {
        return in_array($this->status, [self::STATUS_PENDENTE, self::STATUS_PAGO], true)
            && $this->totalRecebidoEmCentavos() > 0;
    }

    /**
     * Pode ser cancelado: pendente e sem valor recebido (ou com tudo já estornado).
     */
    public function podeSerCancelado(): bool
    {
        return $this->status === self::STATUS_PENDENTE
            && $this->totalRecebidoEmCentavos() === 0;
    }

    private function somarCentavos($registros): int
    {
        return (int) $registros->sum(fn ($registro) => (int) round((float) $registro->valor * 100));
    }

    /**
     * Saldo restante em centavos: valor original − total efetivamente recebido.
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
