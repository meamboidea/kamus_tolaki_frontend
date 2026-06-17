<?php

namespace App\Models;

use App\Enums\StatusKoreksi;
use App\Exceptions\PenyumbangDiblokirException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $bahasa_sumber
 * @property string $teks_sumber
 * @property string $teks_sumber_norm
 * @property string|null $konteks_kalimat
 * @property string $tolaki_usulan
 * @property string|null $tolaki_sistem
 * @property string|null $catatan
 * @property StatusKoreksi $status
 * @property bool $utama
 * @property int $suara
 * @property string|null $penyumbang_id
 * @property string|null $ip_hash
 * @property int|null $ditinjau_oleh
 * @property string|null $alasan_tolak
 * @property Carbon|null $ditinjau_pada
 */
class Koreksi extends Model
{
    protected $table = 'koreksi';

    protected $fillable = [
        'bahasa_sumber',
        'teks_sumber',
        'teks_sumber_norm',
        'konteks_kalimat',
        'tolaki_usulan',
        'tolaki_sistem',
        'catatan',
        'status',
        'utama',
        'suara',
        'penyumbang_id',
        'ip_hash',
        'ditinjau_oleh',
        'ditinjau_pada',
        'alasan_tolak',
        'dicabut_oleh',
        'dicabut_pada',
        'alasan_cabut',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusKoreksi::class,
            'utama' => 'boolean',
            'ditinjau_pada' => 'datetime',
            'dicabut_pada' => 'datetime',
        ];
    }

    /** Normalisasi teks sumber untuk pencocokan RAG (lowercase + rapatkan spasi). */
    public static function normalisasi(string $teks): string
    {
        return Str::of($teks)->lower()->squish()->value();
    }

    /**
     * Ajukan koreksi (dipakai bersama API & web). Bila koreksi serupa
     * (teks ternormalisasi + usulan sama) sudah ada, tambah `suara`;
     * selain itu buat baru berstatus pending.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws PenyumbangDiblokirException bila penyumbang/IP sudah diblokir.
     */
    public static function ajukan(array $data): self
    {
        // Tolak penyumbang yang diblokir (anti spam). Cek via cookie-id ATAU hash IP.
        if (PenyumbangDiblokir::diblokir($data['penyumbang_id'] ?? null, $data['ip_hash'] ?? null)) {
            throw new PenyumbangDiblokirException();
        }

        $norm = self::normalisasi($data['teks_sumber']);

        $koreksi = self::query()
            ->where('teks_sumber_norm', $norm)
            ->where('tolaki_usulan', $data['tolaki_usulan'])
            ->whereIn('status', [StatusKoreksi::Pending->value, StatusKoreksi::Approved->value])
            ->first();

        if ($koreksi) {
            $koreksi->increment('suara');

            return $koreksi;
        }

        return self::create([
            ...$data,
            'teks_sumber_norm' => $norm,
            'status' => StatusKoreksi::Pending,
        ]);
    }

    /** Moderator yang meninjau (menyetujui/menolak) awal. */
    public function peninjau(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditinjau_oleh');
    }

    /** Admin yang mencabut koreksi (untuk audit; approver awal tetap di peninjau). */
    public function pencabut(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicabut_oleh');
    }

    public function scopeDicabut(Builder $query): Builder
    {
        return $query->where('status', StatusKoreksi::Superseded);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', StatusKoreksi::Pending);
    }

    public function scopeDisetujui(Builder $query): Builder
    {
        return $query->where('status', StatusKoreksi::Approved);
    }
}
