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
        Schema::create('resep', function (Blueprint $table) {
            $table->char('id_resep', 5)->primary();
            $table->string('nama_resep', 20);
            $table->integer('waktu_produksi'); // dalam menit
            // $table->decimal('kapasitas_produksi', 10, 2);
            $table->unsignedInteger('kapasitas_produksi');
            $table->string('satuan_output', 5);
            $table->enum('status', ['aktif', 'non_aktif'])->default('aktif');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resep');
    }
};
