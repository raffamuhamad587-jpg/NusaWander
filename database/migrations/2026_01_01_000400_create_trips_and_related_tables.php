<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_trip', 150);
            $table->string('wilayah_tujuan', 100);
            $table->date('tanggal_mulai');
            $table->unsignedTinyInteger('durasi_hari');
            $table->unsignedSmallInteger('jumlah_peserta')->default(1);
            $table->decimal('anggaran', 12, 2);
            $table->json('preferensi')->nullable();
            $table->decimal('total_estimasi', 12, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('itinerary_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->foreignId('destination_id')->constrained('destinations')->restrictOnDelete();
            $table->unsignedTinyInteger('hari_ke');
            $table->unsignedSmallInteger('urutan_kunjungan')->default(1);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('trip_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->foreignId('itinerary_item_id')->nullable()->constrained('itinerary_items')->cascadeOnDelete();
            $table->string('nama_biaya', 150);
            $table->enum('jenis_biaya', ['tiket', 'transportasi', 'penginapan', 'kuliner', 'lainnya'])->default('lainnya');
            $table->enum('dasar_perhitungan', ['per_orang', 'per_kelompok', 'per_hari', 'tetap'])->default('tetap');
            $table->unsignedInteger('jumlah')->default(1);
            $table->decimal('harga_satuan', 12, 2)->nullable();
            $table->decimal('subtotal', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_costs');
        Schema::dropIfExists('itinerary_items');
        Schema::dropIfExists('trips');
    }
};
