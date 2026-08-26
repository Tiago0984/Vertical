<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Campos coletados hoje no checkout (Identificação/Endereço) e que
     * fazem sentido guardar no perfil do cliente pra compras futuras.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('sobrenome')->nullable()->after('name');
            $table->string('telefone', 20)->nullable()->after('email');
            $table->date('data_nascimento')->nullable()->after('telefone');
            $table->boolean('newsletter_email')->default(true)->after('data_nascimento');
            $table->boolean('newsletter_sms')->default(false)->after('newsletter_email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['sobrenome', 'telefone', 'data_nascimento', 'newsletter_email', 'newsletter_sms']);
        });
    }
};
