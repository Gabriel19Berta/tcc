<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venda extends Model
{
    public const STATUS_ABERTA = 'aberta';
    public const STATUS_FINALIZADA = 'finalizada';
    public const STATUS_QUITADA = 'quitada';
    public const STATUS_CANCELADA = 'cancelada';

    protected $fillable = [
        'cliente_id',
        'funcionario_id',
        'status',
        'valor_produtos',
        'descontos',
        'outros_custos',
        'valor_total',
        'data',
        'justificativa_cancelamento',
    ];

    protected function casts(): array
    {
        return [
            'valor_produtos' => 'decimal:2',
            'descontos' => 'decimal:2',
            'outros_custos' => 'decimal:2',
            'valor_total' => 'decimal:2',
            'data' => 'datetime',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ItemVenda::class);
    }

    public function recebimentos(): HasMany
    {
        return $this->hasMany(Recebimento::class);
    }

    /**
     * Status que permitem tentar o cancelamento.
     * (A existência de recebimentos pagos é verificada à parte, no cancelamento.)
     */
    public function podeSerCancelada(): bool
    {
        return in_array($this->status, [
            self::STATUS_ABERTA,
            self::STATUS_FINALIZADA,
            self::STATUS_QUITADA,
        ], true);
    }

    /**
     * Situação financeira de uma venda concluída, conforme os recebimentos pagos:
     * total pago ≥ valor total → quitada; caso contrário → finalizada.
     * (Regra usada na finalização do PDV e nas operações de recebimentos.)
     */
    public function situacaoPorPagamentos(): string
    {
        $totalPago = $this->recebimentos()
            ->where('status', Recebimento::STATUS_PAGO)
            ->sum('valor');

        // Compara em centavos para evitar imprecisão de ponto flutuante.
        $pagoCentavos = (int) round((float) $totalPago * 100);
        $totalCentavos = (int) round((float) $this->valor_total * 100);

        return $pagoCentavos >= $totalCentavos
            ? self::STATUS_QUITADA
            : self::STATUS_FINALIZADA;
    }

    /**
     * Reaplica a situação financeira (quitada ou finalizada) de uma venda já
     * concluída, depois de pagamentos, lançamentos, estornos ou cancelamentos.
     * Vendas abertas ou canceladas não mudam: na aberta, quem define a situação
     * é a finalização do PDV.
     */
    public function atualizarSituacaoFinanceira(): void
    {
        if (!in_array($this->status, [self::STATUS_FINALIZADA, self::STATUS_QUITADA], true)) {
            return;
        }

        $this->update(['status' => $this->situacaoPorPagamentos()]);
    }

    /**
     * Soma, em centavos, do valor original dos recebimentos que comprometem a
     * venda (pagos e pendentes). Cancelados ficam fora: o valor volta a ficar
     * disponível. Opcionalmente ignora um recebimento (edição) e bloqueia as
     * linhas somadas (dentro de transação).
     */
    public function recebimentosComprometidosEmCentavos(?int $ignorarId = null, bool $bloquear = false): int
    {
        return $this->recebimentos()
            ->where('status', '!=', Recebimento::STATUS_CANCELADO)
            ->when($ignorarId, fn ($query) => $query->whereKeyNot($ignorarId))
            ->when($bloquear, fn ($query) => $query->lockForUpdate())
            ->get()
            ->sum(fn (Recebimento $recebimento) => (int) round((float) $recebimento->valor * 100));
    }

    /**
     * Lançamento de recebimentos fora do PDV: somente vendas finalizadas.
     * (Aberta: lançamentos pelo PDV. Quitada: já não há saldo. Cancelada: não recebe.)
     */
    public function permiteLancarRecebimento(): bool
    {
        return $this->status === self::STATUS_FINALIZADA;
    }

    /**
     * Somente vendas canceladas podem ser excluídas.
     * (O cancelamento já cancelou os recebimentos pendentes e devolveu o estoque.)
     */
    public function podeSerExcluida(): bool
    {
        return $this->status === self::STATUS_CANCELADA;
    }

    /**
     * O estoque é baixado somente na finalização, na mesma transação que
     * muda a venda para "finalizada" ou "quitada". Portanto, esses status
     * indicam que a baixa já ocorreu; venda aberta nunca movimentou estoque.
     */
    public function estoqueFoiBaixado(): bool
    {
        return in_array($this->status, [
            self::STATUS_FINALIZADA,
            self::STATUS_QUITADA,
        ], true);
    }
}
