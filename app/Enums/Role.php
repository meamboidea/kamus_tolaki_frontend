<?php

namespace App\Enums;

/**
 * Peran pengguna staf (login ke panel).
 * Penyumbang koreksi dari aplikasi TIDAK punya akun → tidak diwakili di sini.
 */
enum Role: string
{
    case Admin = 'admin';
    case Moderator = 'moderator';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Moderator => 'Moderator',
        };
    }
}
