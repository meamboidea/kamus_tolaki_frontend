<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Daftar penyumbang koreksi yang diblokir (anti spam/asal-asalan).
 * Pemblokiran TERPISAH dari penolakan koreksi — keputusan admin yang disengaja.
 *
 * @property int $id
 * @property string|null $penyumbang_id
 * @property string|null $ip_hash
 * @property string|null $alasan
 * @property int|null $diblokir_oleh
 */
class PenyumbangDiblokir extends Model
{
    protected $table = 'penyumbang_diblokir';

    protected $fillable = [
        'penyumbang_id',
        'ip_hash',
        'alasan',
        'diblokir_oleh',
    ];

    /** Apakah penyumbang ini diblokir? Cocokkan via penyumbang_id ATAU ip_hash. */
    public static function diblokir(?string $penyumbangId, ?string $ipHash): bool
    {
        if (! $penyumbangId && ! $ipHash) {
            return false;
        }

        return self::query()
            ->when($penyumbangId, fn ($q) => $q->orWhere('penyumbang_id', $penyumbangId))
            ->when($ipHash, fn ($q) => $q->orWhere('ip_hash', $ipHash))
            ->exists();
    }

    public function pemblokir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diblokir_oleh');
    }
}
