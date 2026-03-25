<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dhru_catalog_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dhru_provider_id')->constrained()->cascadeOnDelete();
            $table->string('group_name');
            $table->string('group_type', 20);
            $table->string('service_type', 20);
            $table->unsignedBigInteger('external_service_id');
            $table->string('service_name');
            $table->string('time_text')->nullable();
            $table->decimal('cost', 12, 4)->default(0);
            $table->unsignedInteger('min_qnt')->default(0);
            $table->unsignedInteger('max_qnt')->default(0);
            $table->string('custom_name')->nullable();
            $table->json('custom_fields')->nullable();
            $table->json('raw_payload')->nullable();
            $table->string('fingerprint', 64);
            $table->boolean('is_active')->default(true);
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique(['dhru_provider_id', 'external_service_id'], 'dhru_services_provider_external_unique');
            $table->index(['group_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dhru_catalog_services');
    }
};
