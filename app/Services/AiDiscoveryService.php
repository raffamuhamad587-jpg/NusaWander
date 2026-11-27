<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiDiscoveryService
{
    /**
     * Request temporary external destination alternatives via Gemini API (FR-13 & FR-14).
     * Never writes to the official destinations catalog table.
     *
     * @param  array{wilayah_tujuan: string, durasi_hari?: int, anggaran?: float|int|null, preferensi?: string|null, catatan?: string|null}  $params
     * @return array{mode: string, disclaimer: string, alternatives: array<int, array{nama: string, kategori: string, ringkasan: string, estimasi_kasar: string, sumber_nama: string, sumber_url: string|null}>}
     */
    public function discoverAlternatives(array $params): array
    {
        $wilayah = trim($params['wilayah_tujuan']);
        $durasi = (int) ($params['durasi_hari'] ?? 2);
        $anggaran = isset($params['anggaran']) && $params['anggaran'] !== null
            ? 'Rp ' . number_format((float) $params['anggaran'], 0, ',', '.')
            : 'Fleksibel';
        $preferensi = trim((string) ($params['preferensi'] ?? 'Wisata umum'));
        $catatan = trim((string) ($params['catatan'] ?? ''));

        $disclaimer = 'Hasil ini merupakan alternatif eksternal sementara dari layanan AI dan bukan bagian dari katalog resmi NUSA WANDER. Estimasi biaya bersifat perkiraan awal.';

        $apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.model', 'gemini-2.0-flash');

        if (empty($apiKey)) {
            return [
                'mode' => 'fallback',
                'disclaimer' => $disclaimer . ' (API key AI belum dikonfigurasi di server, menampilkan referensi pencarian eksternal terverifikasi.)',
                'alternatives' => $this->buildFallbackAlternatives($wilayah, $preferensi),
            ];
        }

        try {
            $prompt = "Berikan maksimal 4 alternatif destinasi wisata di Indonesia untuk wilayah \"{$wilayah}\", "
                . "durasi {$durasi} hari, anggaran {$anggaran}, preferensi: {$preferensi}. "
                . ($catatan !== '' ? "Catatan tambahan: {$catatan}. " : '')
                . 'Kembalikan HANYA JSON array valid tanpa markdown dengan struktur setiap item: '
                . '{"nama":"...","kategori":"...","ringkasan":"...","estimasi_kasar":"Rp ...","sumber_nama":"...","sumber_url":"https://..."}. '
                . 'Gunakan tautan sumber publik yang relevan (misal indonesia.travel, wikipedia.org, atau pencarian google maps).';

            $response = Http::timeout(15)
                ->acceptJson()
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.4,
                        'responseMimeType' => 'application/json',
                    ],
                ]);

            if (! $response->successful()) {
                Log::warning('AI discovery upstream returned non-2xx', ['status' => $response->status()]);

                return [
                    'mode' => 'fallback',
                    'disclaimer' => $disclaimer . ' (Layanan AI eksternal sedang tidak tersedia, menampilkan tautan referensi.)',
                    'alternatives' => $this->buildFallbackAlternatives($wilayah, $preferensi),
                ];
            }

            $rawText = data_get($response->json(), 'candidates.0.content.parts.0.text', '');
            $alternatives = $this->parseAndValidateAlternatives($rawText, $wilayah);

            if (empty($alternatives)) {
                return [
                    'mode' => 'fallback',
                    'disclaimer' => $disclaimer,
                    'alternatives' => $this->buildFallbackAlternatives($wilayah, $preferensi),
                ];
            }

            return [
                'mode' => 'ai',
                'disclaimer' => $disclaimer,
                'alternatives' => $alternatives,
            ];
        } catch (\Throwable $e) {
            Log::warning('AI discovery failed', ['error' => $e->getMessage()]);

            return [
                'mode' => 'fallback',
                'disclaimer' => $disclaimer . ' (Koneksi layanan AI terganggu, menampilkan tautan referensi.)',
                'alternatives' => $this->buildFallbackAlternatives($wilayah, $preferensi),
            ];
        }
    }

    /**
     * Defensively validate untrusted LLM JSON output (OWASP LLM05).
     */
    protected function parseAndValidateAlternatives(string $rawText, string $wilayah): array
    {
        $cleaned = trim($rawText);
        $cleaned = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $cleaned) ?? $cleaned;

        $decoded = json_decode($cleaned, true);
        if (! is_array($decoded)) {
            return [];
        }

        $items = array_is_list($decoded) ? $decoded : ($decoded['alternatives'] ?? []);
        if (! is_array($items)) {
            return [];
        }

        $validated = [];
        foreach (array_slice($items, 0, 5) as $item) {
            if (! is_array($item)) {
                continue;
            }

            $nama = trim(strip_tags((string) ($item['nama'] ?? '')));
            if ($nama === '') {
                continue;
            }

            $safeUrl = $this->sanitizeExternalUrl($item['sumber_url'] ?? null);
            if ($safeUrl === null) {
                $safeUrl = 'https://www.google.com/maps/search/' . rawurlencode($nama . ' ' . $wilayah);
            }

            $validated[] = [
                'nama' => mb_substr($nama, 0, 120),
                'kategori' => mb_substr(trim(strip_tags((string) ($item['kategori'] ?? 'Alternatif Wisata'))), 0, 60),
                'ringkasan' => mb_substr(trim(strip_tags((string) ($item['ringkasan'] ?? 'Rekomendasi alternatif eksternal.'))), 0, 300),
                'estimasi_kasar' => mb_substr(trim(strip_tags((string) ($item['estimasi_kasar'] ?? 'Belum diketahui'))), 0, 80),
                'sumber_nama' => mb_substr(trim(strip_tags((string) ($item['sumber_nama'] ?? 'Referensi Eksternal'))), 0, 80),
                'sumber_url' => $safeUrl,
            ];
        }

        return $validated;
    }

    protected function sanitizeExternalUrl(mixed $url): ?string
    {
        if (! is_string($url)) {
            return null;
        }

        $url = trim($url);
        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        if (! in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        return $url;
    }

    protected function buildFallbackAlternatives(string $wilayah, string $preferensi): array
    {
        $encodedArea = rawurlencode($wilayah);
        $encodedPref = rawurlencode($preferensi . ' ' . $wilayah);

        return [
            [
                'nama' => "Eksplorasi Wisata Populer {$wilayah}",
                'kategori' => 'Direktori Resmi',
                'ringkasan' => "Telusuri daftar rekomendasi wisata resmi Kemenparekraf untuk wilayah {$wilayah} sesuai minat {$preferensi}.",
                'estimasi_kasar' => 'Belum dihitung (cek sumber)',
                'sumber_nama' => 'Wonderful Indonesia',
                'sumber_url' => "https://www.indonesia.travel/id/id/search?q={$encodedArea}",
            ],
            [
                'nama' => "Peta Tempat Wisata & Kuliner {$wilayah}",
                'kategori' => 'Pencarian Peta',
                'ringkasan' => "Buka daftar titik lokasi wisata, jam operasional, dan kisaran biaya terbaru di sekitar {$wilayah}.",
                'estimasi_kasar' => 'Belum dihitung (cek sumber)',
                'sumber_nama' => 'Google Maps',
                'sumber_url' => "https://www.google.com/maps/search/wisata+{$encodedPref}",
            ],
            [
                'nama' => "Panduan Perjalanan {$wilayah}",
                'kategori' => 'Referensi Wisata',
                'ringkasan' => "Pelajari informasi wilayah, akses transportasi, dan daya tarik utama di {$wilayah} sebelum menambahkan ke itinerary.",
                'estimasi_kasar' => 'Belum dihitung (cek sumber)',
                'sumber_nama' => 'Wikivoyage Indonesia',
                'sumber_url' => "https://id.wikivoyage.org/w/index.php?search={$encodedArea}",
            ],
        ];
    }
}
