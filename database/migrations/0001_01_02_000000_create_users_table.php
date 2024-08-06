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
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->string('api_token')->unique()->nullable()->default(null);
        $table->unsignedBigInteger('key')->nullable();
        $table->foreignId('status')->nullable();
        $table->foreignId('jabatan_id');
        $table->foreignId('id_cabang')->nullable();
        $table->foreignId('id_wilayah')->nullable();


        $table->rememberToken();
        $table->timestamps();
        $table->foreign('status')->references('id')->on('statuses')->onDelete('cascade');
        $table->foreign('id_cabang')->references('id_cabang')->on('cabangs')->onDelete('cascade');
        $table->foreign('id_wilayah')->references('id_wilayah')->on('wilayahs')->onDelete('cascade');
        $table->foreign('key')->references('key')->on('keys')->onDelete('cascade');

    });

    Schema::create('sessions', function (Blueprint $table) {
        $table->string('id')->primary();
        $table->foreignId('user_id')->nullable()->index();
        $table->string('ip_address', 45)->nullable();
        $table->text('user_agent')->nullable();
        $table->longText('payload');
        $table->integer('last_activity')->index();
    });
}

public function down(): void
{
    Schema::dropIfExists('users');
    Schema::dropIfExists('sessions');
}
};
