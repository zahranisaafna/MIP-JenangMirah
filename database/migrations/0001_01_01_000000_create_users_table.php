<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->char('id_user', 5)->primary();
            $table->string('nama_user', 50);
            $table->string('username', 8)->unique();
            $table->string('password', 255);
            $table->enum('role', ['admin', 'karyawanproduksi', 'owner']);
            $table->string('email', 200)->nullable();
            $table->string('no_telepon', 15);
            $table->enum('status', ['aktif', 'non_aktif'])->default('aktif');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};