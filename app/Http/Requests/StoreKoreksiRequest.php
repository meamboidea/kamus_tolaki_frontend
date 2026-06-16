<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreKoreksiRequest extends FormRequest
{
    /**
     * Endpoint kirim koreksi bersifat publik (penyumbang boleh anonim).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'teks_sumber' => ['required', 'string', 'max:255'],
            'tolaki_usulan' => ['required', 'string', 'max:255'],
            'konteks_kalimat' => ['nullable', 'string', 'max:1000'],
            'tolaki_sistem' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'penyumbang_id' => ['nullable', 'string', 'max:100'],
        ];
    }
}
