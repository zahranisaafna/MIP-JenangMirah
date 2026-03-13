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
        Schema::create('produk', function (Blueprint $table) {
            $table->char('id_produk', 5)->primary();
            $table->char('id_resep', 5);
            $table->string('nama_produk', 20);
            $table->char('kode_produk', 6)->unique();
            $table->string('kategori', 15);
            $table->decimal('harga_jual', 12, 2);
            $table->string('satuan', 3);
            // $table->decimal('stok_tersedia', 10, 2);
            $table->unsignedInteger('stok_tersedia');
            // $table->decimal('stok_minimum', 10, 2);
            $table->unsignedInteger('stok_minimum');
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['tersedia', 'habis'])->default('tersedia');
            $table->timestamps();

            $table->foreign('id_resep')->references('id_resep')->on('resep')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
