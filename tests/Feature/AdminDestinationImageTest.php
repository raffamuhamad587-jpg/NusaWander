<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Destination;
use App\Models\DestinationImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDestinationImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_destination_and_manage_image_order_and_caption(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cat = Category::create(['nama_kategori' => 'Sejarah', 'deskripsi' => 'Sejarah']);

        $dest = Destination::create([
            'category_id' => $cat->id,
            'nama' => 'Candi Prambanan',
            'deskripsi' => 'Kompleks candi Hindu',
            'alamat' => 'Sleman, DIY',
            'kota' => 'Sleman',
            'estimasi_harga' => 50000,
            'jenis_harga' => 'per_orang',
            'status' => 'aktif',
        ]);

        $image = DestinationImage::create([
            'destination_id' => $dest->id,
            'url_gambar' => '/storage/destinations/sample.jpg',
            'keterangan' => 'Tampak depan',
            'urutan_tampil' => 2,
        ]);

        // Update destination price (FR-12)
        $this->actingAs($admin)->put("/admin/destinations/{$dest->id}", [
            'category_id' => $cat->id,
            'nama' => 'Candi Prambanan',
            'deskripsi' => 'Kompleks candi Hindu terbesar di Indonesia',
            'alamat' => 'Sleman, DIY',
            'kota' => 'Sleman',
            'estimasi_harga' => 60000,
            'jenis_harga' => 'per_orang',
            'status' => 'aktif',
        ])->assertRedirect(route('admin.destinations.index'));

        $this->assertDatabaseHas('destinations', [
            'id' => $dest->id,
            'estimasi_harga' => 60000,
        ]);

        // Update image order (was throwing 500 due to missing Request import)
        $this->actingAs($admin)->patch("/admin/destination-images/{$image->id}/order", [
            'urutan_tampil' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('destination_images', [
            'id' => $image->id,
            'urutan_tampil' => 1,
        ]);

        // Update image caption (FR-11)
        $this->actingAs($admin)->patch("/admin/destination-images/{$image->id}", [
            'keterangan' => 'Candi Siwa sore hari',
        ])->assertRedirect();

        $this->assertDatabaseHas('destination_images', [
            'id' => $image->id,
            'keterangan' => 'Candi Siwa sore hari',
        ]);
    }
}
