<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'nome', 'sobrenome', 'cep', 'endereco', 'numero', 'complemento', 'bairro', 'cidade', 'estado', 'telefone', 'referencia', 'is_padrao'])]
class Address extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_padrao' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
