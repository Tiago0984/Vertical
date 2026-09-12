<?php

namespace App\Support;

use App\Models\Order;

class OrderStatus
{
    public static function label(string $status): string
    {
        return match ($status) {
            Order::STATUS_PENDENTE => 'Pagamento Pendente',
            Order::STATUS_PAGO => 'Pago',
            Order::STATUS_CANCELADO => 'Cancelado',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    public static function badgeClass(string $status): string
    {
        return match ($status) {
            Order::STATUS_PENDENTE => 'text-bg-warning',
            Order::STATUS_PAGO => 'text-bg-success',
            Order::STATUS_CANCELADO => 'text-bg-danger',
            default => 'text-bg-secondary',
        };
    }
}
