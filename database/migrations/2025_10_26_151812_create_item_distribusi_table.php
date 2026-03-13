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
        Schema::create('item_distribusi', function (Blueprint $table) {
            $table->char('id_item_distribusi', 8)->primary();
            $table->char('id_distribusi_detail', 8);
            // $table->char('id_bahan_baku', 5)->nullable();
            $table->char('id_produk', 5)->nullable();
            // $table->enum('jenis_item', ['bahan_baku', 'produk_jadi']);
            // $table->decimal('jumlah', 10, 2);
            $table->unsignedInteger('jumlah');
            $table->string('satuan', 5);
            $table->enum('kondisi', ['baik', 'rusak', 'kadaluarsa'])->default('baik');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_distribusi_detail')->references('id_distribusi_detail')->on('distribusi_detail')->onDelete('cascade');
            // $table->foreign('id_bahan_baku')->references('id_bahan_baku')->on('bahan_baku')->onDelete('set null');
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_distribusi');
    }
};
