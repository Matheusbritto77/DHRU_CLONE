<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();           // ex: welcome, login, register
            $table->string('title');
            $table->string('route_name')->nullable();   // ex: home, login
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();           // SEO: title, description, keywords
            $table->json('middlewares')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
