<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var string[]
     */
    private array $tables = [
        'users',
        'depositos',
        'dhru_catalog_services',
        'dhru_providers',
        'imei_services',
        'server_services',
        'imei_available_services',
        'imei_service_categories',
        'pages',
        'plugins',
        'currencies',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
        
        // Adicionar o campo is_active no User caso nao exista (para o toggle do usuario)
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'is_active')) {
             Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('id_admin');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'is_active')) {
             Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
