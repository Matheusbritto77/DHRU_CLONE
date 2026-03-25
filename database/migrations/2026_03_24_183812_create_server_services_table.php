<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('server_services', function (Blueprint $table) {
            $table->id();
            $table->string('servicename');
            $table->string('serviceid')->index();
            $table->decimal('cost', 12, 2);
            $table->string('referenceid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('IMEI');
            $table->unsignedTinyInteger('status')->default(0)->index();
            $table->text('code')->nullable();
            $table->string('uuid')->nullable();
            $table->unsignedInteger('Qnt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_services');
    }
};
