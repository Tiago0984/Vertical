<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('custo', 8, 2)->nullable()->after('preco');
            $table->unsignedInteger('estoque')->default(0)->after('preco_promocional');
            $table->unsignedInteger('estoque_minimo')->default(0)->after('estoque');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['custo', 'estoque', 'estoque_minimo']);
        });
    }
};
