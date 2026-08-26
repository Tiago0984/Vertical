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
            Order::STATUS_CONFIRMADO => 'Confirmado',
            Order::STATUS_CANCELADO => 'Cancelado',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }
}
