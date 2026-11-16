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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('destination_id')->constrained('destinations')->onDelete('cascade');
            $table->string('judul', 150);
            $table->text('deskripsi')->nullable();
            $table->decimal('total_estimasi_harga', 12, 2)->default(0.00);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamps();
        });

        Schema::create('itinerary_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
            $table->foreignId('destination_id')->constrained('destinations')->onDelete('restrict');
            $table->integer('urutan')->default(1);
            $table->string('judul', 150);
            $table->text('catatan')->nullable();
            $table->decimal('estimasi_harga', 12, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('trip_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
            $table->decimal('harga_per_orang', 12, 2)->default(0.00);
            $table->decimal('harga_per_kelompok', 12, 2)->default(0.00);
            $table->string('keterangan', 255)->nullable();
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