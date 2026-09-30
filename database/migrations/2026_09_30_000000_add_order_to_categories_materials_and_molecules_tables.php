<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('categories', 'order')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->unsignedInteger('order')->default(1)->after('description')->index();
            });
        }

        if (! Schema::hasColumn('materials', 'order')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->unsignedInteger('order')->default(1)->after('id_category')->index();
            });
        }

        if (! Schema::hasColumn('molecules', 'order')) {
            Schema::table('molecules', function (Blueprint $table) {
                $table->unsignedInteger('order')->default(1)->after('name')->index();
            });
        }

        // Auto-assign urutan default berurutan (1, 2, 3...) untuk record data lama
        $this->populateInitialOrders();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('categories', 'order')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('order');
            });
        }

        if (Schema::hasColumn('materials', 'order')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->dropColumn('order');
            });
        }

        if (Schema::hasColumn('molecules', 'order')) {
            Schema::table('molecules', function (Blueprint $table) {
                $table->dropColumn('order');
            });
        }
    }

    /**
     * Berikan penomoran urutan bertingkat untuk data yang sudah ada di database.
     */
    protected function populateInitialOrders(): void
    {
        // 1. Categories
        $categories = DB::table('categories')->orderBy('id_category', 'asc')->get();
        $order = 1;
        foreach ($categories as $cat) {
            DB::table('categories')->where('id_category', $cat->id_category)->update(['order' => $order++]);
        }

        // 2. Materials (diurutkan per kategori)
        $materials = DB::table('materials')->orderBy('id_category', 'asc')->orderBy('id_material', 'asc')->get();
        $materialOrderPerCategory = [];
        foreach ($materials as $mat) {
            $catId = $mat->id_category;
            $materialOrderPerCategory[$catId] = ($materialOrderPerCategory[$catId] ?? 0) + 1;
            DB::table('materials')->where('id_material', $mat->id_material)->update(['order' => $materialOrderPerCategory[$catId]]);
        }

        // 3. Molecules
        $molecules = DB::table('molecules')->orderBy('id_molecule', 'asc')->get();
        $molOrder = 1;
        foreach ($molecules as $mol) {
            DB::table('molecules')->where('id_molecule', $mol->id_molecule)->update(['order' => $molOrder++]);
        }
    }
};
