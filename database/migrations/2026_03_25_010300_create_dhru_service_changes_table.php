<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dhru_service_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dhru_provider_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dhru_catalog_service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('change_type', 20);
            $table->json('changed_fields')->nullable();
            $table->json('snapshot_before')->nullable();
            $table->json('snapshot_after')->nullable();
            $table->timestamp('detected_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dhru_service_changes');
    }
};
