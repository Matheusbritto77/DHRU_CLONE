<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imei_service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('imei_available_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('imei_service_categories')->cascadeOnDelete();
            $table->foreignId('dhru_catalog_service_id')->constrained('dhru_catalog_services')->cascadeOnDelete();
            $table->string('custom_name')->nullable();
            $table->text('custom_description')->nullable();
            $table->decimal('selling_price', 12, 4);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['category_id', 'dhru_catalog_service_id'], 'cat_service_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imei_available_services');
        Schema::dropIfExists('imei_service_categories');
    }
};
