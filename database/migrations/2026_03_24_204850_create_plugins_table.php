<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plugins', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('type');           // navbar, hero, section, footer, custom
            $table->string('version')->default('1.0.0');
            $table->string('author')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_system')->default(false);  // Proteção Kernel - não pode excluir
            $table->boolean('is_active')->default(true);
            $table->json('default_settings')->nullable();   // Schema JSON padrão dos campos editáveis
            $table->text('blade_template');                  // Blade inline do plugin
            $table->text('css')->nullable();
            $table->text('js')->nullable();
            $table->json('routes')->nullable();             // Rotas extras do plugin
            $table->json('middlewares')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plugins');
    }
};
