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
        Schema::table('imei_service_categories', function (Blueprint $table) {
            $table->string('type')->default('IMEI')->after('is_active');
        });

        // Hydrate type from services
        \Illuminate\Support\Facades\DB::table('imei_service_categories')
            ->whereIn('id', function($q) {
                $q->select('category_id')->from('imei_available_services')->where('service_type', 'SERVER');
            })
            ->update(['type' => 'SERVER']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('imei_service_categories', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
