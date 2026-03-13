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
        Schema::create('distribusi_detail', function (Blueprint $table) {
            $table->char('id_distribusi_detail', 8)->primary();
            $table->char('id_distribusi', 8);
            $table->char('id_lokasi', 5);
            $table->char('id_user', 5);
            $table->dateTime('tanggal_detail');
            $table->string('lokasi_tujuan', 20);
            $table->enum('status_detail', ['pending', 'diterima'])->default('pending');
            $table->string('nama_penerima', 50);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_distribusi')->references('id_distribusi')->on('distribusi')->onDelete('cascade');
            $table->foreign('id_lokasi')->references('id_lokasi')->on('lokasi')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribusi_detail');
    }
};
