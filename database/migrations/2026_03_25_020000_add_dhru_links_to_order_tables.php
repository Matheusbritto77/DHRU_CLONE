<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('imei_services', function (Blueprint $table) {
            $table->foreignId('dhru_provider_id')->nullable()->after('user_id')->constrained('dhru_providers')->nullOnDelete();
            $table->foreignId('dhru_catalog_service_id')->nullable()->after('dhru_provider_id')->constrained('dhru_catalog_services')->nullOnDelete();
        });

        Schema::table('server_services', function (Blueprint $table) {
            $table->foreignId('dhru_provider_id')->nullable()->after('user_id')->constrained('dhru_providers')->nullOnDelete();
            $table->foreignId('dhru_catalog_service_id')->nullable()->after('dhru_provider_id')->constrained('dhru_catalog_services')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('server_services', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dhru_catalog_service_id');
            $table->dropConstrainedForeignId('dhru_provider_id');
        });

        Schema::table('imei_services', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dhru_catalog_service_id');
            $table->dropConstrainedForeignId('dhru_provider_id');
        });
    }
};
