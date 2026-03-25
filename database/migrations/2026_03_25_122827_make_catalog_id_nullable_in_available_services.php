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
        Schema::table('imei_available_services', function (Blueprint $table) {
            $table->unsignedBigInteger('dhru_catalog_service_id')->nullable()->change();
            $table->string('service_type')->default('IMEI')->after('is_active');
        });

        // Hydrate existing records
        \Illuminate\Support\Facades\DB::table('imei_available_services')
            ->join('dhru_catalog_services', 'imei_available_services.dhru_catalog_service_id', '=', 'dhru_catalog_services.id')
            ->update(['imei_available_services.service_type' => \Illuminate\Support\Facades\DB::raw('dhru_catalog_services.group_type')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('imei_available_services', function (Blueprint $table) {
            $table->unsignedBigInteger('dhru_catalog_service_id')->nullable(false)->change();
            $table->dropColumn('service_type');
        });
    }
};
