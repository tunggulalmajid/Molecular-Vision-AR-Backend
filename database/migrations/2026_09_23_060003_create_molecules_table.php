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
        Schema::create('molecules', function (Blueprint $table) {
            $table->id('id_molecule');
            $table->string('name');
            $table->string('model_3d_url');
            $table->string('formula');
            $table->string('shape');
            $table->string('bent');
            $table->string('bond_type');
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('molecules');
    }
};
