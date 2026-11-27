<?php

namespace App\Services;

use App\Models\Destination;
use App\Models\ItineraryItem;
use App\Models\Trip;
use App\Models\TripCost;

class BudgetCalculatorService
{
    /**
     * Calculate subtotal for a cost line.
     * Returns null when unit price is unknown ("belum dihitung"), never treating it as 0 / free.
     */
    public function calculateSubtotal(?float $hargaSatuan, int $jumlah): ?float
    {
        if ($hargaSatuan === null) {
            return null;
        }

        return round($hargaSatuan * max(1, $jumlah), 2);
    }

    /**
     * Resolve default quantity based on calculation basis and trip parameters.
     */
    public function resolveDefaultQuantity(string $dasarPerhitungan, Trip $trip, ?int $requestedQuantity = null): int
    {
        if ($requestedQuantity !== null && $requestedQuantity > 0) {
            return $requestedQuantity;
        }

        return match ($dasarPerhitungan) {
            'per_orang' => max(1, (int) $trip->jumlah_peserta),
            'per_hari' => max(1, (int) $trip->durasi_hari),
            default => 1,
        };
    }

    /**
     * Create or update the TripCost row associated with an ItineraryItem.
     */
    public function syncItemCost(Trip $trip, ItineraryItem $item, Destination $destination): TripCost
    {
        $dasar = $destination->jenis_harga === 'per_kelompok' ? 'per_kelompok' : 'per_orang';
        $jumlah = $dasar === 'per_orang' ? max(1, (int) $trip->jumlah_peserta) : 1;
        $hargaSatuan = $destination->estimasi_harga !== null ? (float) $destination->estimasi_harga : null;
        $subtotal = $this->calculateSubtotal($hargaSatuan, $jumlah);

        $categoryName = strtolower($destination->category?->nama_kategori ?? '');
        $jenisBiaya = str_contains($categoryName, 'kuliner') ? 'kuliner' : 'tiket';

        return TripCost::updateOrCreate(
            [
                'trip_id' => $trip->id,
                'itinerary_item_id' => $item->id,
            ],
            [
                'nama_biaya' => ($jenisBiaya === 'kuliner' ? 'Estimasi ' : 'Tiket ') . $destination->nama,
                'jenis_biaya' => $jenisBiaya,
                'dasar_perhitungan' => $dasar,
                'jumlah' => $jumlah,
                'harga_satuan' => $hargaSatuan,
                'subtotal' => $subtotal,
            ]
        );
    }

    /**
     * Recalculate per-person / per-day costs when trip participants or duration change,
     * then refresh trip.total_estimasi.
     */
    public function syncCostsWithTripParameters(Trip $trip): Trip
    {
        $costs = $trip->costs()->get();

        foreach ($costs as $cost) {
            $newJumlah = match ($cost->dasar_perhitungan) {
                'per_orang' => max(1, (int) $trip->jumlah_peserta),
                'per_hari' => max(1, (int) $trip->durasi_hari),
                default => max(1, (int) $cost->jumlah),
            };

            $hargaSatuan = $cost->harga_satuan !== null ? (float) $cost->harga_satuan : null;

            $cost->update([
                'jumlah' => $newJumlah,
                'subtotal' => $this->calculateSubtotal($hargaSatuan, $newJumlah),
            ]);
        }

        return $this->recalculateTrip($trip);
    }

    /**
     * Sum only known (non-null) subtotals and update trip.total_estimasi.
     */
    public function recalculateTrip(Trip $trip): Trip
    {
        $total = (float) $trip->costs()
            ->whereNotNull('subtotal')
            ->sum('subtotal');

        $trip->update([
            'total_estimasi' => round($total, 2),
        ]);

        return $trip->fresh();
    }

    /**
     * Build budget summary metrics including unknown ("belum dihitung") component count.
     */
    public function summarize(Trip $trip): array
    {
        $costs = $trip->relationLoaded('costs') ? $trip->costs : $trip->costs()->get();
        $totalEstimasi = round((float) $costs->whereNotNull('subtotal')->sum('subtotal'), 2);
        $anggaran = round((float) $trip->anggaran, 2);
        $sisaAnggaran = round($anggaran - $totalEstimasi, 2);
        $belumDihitungCount = $costs->whereNull('subtotal')->count();

        return [
            'anggaran' => $anggaran,
            'total_estimasi' => $totalEstimasi,
            'sisa_anggaran' => $sisaAnggaran,
            'is_over_budget' => $sisaAnggaran < 0,
            'belum_dihitung_count' => $belumDihitungCount,
        ];
    }
}
