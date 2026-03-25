<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('country_code', 2)->nullable()->after('phone');
            $table->string('state_region')->nullable()->after('country_code');
            $table->string('city')->nullable()->after('state_region');
            $table->json('registration_meta')->nullable()->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'country_code',
                'state_region',
                'city',
                'registration_meta',
            ]);
        });
    }
};
