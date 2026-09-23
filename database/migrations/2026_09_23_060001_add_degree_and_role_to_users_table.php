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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('id_degree')->nullable()->after('id_user')->constrained('degrees', 'id_degree')->nullOnDelete();
            $table->foreignId('id_role')->nullable()->after('id_degree')->constrained('roles', 'id')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_degree']);
            $table->dropForeign(['id_role']);
            $table->dropColumn(['id_degree', 'id_role']);
        });
    }
};
