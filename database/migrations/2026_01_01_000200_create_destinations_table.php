<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->string('nama', 150);
            $table->text('deskripsi');
            $table->text('alamat');
            $table->string('kota', 100);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('jam_operasional', 100)->nullable();
            $table->decimal('estimasi_harga', 12, 2)->nullable();
            $table->enum('jenis_harga', ['per_orang', 'per_kelompok'])->default('per_orang');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};
