<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TripPlannerTest extends TestCase
{
    use RefreshDatabase;

    public function test_tourist_can_generate_day_by_day_itinerary_and_budget_summary(): void
    {
        $user = User::factory()->create(['role' => 'wisatawan']);
        $sejarah = Category::create(['nama_kategori' => 'Sejarah', 'deskripsi' => 'Sejarah']);
        $kuliner = Category::create(['nama_kategori' => 'Kuliner', 'deskripsi' => 'Kuliner']);

        Destination::create([
            'category_id' => $sejarah->id,
            'nama' => 'Taman Sari',
            'deskripsi' => 'Situs kerajaan',
            'alamat' => 'Kraton, Yogyakarta',
            'kota' => 'Yogyakarta',
            'estimasi_harga' => 15000,
            'jenis_harga' => 'per_orang',
            'status' => 'aktif',
        ]);

        Destination::create([
            'category_id' => $kuliner->id,
            'nama' => 'Gudeg Wijilan',
            'deskripsi' => 'Kuliner khas',
            'alamat' => 'Wijilan, Yogyakarta',
            'kota' => 'Yogyakarta',
            'estimasi_harga' => null, // Belum dihitung
            'jenis_harga' => 'per_orang',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($user)->post('/trips', [
            'nama_trip' => 'Liburan Jogja 2 Hari',
            'wilayah_tujuan' => 'Yogyakarta',
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'durasi_hari' => 2,
            'jumlah_peserta' => 3,
            'anggaran' => 500000,
            'kategori_ids' => [$sejarah->id, $kuliner->id],
            'prioritas_biaya' => 'seimbang',
        ]);

        $trip = Trip::firstOrFail();
        $response->assertRedirect(route('trips.show', $trip->id));

        // 2 destinations distributed across Day 1 and Day 2
        $this->assertCount(2, $trip->itineraryItems);
        $this->assertEquals([1, 2], $trip->itineraryItems->pluck('hari_ke')->sort()->values()->all());

        // Taman Sari = 15000 * 3 orang = 45000; Gudeg = null (belum dihitung, not 0)
        $this->assertEquals('45000.00', $trip->total_estimasi);
        $this->assertEquals(455000.0, $trip->sisa_anggaran);
        $this->assertDatabaseHas('trip_costs', [
            'trip_id' => $trip->id,
            'subtotal' => null,
        ]);
    }

    public function test_itinerary_item_day_cannot_exceed_trip_duration(): void
    {
        $user = User::factory()->create(['role' => 'wisatawan']);
        $cat = Category::create(['nama_kategori' => 'Alam', 'deskripsi' => 'Alam']);
        $dest = Destination::create([
            'category_id' => $cat->id,
            'nama' => 'Pantai Kuta',
            'deskripsi' => 'Pantai Bali',
            'alamat' => 'Badung, Bali',
            'kota' => 'Badung',
            'estimasi_harga' => 10000,
            'jenis_harga' => 'per_kelompok',
            'status' => 'aktif',
        ]);

        $trip = Trip::create([
            'user_id' => $user->id,
            'nama_trip' => 'Trip Bali',
            'wilayah_tujuan' => 'Badung',
            'tanggal_mulai' => now()->addDays(2)->toDateString(),
            'durasi_hari' => 2,
            'jumlah_peserta' => 2,
            'anggaran' => 300000,
            'total_estimasi' => 0,
        ]);

        $response = $this->actingAs($user)->post("/trips/{$trip->id}/items", [
            'destination_id' => $dest->id,
            'hari_ke' => 3, // invalid (> durasi_hari 2)
        ]);

        $response->assertSessionHasErrors('hari_ke');
    }

    public function test_tourist_cannot_view_or_modify_another_tourists_trip(): void
    {
        $owner = User::factory()->create(['role' => 'wisatawan']);
        $other = User::factory()->create(['role' => 'wisatawan']);

        $trip = Trip::create([
            'user_id' => $owner->id,
            'nama_trip' => 'Trip Pribadi',
            'wilayah_tujuan' => 'Yogyakarta',
            'tanggal_mulai' => now()->addDays(3)->toDateString(),
            'durasi_hari' => 2,
            'jumlah_peserta' => 1,
            'anggaran' => 200000,
            'total_estimasi' => 0,
        ]);

        $this->actingAs($other)->get("/trips/{$trip->id}")->assertForbidden();
        $this->actingAs($other)->delete("/trips/{$trip->id}")->assertForbidden();
    }

    public function test_ai_discovery_returns_ephemeral_alternatives_without_mutating_catalog(): void
    {
        $user = User::factory()->create(['role' => 'wisatawan']);
        config(['services.gemini.api_key' => 'test-key']);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        [
                                            'nama' => 'Bukit Pengilon',
                                            'kategori' => 'Alam',
                                            'ringkasan' => 'Perbukitan hijau menghadap laut selatan.',
                                            'estimasi_kasar' => 'Rp 10.000',
                                            'sumber_nama' => 'Wonderful Indonesia',
                                            'sumber_url' => 'https://www.indonesia.travel/id/id/home',
                                        ],
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $initialCount = Destination::count();

        $response = $this->actingAs($user)->postJson('/ai/alternatives', [
            'wilayah_tujuan' => 'Gunungkidul',
            'durasi_hari' => 2,
            'anggaran' => 400000,
            'preferensi' => 'Alam',
        ]);

        $response->assertOk()
            ->assertJsonPath('mode', 'ai')
            ->assertJsonPath('alternatives.0.nama', 'Bukit Pengilon')
            ->assertJsonPath('alternatives.0.sumber_url', 'https://www.indonesia.travel/id/id/home');

        // Business Rule 5.3 / FR-14: AI output must NEVER auto-insert into destinations catalog
        $this->assertSame($initialCount, Destination::count());
    }
}
