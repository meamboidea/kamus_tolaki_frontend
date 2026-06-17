<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Dilempar saat penyumbang yang sudah diblokir mencoba mengirim koreksi.
 */
class PenyumbangDiblokirException extends RuntimeException
{
    public function __construct(string $message = 'Penyumbang ini telah diblokir dan tidak dapat mengirim koreksi.')
    {
        parent::__construct($message);
    }
}
