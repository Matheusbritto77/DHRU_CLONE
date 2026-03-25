<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('depositos', function (Blueprint $table) {
            if (! Schema::hasColumn('depositos', 'gateway_slug')) {
                $table->string('gateway_slug')->nullable()->after('txid')->index();
            }

            if (! Schema::hasColumn('depositos', 'gateway_reference')) {
                $table->string('gateway_reference')->nullable()->after('gateway_slug')->index();
            }

            if (! Schema::hasColumn('depositos', 'gateway_payload')) {
                $table->json('gateway_payload')->nullable()->after('gateway_reference');
            }

            if (! Schema::hasColumn('depositos', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('depositos', function (Blueprint $table) {
            $columns = collect(['gateway_slug', 'gateway_reference', 'gateway_payload', 'paid_at'])
                ->filter(fn (string $column): bool => Schema::hasColumn('depositos', $column))
                ->all();

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
