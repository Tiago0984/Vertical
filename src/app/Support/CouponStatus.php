<?php

namespace App\Support;

use App\Models\Coupon;
use Illuminate\Support\Carbon;

/**
 * Mesmo padrão do OrderStatus/EstoqueStatus: rótulo e cor do estado do
 * cupom centralizados aqui, não recalculados em cada blade que lista cupom.
 */
class CouponStatus
{
    public static function label(Coupon $cupom): string
    {
        if (! $cupom->ativo) {
            return 'Inativo';
        }

        if ($cupom->uso_maximo !== null && $cupom->usos >= $cupom->uso_maximo) {
            return 'Esgotado';
        }

        $agora = Carbon::now();

        if ($cupom->inicio_em && $agora->lt($cupom->inicio_em)) {
            return 'Agendado';
        }

        if ($cupom->fim_em && $agora->gt($cupom->fim_em)) {
            return 'Expirado';
        }

        return 'Ativo';
    }

    public static function badgeClass(Coupon $cupom): string
    {
        return match (self::label($cupom)) {
            'Ativo' => 'text-bg-success',
            'Esgotado', 'Expirado' => 'text-bg-warning',
            default => 'text-bg-secondary', // Inativo, Agendado
        };
    }
}
