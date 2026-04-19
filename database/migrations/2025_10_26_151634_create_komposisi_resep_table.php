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
        Schema::create('komposisi_resep', function (Blueprint $table) {
            $table->char('id_komposisi', 5)->primary();
            $table->char('id_resep', 5);
            $table->char('id_bahan_baku', 5);
            // $table->decimal('jumlah_diperlukan', 10, 2);
            $table->decimal('jumlah_diperlukan', 10, 2);
            $table->string('satuan', 5);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_resep')->references('id_resep')->on('resep')->onDelete('cascade');
            $table->foreign('id_bahan_baku')->references('id_bahan_baku')->on('bahan_baku')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komposisi_resep');
    }
};
