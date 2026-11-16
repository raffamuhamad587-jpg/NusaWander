<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Destination;
use App\Models\DestinationImage;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        $alam = Category::where('nama_kategori', 'Alam')->first();
        $budaya = Category::where('nama_kategori', 'Budaya')->first();
        $sejarah = Category::where('nama_kategori', 'Sejarah')->first();
        $kuliner = Category::where('nama_kategori', 'Kuliner')->first();

        $destinations = [
            [
                'category_id' => $sejarah?->id,
                'nama' => 'Candi Borobudur',
                'deskripsi' => 'Candi Buddha terbesar di dunia yang dibangun pada abad ke-8. Terkenal dengan arsitektur megah dan relief bersejarah.',
                'alamat' => 'Jl. Badrawati, Kw. Candi Borobudur, Borobudur, Kec. Borobudur, Kabupaten Magelang, Jawa Tengah',
                'kota' => 'Magelang',
                'latitude' => -7.60787380,
                'longitude' => 110.20375130,
                'jam_operasional' => '06:30 - 16:30 WIB',
                'estimasi_harga' => 50000.00,
                'jenis_harga' => 'per_orang',
                'status' => 'aktif',
            ],
            [
                'category_id' => $alam?->id,
                'nama' => 'Pantai Kuta',
                'deskripsi' => 'Pantai pasir putih ikonik di Bali yang menawarkan pemandangan matahari terbenam memukau dan tempat berselancar favorit.',
                'alamat' => 'Kuta, Kabupaten Badung, Bali',
                'kota' => 'Badung',
                'latitude' => -8.71852440,
                'longitude' => 115.16858160,
                'jam_operasional' => '24 Jam',
                'estimasi_harga' => 10000.00,
                'jenis_harga' => 'per_orang',
                'status' => 'aktif',
            ],
            [
                'category_id' => $kuliner?->id,
                'nama' => 'Gudeg Yu Djum Wijilan',
                'deskripsi' => 'Sentra kuliner gudeg legendaris Yogyakarta dengan resep tradisional manis gurih yang otentik.',
                'alamat' => 'Jl. Wijilan No.167, Panembahan, Kec. Kraton, Kota Yogyakarta',
                'kota' => 'Yogyakarta',
                'latitude' => -7.80491000,
                'longitude' => 110.36442000,
                'jam_operasional' => '06:00 - 22:00 WIB',
                'estimasi_harga' => 35000.00,
                'jenis_harga' => 'per_orang',
                'status' => 'aktif',
            ],
        ];

        foreach ($destinations as $destData) {
            if ($destData['category_id']) {
                Destination::create($destData);
            }
        }
    }
}
