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
        Schema::create('bahan_baku', function (Blueprint $table) {
            $table->char('id_bahan_baku', 5)->primary();
            $table->string('nama_bahan', 20) ->unique();
            $table->string('kategori', 15);
            $table->string('satuan', 10);
            // $table->decimal('stok_minimum', 10, 2);
            $table->unsignedInteger('stok_minimum');
            // $table->decimal('stok_saat_ini', 10, 2);
            $table->unsignedInteger('stok_saat_ini');
            $table->decimal('harga_rata_rata', 12, 2);
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_baku');
    }
};
