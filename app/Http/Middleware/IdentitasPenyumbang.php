<?php

namespace App\Http\Middleware;

use App\Support\Penyumbang;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memberi setiap pengunjung web identitas penyumbang anonim yang stabil:
 * UUID disimpan di cookie jangka panjang (analog device-id Flutter), plus
 * hash IP sebagai sinyal pendukung. Dipakai untuk atribusi & fitur blokir.
 */
class IdentitasPenyumbang
{
    /** Umur cookie identitas: 2 tahun (menit). */
    private const UMUR = 60 * 24 * 365 * 2;

    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->cookie(Penyumbang::COOKIE);

        if (! $id) {
            $id = (string) Str::uuid();
            // Antrekan ke response; sekaligus pakai di request ini lewat attribute.
            Cookie::queue(Penyumbang::COOKIE, $id, self::UMUR);
        }

        $request->attributes->set('penyumbang_id', $id);
        $request->attributes->set('ip_hash', Penyumbang::hashIp($request->ip()));

        return $next($request);
    }
}
