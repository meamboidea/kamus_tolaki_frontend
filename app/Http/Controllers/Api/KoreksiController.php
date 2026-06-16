<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKoreksiRequest;
use App\Models\Koreksi;
use Illuminate\Http\JsonResponse;

class KoreksiController extends Controller
{
    /**
     * Kirim koreksi dari aplikasi (publik, anonim). Masuk sebagai `pending`.
     * Bila koreksi serupa (teks + usulan sama) sudah ada, tambah `suara`.
     */
    public function store(StoreKoreksiRequest $request): JsonResponse
    {
        $koreksi = Koreksi::ajukan($request->validated());

        return response()->json([
            'id' => $koreksi->id,
            'status' => $koreksi->status->value,
            'pesan' => 'Terima kasih, koreksi dikirim untuk ditinjau.',
        ], 201);
    }

    /**
     * Daftar koreksi yang DISETUJUI — dibaca mesin terjemah (FastAPI) untuk
     * disuntikkan ke RAG. Hanya field yang diperlukan.
     */
    public function disetujui(): JsonResponse
    {
        $koreksi = Koreksi::query()
            ->disetujui()
            ->orderByDesc('utama')
            ->get(['teks_sumber', 'teks_sumber_norm', 'tolaki_usulan', 'utama', 'catatan'])
            ->map(fn (Koreksi $k) => [
                'indonesia' => $k->teks_sumber,
                'kunci' => $k->teks_sumber_norm,
                'tolaki' => $k->tolaki_usulan,
                'utama' => $k->utama,
                'catatan' => $k->catatan,
            ]);

        return response()->json(['data' => $koreksi]);
    }
}
