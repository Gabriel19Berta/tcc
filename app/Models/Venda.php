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
}
