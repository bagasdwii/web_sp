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
        {
            Schema::create('surat_peringatans', function (Blueprint $table) {
                $table->id('id_peringatan');
                $table->unsignedBigInteger('no');
                $table->integer('tingkat');
                $table->datetime('tanggal');
                $table->string('bukti_gambar');
                $table->string('scan_pdf');
                $table->unsignedBigInteger('id_account_officer');
                $table->timestamps();
    
                $table->foreign('no')->references('no')->on('nasabahs')->onDelete('cascade');
                $table->foreign('id_account_officer')->references('id_account_officer')->on('pegawai_account_offices')->onDelete('cascade');
            });
        }  
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_peringatans');
    }
};
