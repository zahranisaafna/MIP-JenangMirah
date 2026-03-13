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
        Schema::create('produksi', function (Blueprint $table) {
            $table->char('id_produksi', 8)->primary();
            $table->char('id_user', 5);
            // $table->string('kode_batch', 25)->unique();
            $table->date('tanggal_produksi');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai')->nullable();
            $table->enum('status', ['proses', 'selesai', 'gagal', 'pending'])->default('pending');
            $table->integer('total_produk_dihasilkan')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produksi');
    }
};
