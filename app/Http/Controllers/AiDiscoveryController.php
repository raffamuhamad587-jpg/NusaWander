<?php

namespace App\Http\Controllers;

use App\Services\AiDiscoveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiDiscoveryController extends Controller
{
    public function __construct(
        protected AiDiscoveryService $aiDiscovery
    ) {}

    /**
     * Request temporary AI-assisted destination alternatives (FR-13 & FR-14).
     */
    public function suggest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'wilayah_tujuan' => ['required', 'string', 'max:100'],
            'durasi_hari' => ['nullable', 'integer', 'min:1', 'max:30'],
            'anggaran' => ['nullable', 'numeric', 'min:0'],
            'preferensi' => ['nullable', 'string', 'max:200'],
            'catatan' => ['nullable', 'string', 'max:300'],
        ], [
            'wilayah_tujuan.required' => 'Masukkan wilayah tujuan untuk mencari alternatif.',
        ]);

        $result = $this->aiDiscovery->discoverAlternatives($validated);

        return response()->json($result);
    }
}
