<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Review;
use App\Models\Trip;
use App\Models\ItineraryItem;
use App\Models\TripCost;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat user wisatawan demo
        $wisatawan = User::factory()->create([
            'name' => 'Ravi Patel',
            'email' => 'ravi@nusawander.test',
            'role' => 'wisatawan',
        ]);

        // 2. Sample destinations (ambil id dari DestinationSeeder)
        $borobudur = Destination::where('nama', 'Candi Borobudur')->first();
        $pantaiKuta = Destination::where('nama', 'Pantai Kuta')->first();
        $gudegYuDjum = Destination::where('nama', 'Gudeg Yu Djum Wijilan')->first();

        if (!$borobudur || !$pantaiKuta || !$gudegYuDjum) {
            $this->command->warn('Some destinations not found for seeding reviews/trips.');
            return;
        }

        // 3. Ulasan dari wisatawan (satu ulasan per destinasi)
        Review::create([
            'user_id' => $wisatawan->id,
            'destination_id' => $borobudur->id,
            'rating' => 5,
            'komentar' => 'Wah candi ini spektakuler! Arsitekturnya megah dan detail reliefnya luar biasa. Sangat disarankan datang pagi hari agar ramai wisatawan sedikit.',
        ]);

        Review::create([
            'user_id' => $wisatawan->id,
            'destination_id' => $pantaiKuta->id,
            'rating' => 4,
            'komentar' => 'Pantai yang indah, airnya jernih dan pasir putih. Harga tiket masuk terjangkau. Hati-hati jika datang di liburan liburan karena padat orang.',
        ]);

        Review::create([
            'user_id' => $wisatawan->id,
            'destination_id' => $gudegYuDjum->id,
            'rating' => 3,
            'komentar' => 'Gudegnya enak tapi cukup manis menurut saya. Pesannnya harus tepat waktu karena stoklah habitualmente cepat habis pagi.',
        ]);

        // 4. Buat trip pertama dipublikasikan
        $trip1 = Trip::create([
            'user_id' => $wisatawan->id,
            'destination_id' => $borobudur->id,
            'judul' => 'Keajaiban Sejarah: Borobudur Sunrise',
            'deskripsi' => 'Menghadiah matahari terbit di candi Borobudur, pengalaman spiritual tak terlupakan.',
            'status' => 'published',
            'total_estimasi_harga' => 150000.00,
        ]);

        // Itinerary items for trip 1
        ItineraryItem::create([
            'trip_id' => $trip1->id,
            'destination_id' => $borobudur->id,
            'urutan' => 1,
            'judul' => 'Candi Borobudur',
            'catatan' => 'Masuk dini hari untuk sunrise',
            'estimasi_harga' => 50000.00,
        ]);

        ItineraryItem::create([
            'trip_id' => $trip1->id,
            'destination_id' => $pantaiKuta->id,
            'urutan' => 2,
            'judul' => 'Pantai Kuta, Bali',
            'catatan' => 'Lihat matahari terbenam dari Batu Meja',
            'estimasi_harga' => 100000.00,
        ]);

        TripCost::create([
            'trip_id' => $trip1->id,
            'harga_per_orang' => 250000.00,
            'harga_per_kelompok' => 1500000.00,
            'keterangan' => 'Transport dan akomodasi 2 hari 1 malam',
        ]);

        // 5. Buat trip kedua sebagai draf
        $trip2 = Trip::create([
            'user_id' => $wisatawan->id,
            'destination_id' => $gudegYuDjum->id,
            'judul' => 'Kenikmatan Kuliner: Yogyakarta',
            'deskripsi' => 'Menikmati kuliner legendaris Yogyakarta',
            'status' => 'draft',
            'total_estimasi_harga' => 85000.00,
        ]);

        ItineraryItem::create([
            'trip_id' => $trip2->id,
            'destination_id' => $gudegYuDjum->id,
            'urutan' => 1,
            'judul' => 'Gudeg Yu Djum Wijilan',
            'catatan' => 'Coba gudeg manis pedas',
            'estimasi_harga' => 35000.00,
        ]);

        TripCost::create([
            'trip_id' => $trip2->id,
            'harga_per_orang' => 50000.00,
            'harga_per_kelompok' => 300000.00,
            'keterangan' => 'Biaya makan dan transport lokal',
        ]);

        // 6. Buat trip ketiga dipublikasikan dengan destinasi lain
        $trip3 = Trip::create([
            'user_id' => $wisatawan->id,
            'destination_id' => $pantaiKuta->id,
            'judul' => 'Liburan Di Pantai Kuta',
            'deskripsi' => 'Liburan akhir tahun di pantai ikonik Bali',
            'status' => 'published',
            'total_estimasi_harga' => 300000.00,
        ]);

        ItineraryItem::create([
            'trip_id' => $trip3->id,
            'destination_id' => $pantaiKuta->id,
            'urutan' => 1,
            'judul' => 'Pantai Kuta',
            'catatan' => 'Aktivitas: surfing & sunset hunt',
            'estimasi_harga' => 100000.00,
        ]);

        TripCost::create([
            'trip_id' => $trip3->id,
            'harga_per_orang' => 150000.00,
            'harga_per_kelompok' => 900000.00,
            'keterangan' => 'Akomodasi 3 malam di resort beachfront',
        ]);

        echo "Demo content seeded successfully.\n";
    }
}