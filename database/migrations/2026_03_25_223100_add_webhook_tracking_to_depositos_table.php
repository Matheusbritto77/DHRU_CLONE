<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('depositos', function (Blueprint $table) {
            if (! Schema::hasColumn('depositos', 'webhook_token')) {
                $table->string('webhook_token')->nullable()->after('gateway_reference')->index();
            }

            if (! Schema::hasColumn('depositos', 'credited_at')) {
                $table->timestamp('credited_at')->nullable()->after('paid_at');
            }

            if (! Schema::hasColumn('depositos', 'credited_amount')) {
                $table->decimal('credited_amount', 10, 2)->nullable()->after('credited_at');
            }

            if (! Schema::hasColumn('depositos', 'payment_status_detail')) {
                $table->string('payment_status_detail')->nullable()->after('status');
            }

            if (! Schema::hasColumn('depositos', 'webhook_last_payload')) {
                $table->json('webhook_last_payload')->nullable()->after('gateway_payload');
            }
        });
    }

    public function down(): void
    {
        Schema::table('depositos', function (Blueprint $table) {
            $columns = collect([
                'webhook_token',
                'credited_at',
                'credited_amount',
                'payment_status_detail',
                'webhook_last_payload',
            ])->filter(fn (string $column): bool => Schema::hasColumn('depositos', $column))->all();

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
