<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

#[Fillable([
    'codigo', 'tipo', 'valor', 'inicio_em', 'fim_em',
    'minimo_compra', 'uso_maximo', 'usos', 'ativo',
])]
class Coupon extends Model
{
    public const TIPO_PERCENTUAL = 'percentual';
    public const TIPO_FIXO = 'fixo';

    public const TIPOS = [
        self::TIPO_PERCENTUAL,
        self::TIPO_FIXO,
    ];

    /**
     * Chave usada para cachear "existe algum cupom utilizável agora?" (ver
     * scopeUtilizavelAgora() e CheckoutController::checkout()) -- mesma
     * constante nos dois lugares pra não desalinhar grafia entre quem grava
     * e quem invalida o cache.
     */
    public const CACHE_KEY_UTILIZAVEL_AGORA = 'coupons.utilizavel_agora';

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'minimo_compra' => 'decimal:2',
            'inicio_em' => 'datetime',
            'fim_em' => 'datetime',
            'uso_maximo' => 'integer',
            'usos' => 'integer',
            'ativo' => 'boolean',
        ];
    }

    /**
     * codigo sempre em MAIÚSCULA -- salvo e comparado sempre na mesma
     * forma, sem depender de collation case-insensitive do banco.
     */
    protected static function booted(): void
    {
        static::saving(function (Coupon $coupon) {
            if ($coupon->codigo !== null) {
                $coupon->codigo = Str::upper(trim($coupon->codigo));
            }
        });

        // Invalidação no MODEL, não no controller: assim uma alteração via
        // tinker/seeder também limpa o cache, não só a rota do admin.
        static::saved(fn () => Cache::forget(self::CACHE_KEY_UTILIZAVEL_AGORA));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY_UTILIZAVEL_AGORA));
    }

    /**
     * Cupom "utilizável agora": ativo, dentro da janela de validade e com
     * uso disponível. NÃO checa minimo_compra -- essa checagem "existe
     * cupom em tese" não conhece o subtotal de nenhum carrinho; a decisão
     * completa por carrinho específico continua sendo CupomValidador.
     */
    public function scopeUtilizavelAgora(Builder $query): Builder
    {
        $agora = Carbon::now();

        return $query->where('ativo', true)
            ->where(fn ($q) => $q->whereNull('inicio_em')->orWhere('inicio_em', '<=', $agora))
            ->where(fn ($q) => $q->whereNull('fim_em')->orWhere('fim_em', '>=', $agora))
            ->where(fn ($q) => $q->whereNull('uso_maximo')->orWhereColumn('usos', '<', 'uso_maximo'));
    }
}
