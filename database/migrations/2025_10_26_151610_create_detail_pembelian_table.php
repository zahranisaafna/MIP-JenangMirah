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
        Schema::create('detail_pembelian', function (Blueprint $table) {
            $table->char('id_detail_pembelian', 8)->primary();
            $table->char('id_pembelian', 7);
            $table->char('id_bahan_baku', 5);
            $table->char('id_supplier', 5);
            // $table->decimal('jumlah', 10, 2);
            $table->unsignedInteger('jumlah');
            $table->decimal('harga_satuan', 12, 2);
            $table->decimal('subtotal', 15, 2);
            $table->date('tanggal_diterima');
            $table->enum('kondisi', ['baik', 'rusak', 'kadaluarsa'])->default('baik');
            $table->timestamps();

            $table->foreign('id_pembelian')->references('id_pembelian')->on('pembelian')->onDelete('cascade');
            $table->foreign('id_bahan_baku')->references('id_bahan_baku')->on('bahan_baku')->onDelete('cascade');
            $table->foreign('id_supplier')->references('id_supplier')->on('suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pembelian');
    }
};
