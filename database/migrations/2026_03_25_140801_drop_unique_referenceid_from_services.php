<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Dropping unique constraint on referenceid because VITRINE services multiple entries reuse this field
        Schema::table('server_services', function (Blueprint $table) {
            $table->dropUnique('server_services_referenceid_unique');
            $table->index('referenceid'); // Keep as index but not unique
        });

        Schema::table('imei_services', function (Blueprint $table) {
            $table->dropUnique('imei_services_referenceid_unique');
            $table->index('referenceid'); // Keep as index but not unique
        });
    }

    public function down(): void
    {
        Schema::table('server_services', function (Blueprint $table) {
            $table->dropIndex(['referenceid']);
            $table->unique('referenceid', 'server_services_referenceid_unique');
        });

        Schema::table('imei_services', function (Blueprint $table) {
            $table->dropIndex(['referenceid']);
            $table->unique('referenceid', 'imei_services_referenceid_unique');
        });
    }
};
