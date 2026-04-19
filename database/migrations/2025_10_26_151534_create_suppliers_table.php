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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->char('id_supplier', 5)->primary();
            $table->char('id_bahan_baku', 5);
            $table->string('nama_supplier', 20)->unique();
            $table->text('alamat');
            $table->string('no_telepon', 15)->unique();
            $table->string('kontak_person', 20);
            $table->enum('status', ['aktif', 'non_aktif'])->default('aktif');
            $table->timestamps();

            $table->foreign('id_bahan_baku')
                  ->references('id_bahan_baku')
                  ->on('bahan_baku')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
