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
        Schema::create('cabang_wilayahs', function (Blueprint $table) {
            $table->unsignedBigInteger('id_cabang');
            $table->unsignedBigInteger('id_wilayah');
            $table->foreign('id_cabang')->references('id_cabang')->on('cabangs')->onDelete('cascade');
            $table->foreign('id_wilayah')->references('id_wilayah')->on('wilayahs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cabang_wilayahs');
    }
};
