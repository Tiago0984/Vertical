<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
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
    }
}
