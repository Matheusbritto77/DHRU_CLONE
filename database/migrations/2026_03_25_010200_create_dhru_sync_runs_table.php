<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dhru_sync_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dhru_provider_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('running');
            $table->unsignedInteger('services_seen')->default(0);
            $table->unsignedInteger('services_created')->default(0);
            $table->unsignedInteger('services_updated')->default(0);
            $table->unsignedInteger('services_removed')->default(0);
            $table->text('message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dhru_sync_runs');
    }
};
