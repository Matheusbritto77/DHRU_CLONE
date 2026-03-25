<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Re-ordenar Categorias
        $categories = DB::table('imei_service_categories')->orderBy('id')->get();
        $order = 1;
        foreach ($categories as $cat) {
            DB::table('imei_service_categories')->where('id', $cat->id)->update(['sort_order' => $order++]);
        }

        // 2. Re-ordenar Servicos dentro de cada Categoria
        $cats_with_services = DB::table('imei_available_services')->select('category_id')->distinct()->get();
        foreach ($cats_with_services as $cs) {
            $services = DB::table('imei_available_services')
                ->where('category_id', $cs->category_id)
                ->orderBy('id')
                ->get();
            $order_s = 1;
            foreach ($services as $s) {
                DB::table('imei_available_services')->where('id', $s->id)->update(['sort_order' => $order_s++]);
            }
        }
    }

    public function down(): void
    {
        // No action needed for down.
    }
};
