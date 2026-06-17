<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Util identitas penyumbang koreksi (web & API).
 */
class Penyumbang
{
    /** Nama cookie identitas anonim. */
    public const COOKIE = 'penyumbang_id';

    /**
     * Hash IP dengan garam dari APP_KEY agar tidak mudah dibalik (privacy-safe)
     * namun tetap konsisten untuk pencocokan blokir.
     */
    public static function hashIp(?string $ip): ?string
    {
        if (! $ip) {
            return null;
        }

        return hash('sha256', $ip . '|' . config('app.key'));
    }

    /** Ambil penyumbang_id dari request (attribute middleware → cookie → body). */
    public static function id(Request $request): ?string
    {
        return $request->attributes->get('penyumbang_id')
            ?? $request->cookie(self::COOKIE)
            ?? $request->input('penyumbang_id');
    }

    /** Ambil hash IP dari request (attribute middleware → hitung dari IP). */
    public static function ipHash(Request $request): ?string
    {
        return $request->attributes->get('ip_hash')
            ?? self::hashIp($request->ip());
    }
}
