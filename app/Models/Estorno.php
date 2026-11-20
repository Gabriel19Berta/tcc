<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Estorno de um recebimento: valor total devolvido (soma dos pagamentos),
 * quando e por quem. Os pagamentos originais permanecem registrados.
 */
class Estorno extends Model
{
    protected $fillable = [
        'recebimento_id',
        'user_id',
        'valor',
        'data_estorno',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'data_estorno' => 'datetime',
        ];
    }

    public function recebimento(): BelongsTo
    {
        return $this->belongsTo(Recebimento::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Pagamentos desfeitos por este estorno.
     */
    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class);
    }
}
