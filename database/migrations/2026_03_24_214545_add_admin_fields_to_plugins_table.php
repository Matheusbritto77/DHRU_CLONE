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
        Schema::table('plugins', function (Blueprint $table) {
            $table->boolean('has_admin_panel')->default(false)->after('is_active');
            $table->string('admin_panel_location')->nullable()->after('has_admin_panel');
            $table->string('admin_navigation_group')->nullable()->after('admin_panel_location');
            $table->string('admin_navigation_label')->nullable()->after('admin_navigation_group');
            $table->string('admin_navigation_icon')->nullable()->after('admin_navigation_label');
            $table->string('admin_page_title')->nullable()->after('admin_navigation_icon');
            $table->json('admin_form_schema')->nullable()->after('admin_page_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plugins', function (Blueprint $table) {
            $table->dropColumn([
                'has_admin_panel',
                'admin_panel_location',
                'admin_navigation_group',
                'admin_navigation_label',
                'admin_navigation_icon',
                'admin_page_title',
                'admin_form_schema',
            ]);
        });
    }
};
