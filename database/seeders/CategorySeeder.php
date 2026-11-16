<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nama_kategori' => 'Alam', 'deskripsi' => 'Destinasi wisata alam seperti gunung, pantai, danau, hutan, dan air terjun.'],
            ['nama_kategori' => 'Budaya', 'deskripsi' => 'Destinasi yang menampilkan kearifan lokal, seni tradisional, dan budaya masyarakat.'],
            ['nama_kategori' => 'Sejarah', 'deskripsi' => 'Situs bersejarah, candi, museum, dan bangunan peninggalan masa lalu.'],
            ['nama_kategori' => 'Kuliner', 'deskripsi' => 'Tempat kuliner khas daerah, pasar malam, dan sentra makanan tradisional.'],
            ['nama_kategori' => 'Hotel', 'deskripsi' => 'Akomodasi perhotelan mulai dari budget hingga bintang 5, villa, resort, dan homestay.'],
            ['nama_kategori' => 'Makanan', 'deskripsi' => 'Restoran, warung makan, kafe, dan tempat kuliner lainnya selain kuliner khas daerah.'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['nama_kategori' => $cat['nama_kategori']],
                $cat
            );
        }
    }
}