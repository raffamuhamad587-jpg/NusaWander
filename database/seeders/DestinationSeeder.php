<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Destination;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        $alam = Category::where('nama_kategori', 'Alam')->first();
        $budaya = Category::where('nama_kategori', 'Budaya')->first();
        $sejarah = Category::where('nama_kategori', 'Sejarah')->first();
        $kuliner = Category::where('nama_kategori', 'Kuliner')->first();
        $rekreasi = Category::where('nama_kategori', 'Rekreasi')->first();

        $destinations = [
            [
                'category_id' => $sejarah?->id,
                'nama' => 'Candi Borobudur',
                'deskripsi' => 'Candi Buddha terbesar di dunia yang dibangun pada abad ke-8. Terkenal dengan arsitektur megah dan relief bersejarah.',
                'alamat' => 'Jl. Badrawati, Kw. Candi Borobudur, Kec. Borobudur, Kabupaten Magelang, Jawa Tengah',
                'kota' => 'Magelang',
                'latitude' => -7.60787380,
                'longitude' => 110.20375130,
                'jam_operasional' => '06:30 - 16:30 WIB',
                'estimasi_harga' => 50000.00,
                'jenis_harga' => 'per_orang',
                'status' => 'aktif',
            ],
            [
                'category_id' => $sejarah?->id,
                'nama' => 'Taman Sari Yogyakarta',
                'deskripsi' => 'Bekas taman dan pemandian kerajaan Keraton Yogyakarta dengan arsitektur kuno dan lorong bawah tanah Sumur Gumuling.',
                'alamat' => 'Patehan, Kecamatan Kraton, Kota Yogyakarta, DI Yogyakarta',
                'kota' => 'Yogyakarta',
                'latitude' => -7.81006700,
                'longitude' => 110.35931500,
                'jam_operasional' => '09:00 - 15:00 WIB',
                'estimasi_harga' => 15000.00,
                'jenis_harga' => 'per_orang',
                'status' => 'aktif',
            ],
            [
                'category_id' => $budaya?->id,
                'nama' => 'Keraton Ngayogyakarta Hadiningrat',
                'deskripsi' => 'Istana resmi Kesultanan Ngayogyakarta yang menyimpan koleksi pusaka, gamelan, dan pertunjukan seni tradisional.',
                'alamat' => 'Jl. Rotowijayan Blok No. 1, Panembahan, Kraton, Kota Yogyakarta',
                'kota' => 'Yogyakarta',
                'latitude' => -7.80528400,
                'longitude' => 110.36420300,
                'jam_operasional' => '08:30 - 14:30 WIB',
                'estimasi_harga' => 15000.00,
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
            [
                'category_id' => $rekreasi?->id,
                'nama' => 'HeHa Sky View',
                'deskripsi' => 'Spot panorama perbukitan dengan instalasi foto modern, pemandangan lampu kota Yogyakarta, dan area bersantai.',
                'alamat' => 'Jl. Dlingo-Patuk No.2, Patuk, Gunungkidul, DI Yogyakarta',
                'kota' => 'Yogyakarta',
                'latitude' => -7.84950100,
                'longitude' => 110.47835500,
                'jam_operasional' => '10:00 - 21:00 WIB',
                'estimasi_harga' => 25000.00,
                'jenis_harga' => 'per_orang',
                'status' => 'aktif',
            ],
            [
                'category_id' => $alam?->id,
                'nama' => 'Pantai Parangtritis',
                'deskripsi' => 'Pantai ikonik di selatan Yogyakarta dengan hamparan pasir vulkanik, gumuk pasir, dan pemandangan matahari terbenam.',
                'alamat' => 'Parangtritis, Kec. Kretek, Kabupaten Bantul, DI Yogyakarta',
                'kota' => 'Yogyakarta',
                'latitude' => -8.02521000,
                'longitude' => 110.33384000,
                'jam_operasional' => '24 Jam',
                'estimasi_harga' => 15000.00,
                'jenis_harga' => 'per_orang',
                'status' => 'aktif',
            ],
            [
                'category_id' => $alam?->id,
                'nama' => 'Pantai Kuta',
                'deskripsi' => 'Pantai pasir putih ikonik di Bali yang menawarkan pemandangan matahari terbenam memukau dan ombak favorit peselancar.',
                'alamat' => 'Kuta, Kabupaten Badung, Bali',
                'kota' => 'Badung',
                'latitude' => -8.71852440,
                'longitude' => 115.16858160,
                'jam_operasional' => '24 Jam',
                'estimasi_harga' => 10000.00,
                'jenis_harga' => 'per_kelompok',
                'status' => 'aktif',
            ],
            [
                'category_id' => $budaya?->id,
                'nama' => 'Desa Adat Penglipuran',
                'deskripsi' => 'Desa tradisional Bali yang terkenal dengan tata ruang rapi, kebersihan lingkungan, dan kelestarian arsitektur adat.',
                'alamat' => 'Jl. Penglipuran, Kubu, Kec. Bangli, Kabupaten Bangli, Bali',
                'kota' => 'Bangli',
                'latitude' => -8.42228400,
                'longitude' => 115.35969900,
                'jam_operasional' => '08:15 - 18:30 WITA',
                'estimasi_harga' => null, // Contoh komponen harga belum diisi Admin ("belum dihitung")
                'jenis_harga' => 'per_orang',
                'status' => 'aktif',
            ],
        ];

        foreach ($destinations as $destData) {
            if ($destData['category_id']) {
                Destination::firstOrCreate(
                    ['nama' => $destData['nama']],
                    $destData
                );
            }
        }
    }
}
