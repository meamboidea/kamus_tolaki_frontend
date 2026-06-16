<?php

namespace App\Enums;

/**
 * Siklus status koreksi (lihat DESAIN_LANGKAH_F.md §4).
 */
enum StatusKoreksi: string
{
    case Pending = 'pending';      // baru masuk, menunggu tinjauan
    case Approved = 'approved';    // disetujui → dipakai RAG
    case Rejected = 'rejected';    // ditolak
    case Superseded = 'superseded'; // digantikan koreksi yang lebih baik

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
            self::Superseded => 'Digantikan',
        };
    }
}
