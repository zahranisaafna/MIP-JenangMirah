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
        Schema::create('detail_produksi', function (Blueprint $table) {
            $table->char('id_detail_produksi', 8)->primary();
            $table->char('id_produksi', 8);
            $table->char('id_resep', 5);
            $table->char('id_produk', 5);
            // $table->decimal('jumlah_target', 10, 2);
             $table->unsignedInteger('jumlah_target');
            // $table->decimal('jumlah_berhasil', 10, 2);
            $table->unsignedInteger('jumlah_berhasil');
            // $table->decimal('jumlah_gagal', 10, 2);
            $table->unsignedInteger('jumlah_gagal');
            $table->decimal('persentase_keberhasilan', 5, 2)->nullable();
            $table->text('keterangan_gagal')->nullable();
            $table->timestamps();

            $table->foreign('id_produksi')->references('id_produksi')->on('produksi')->onDelete('cascade');
            $table->foreign('id_resep')->references('id_resep')->on('resep')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_produksi');
    }
};
