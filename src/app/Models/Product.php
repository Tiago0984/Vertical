<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['category_id', 'nome', 'slug', 'descricao', 'preco', 'custo', 'preco_promocional', 'estoque', 'estoque_minimo', 'imagem', 'is_novo', 'is_promocao'])]
class Product extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'preco' => 'decimal:2',
            'custo' => 'decimal:2',
            'preco_promocional' => 'decimal:2',
            'is_novo' => 'boolean',
            'is_promocao' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
