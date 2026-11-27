<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\ItineraryItem;
use App\Models\Trip;
use App\Models\TripCost;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $wisatawan = User::updateOrCreate(
            ['email' => 'ravi@nusawander.test'],
            [
                'name' => 'Ravi Patel',
                'password' => Hash::make('password'),
                'role' => 'wisatawan',
                'email_verified_at' => now(),
            ]
        );

        $tamanSari = Destination::where('nama', 'Taman Sari Yogyakarta')->first();
        $gudeg = Destination::where('nama', 'Gudeg Yu Djum Wijilan')->first();
        $heha = Destination::where('nama', 'HeHa Sky View')->first();

        if (! $tamanSari || ! $gudeg || ! $heha) {
            return;
        }

        if (Trip::where('user_id', $wisatawan->id)->exists()) {
            return;
        }

        $trip = Trip::create([
            'user_id' => $wisatawan->id,
            'nama_trip' => 'Jelajah Yogyakarta 2 Hari',
            'wilayah_tujuan' => 'Yogyakarta',
            'tanggal_mulai' => now()->addDays(7)->toDateString(),
            'durasi_hari' => 2,
            'jumlah_peserta' => 2,
            'anggaran' => 800000.00,
            'preferensi' => [
                'kategori_ids' => array_values(array_unique([$tamanSari->category_id, $gudeg->category_id, $heha->category_id])),
                'prioritas_biaya' => 'seimbang',
            ],
            'total_estimasi' => 450000.00,
        ]);

        $item1 = ItineraryItem::create([
            'trip_id' => $trip->id,
            'destination_id' => $tamanSari->id,
            'hari_ke' => 1,
            'urutan_kunjungan' => 1,
            'catatan' => 'Berangkat pagi agar tidak terlalu panas.',
        ]);

        $item2 = ItineraryItem::create([
            'trip_id' => $trip->id,
            'destination_id' => $gudeg->id,
            'hari_ke' => 1,
            'urutan_kunjungan' => 2,
            'catatan' => 'Makan siang kuliner khas Yogyakarta.',
        ]);

        $item3 = ItineraryItem::create([
            'trip_id' => $trip->id,
            'destination_id' => $heha->id,
            'hari_ke' => 2,
            'urutan_kunjungan' => 1,
            'catatan' => 'Datang sore untuk menikmati sunset.',
        ]);

        TripCost::create([
            'trip_id' => $trip->id,
            'itinerary_item_id' => $item1->id,
            'nama_biaya' => 'Tiket ' . $tamanSari->nama,
            'jenis_biaya' => 'tiket',
            'dasar_perhitungan' => 'per_orang',
            'jumlah' => 2,
            'harga_satuan' => 15000.00,
            'subtotal' => 30000.00,
        ]);

        TripCost::create([
            'trip_id' => $trip->id,
            'itinerary_item_id' => $item2->id,
            'nama_biaya' => 'Estimasi ' . $gudeg->nama,
            'jenis_biaya' => 'kuliner',
            'dasar_perhitungan' => 'per_orang',
            'jumlah' => 2,
            'harga_satuan' => 35000.00,
            'subtotal' => 70000.00,
        ]);

        TripCost::create([
            'trip_id' => $trip->id,
            'itinerary_item_id' => $item3->id,
            'nama_biaya' => 'Tiket ' . $heha->nama,
            'jenis_biaya' => 'tiket',
            'dasar_perhitungan' => 'per_orang',
            'jumlah' => 2,
            'harga_satuan' => 25000.00,
            'subtotal' => 50000.00,
        ]);

        TripCost::create([
            'trip_id' => $trip->id,
            'itinerary_item_id' => null,
            'nama_biaya' => 'Transportasi Lokal (2 Hari)',
            'jenis_biaya' => 'transportasi',
            'dasar_perhitungan' => 'per_hari',
            'jumlah' => 2,
            'harga_satuan' => 150000.00,
            'subtotal' => 300000.00,
        ]);
    }
}
