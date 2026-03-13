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
        Schema::create('lokasi', function (Blueprint $table) {
            $table->char('id_lokasi', 5)->primary();
            $table->string('nama_lokasi', 50);
            $table->enum('jenis_lokasi', ['gudang', 'toko']);
            $table->text('alamat')->nullable();
            // $table->decimal('kapasitas', 10, 2);
            $table->unsignedInteger('kapasitas');
            $table->string('satuan_kapasitas', 10);
            $table->string('penanggung_jawab', 100);
            $table->string('no_telepon', 15)->nullable();
            $table->enum('status', ['aktif', 'non_aktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lokasi');
    }
};
