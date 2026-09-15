<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * codigo é salvo em MAIÚSCULA (normalizado no model) -- unique() aqui já
 * garante unicidade porque a comparação sempre passa pela mesma forma
 * normalizada, sem precisar de collation especial.
 *
 * minimo_compra é comparado ao subtotal ANTES do desconto (decisão do
 * projeto). uso_maximo nullable = sem limite de uso.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('tipo'); // percentual | fixo
            $table->decimal('valor', 8, 2);
            $table->dateTime('inicio_em')->nullable();
            $table->dateTime('fim_em')->nullable();
            $table->decimal('minimo_compra', 8, 2)->nullable();
            $table->unsignedInteger('uso_maximo')->nullable();
            $table->unsignedInteger('usos')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
