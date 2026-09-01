<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 'numero_pedido', 'nome', 'sobrenome', 'email', 'telefone',
    'subtotal', 'frete', 'total', 'cupom', 'forma_pagamento', 'status',
    'endereco_entrega', 'endereco_faturamento',
])]
class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDENTE = 'pendente_pagamento';
    public const STATUS_PAGO = 'pago';
    public const STATUS_CONFIRMADO = 'confirmado';
    public const STATUS_CANCELADO = 'cancelado';

    public const PAGAMENTO_CARTAO = 'cartao';
    public const PAGAMENTO_PIX = 'pix';
    public const PAGAMENTO_BOLETO = 'boleto';

    public const STATUSES = [
        self::STATUS_PENDENTE,
        self::STATUS_PAGO,
        self::STATUS_CONFIRMADO,
        self::STATUS_CANCELADO,
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'frete' => 'decimal:2',
            'total' => 'decimal:2',
            'endereco_entrega' => 'array',
            'endereco_faturamento' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
