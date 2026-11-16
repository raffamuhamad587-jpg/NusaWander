<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->text('deskripsi');
            $table->text('alamat');
            $table->string('kota', 100);
            $table->string('provinsi', 100)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('tipe', 120)->default('Hotel');
            $table->unsignedTinyInteger('bintang')->default(3);
            $table->decimal('harga_min', 12, 2)->default(0.00);
            $table->decimal('harga_max', 12, 2)->default(0.00);
            $table->decimal('rating', 3, 1)->default(0.0);
            $table->unsignedInteger('jumlah_ulasan')->default(0);
            $table->string('image')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
