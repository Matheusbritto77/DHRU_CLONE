<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique()->comment('Ex: USD, BRL, EUR');
            $table->string('name')->nullable();
            $table->string('symbol', 10)->nullable();
            $table->decimal('exchange_rate', 16, 8)->default(1.0)->comment('Valor em relacao a moeda base');
            $table->boolean('is_base')->default(false)->comment('Se esta e a moeda de registro dos servicos');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
