<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Klien ke mesin terjemah FastAPI (terjemah RAG & telusur kamus).
 */
class TerjemahService
{
    private string $base;

    public function __construct()
    {
        $this->base = rtrim((string) config('services.fastapi.url'), '/');
    }

    /**
     * Terjemahkan kalimat Indonesia → Tolaki. Kembalikan payload hasil.
     *
     * @return array<string, mixed>
     */
    public function terjemah(string $kalimat, string $arah = 'id-tolaki'): array
    {
        return Http::acceptJson()
            ->timeout(120) // LLM bisa lama
            ->post("{$this->base}/terjemah", ['kalimat' => $kalimat, 'arah' => $arah])
            ->throw()
            ->json();
    }

    /**
     * Telusur kamus. bidang: 'auto' | 'tolaki' (Tolaki→Indonesia) | 'id' (Indonesia→Tolaki).
     *
     * @return array<int, array<string, mixed>>
     */
    public function cari(string $q, string $bidang = 'auto', int $batas = 50): array
    {
        return Http::acceptJson()
            ->timeout(15)
            ->get("{$this->base}/cari", ['q' => $q, 'batas' => $batas, 'bidang' => $bidang])
            ->throw()
            ->json('hasil') ?? [];
    }
}
