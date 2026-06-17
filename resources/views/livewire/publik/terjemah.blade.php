<?php

use App\Exceptions\PenyumbangDiblokirException;
use App\Models\Koreksi;
use App\Services\TerjemahService;
use App\Support\Penyumbang;

use function Livewire\Volt\{state};

state([
    'kalimat' => '',
    'arah' => 'id-tolaki', // 'id-tolaki' | 'tolaki-id'
    'hasil' => null,
    'memuat' => false,
    'galat' => null,
    'perbaikiTeks' => null,
    'perbaikiSistem' => null,
    'tolakiBenar' => '',
    'catatan' => '',
]);

$balik = function (): void {
    $this->arah = $this->arah === 'id-tolaki' ? 'tolaki-id' : 'id-tolaki';
    // Pindahkan hasil sebelumnya ke input agar mudah menerjemahkan balik.
    if ($this->hasil && ! empty($this->hasil['terjemahan'])) {
        $this->kalimat = $this->hasil['terjemahan'];
    }
    $this->hasil = null;
    $this->galat = null;
    $this->perbaikiTeks = null;
};

$terjemah = function (): void {
    $this->kalimat = trim($this->kalimat);
    $this->perbaikiTeks = null;
    if ($this->kalimat === '') {
        return;
    }
    $this->memuat = true;
    $this->galat = null;
    $this->hasil = null;
    try {
        $this->hasil = app(TerjemahService::class)->terjemah($this->kalimat, $this->arah);
    } catch (\Throwable $e) {
        $this->galat = 'Gagal menerjemahkan. Pastikan layanan terjemah (FastAPI) aktif.';
    }
    $this->memuat = false;
};

$mulaiPerbaiki = function (string $teks, ?string $sistem): void {
    $this->perbaikiTeks = $teks;
    $this->perbaikiSistem = $sistem;
    $this->tolakiBenar = $sistem ?? '';
    $this->catatan = '';
};

$batalPerbaiki = function (): void {
    $this->perbaikiTeks = null;
};

$kirimKoreksi = function (): void {
    if ($this->perbaikiTeks === null || trim($this->tolakiBenar) === '') {
        return;
    }
    try {
        Koreksi::ajukan([
            'teks_sumber' => $this->perbaikiTeks,
            'tolaki_usulan' => trim($this->tolakiBenar),
            'tolaki_sistem' => $this->perbaikiSistem,
            'konteks_kalimat' => $this->kalimat,
            'catatan' => trim($this->catatan) ?: null,
            'penyumbang_id' => Penyumbang::id(request()),
            'ip_hash' => Penyumbang::ipHash(request()),
        ]);
    } catch (PenyumbangDiblokirException $e) {
        // Tampilkan pesan netral; jangan beri tahu sebab teknis pemblokiran.
        $this->perbaikiTeks = null;
        session()->flash('galat', 'Koreksi tidak dapat dikirim. Hubungi admin bila ini keliru.');

        return;
    }
    $this->perbaikiTeks = null;
    session()->flash('ok', 'Terima kasih, koreksi terkirim untuk ditinjau.');
};

?>

@php
    $maju = $arah === 'id-tolaki';
    $labelSumber = $maju ? 'Indonesia' : 'Tolaki';
    $labelTujuan = $maju ? 'Tolaki' : 'Indonesia';
@endphp

<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <h1 class="font-serif text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white">Uji Terjemahan</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Terjemahkan kalimat sehari-hari atau teks panjang ke dalam Bahasa Tolaki.</p>
        </div>
        
        <!-- Quick Switch Bar -->
        <div class="flex items-center gap-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-1.5 shadow-sm text-sm self-start sm:self-auto">
            <span class="px-3 py-1 font-medium text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-lg">{{ $labelSumber }}</span>
            <button wire:click="balik" type="button" class="p-2 rounded-lg text-brand-700 dark:text-brand-400 hover:bg-brand-50 dark:hover:bg-slate-850 transition-all duration-200 cursor-pointer" title="Tukar Bahasa">
                <svg class="h-4 w-4 transform transition-transform duration-300 hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
            </button>
            <span class="px-3 py-1 font-medium text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-lg">{{ $labelTujuan }}</span>
        </div>
    </div>

    @if (session('ok'))
        <div class="rounded-xl bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/30 px-4 py-3.5 text-sm text-emerald-800 dark:text-emerald-300 flex items-center gap-2 shadow-sm animate-pulse">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">{{ session('ok') }}</span>
        </div>
    @endif

    @if (session('galat'))
        <div class="rounded-xl bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/30 px-4 py-3.5 text-sm text-rose-700 dark:text-rose-400 flex items-center gap-2 shadow-sm">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="font-medium">{{ session('galat') }}</span>
        </div>
    @endif

    <!-- Translation Dashboard Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Input Panel -->
        <div class="rounded-2xl bg-white dark:bg-slate-900 shadow-sm border border-brand-100/30 dark:border-slate-800/80 flex flex-col min-h-[200px]">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 dark:border-slate-800/50">
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ $labelSumber }}</span>
                @if($kalimat)
                    <button type="button" wire:click="$set('kalimat', '')" class="text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">Bersihkan</button>
                @endif
            </div>
            <div class="p-5 flex-1 flex flex-col">
                <form wire:submit="terjemah" class="flex-1 flex flex-col justify-between gap-4">
                    <textarea wire:model="kalimat" rows="4" placeholder="Masukkan kalimat/kata dalam bahasa {{ $labelSumber }}..."
                        class="w-full flex-1 resize-none border-none bg-transparent p-0 focus:ring-0 focus:outline-none text-slate-800 dark:text-slate-100 text-lg placeholder-slate-400 min-h-[120px]"></textarea>
                    
                    <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-800/50 pt-3 mt-2">
                        <span class="text-xs text-slate-400 dark:text-slate-500">{{ strlen($kalimat) }} karakter</span>
                        <button type="submit" wire:loading.attr="disabled"
                            class="rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-800 dark:bg-brand-600 dark:hover:bg-brand-700 shadow-md shadow-brand-700/10 dark:shadow-none transition-all duration-150 disabled:opacity-60 cursor-pointer flex items-center gap-2">
                            <span wire:loading.remove wire:target="terjemah">Terjemahkan</span>
                            <span wire:loading wire:target="terjemah" class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                            <span wire:loading wire:target="terjemah">Memproses...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Output Panel -->
        <div class="rounded-2xl bg-white dark:bg-slate-900 shadow-sm border border-brand-100/30 dark:border-slate-800/80 flex flex-col min-h-[200px] relative overflow-hidden">
            <!-- Background Accent Deco (Rattan tone corner glow) -->
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-brand-500/5 dark:bg-brand-600/5 rounded-full filter blur-xl"></div>
            
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 dark:border-slate-800/50">
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ $labelTujuan }}</span>
                @if ($hasil && !empty($hasil['terjemahan']))
                    <button onclick="navigator.clipboard.writeText('{{ addslashes($hasil['terjemahan']) }}'); alert('Terjemahan berhasil disalin!');" type="button" class="text-xs text-brand-700 hover:text-brand-800 dark:text-brand-400 flex items-center gap-1 cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                        Salin
                    </button>
                @endif
            </div>
            
            <div class="p-5 flex-1 flex flex-col justify-between bg-brand-50/10 dark:bg-slate-900/50">
                <div class="flex-1 flex flex-col justify-center">
                    @if ($memuat)
                        <div class="space-y-2.5 animate-pulse w-full">
                            <div class="h-5 bg-slate-200 dark:bg-slate-800 rounded w-3/4"></div>
                            <div class="h-5 bg-slate-200 dark:bg-slate-800 rounded w-1/2"></div>
                        </div>
                    @elseif ($hasil)
                        <div class="text-xl font-bold text-slate-900 dark:text-white leading-relaxed break-words">
                            {{ $hasil['terjemahan'] ?? '—' }}
                        </div>
                    @else
                        <div class="text-slate-400 dark:text-slate-500 italic text-base">
                            Terjemahan akan muncul di sini...
                        </div>
                    @endif
                </div>

                @if ($hasil && $maju && !$memuat)
                    <div class="border-t border-slate-100 dark:border-slate-800/50 pt-3 mt-4 flex justify-end">
                        <button wire:click="mulaiPerbaiki(@js($hasil['input'] ?? $kalimat), @js($hasil['terjemahan'] ?? null))"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-700 hover:text-brand-800 dark:text-brand-400 hover:underline cursor-pointer">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Ajukan Koreksi
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($galat)
        <div class="rounded-xl bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/30 px-4 py-3.5 text-sm text-rose-700 dark:text-rose-400 flex items-center gap-2">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ $galat }}</span>
        </div>
    @endif

    <!-- Extra Insights (Only shown when results are available) -->
    @if ($hasil && !$memuat)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Jembatan Makna -->
            @if (! empty($hasil['jembatan']))
                <div class="rounded-2xl bg-white dark:bg-slate-900 p-5 shadow-sm border border-brand-100/30 dark:border-slate-800/80 flex flex-col gap-2">
                    <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-brand-700 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Verifikasi Makna (Jembatan {{ $labelSumber }})
                    </div>
                    <div class="text-slate-700 dark:text-slate-300 text-sm leading-relaxed">{{ $hasil['jembatan'] ?? '' }}</div>
                </div>
            @endif

            <!-- Alternatif translations -->
            @if (! empty($hasil['alternatif']))
                <div class="rounded-2xl bg-white dark:bg-slate-900 p-5 shadow-sm border border-brand-100/30 dark:border-slate-800/80 flex flex-col gap-3">
                    <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Alternatif Terjemahan</div>
                    <ul class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        @foreach ($hasil['alternatif'] as $alt)
                            <li class="flex items-start justify-between gap-3 py-2.5 first:pt-0 last:pb-0">
                                <div class="min-w-0">
                                    <span class="font-medium text-slate-850 dark:text-slate-200 text-sm">{{ $alt['teks'] ?? '' }}</span>
                                    @if (! empty($alt['ragam']))
                                        <span class="ml-2 rounded-full bg-brand-50 text-brand-800 border border-brand-100/30 dark:bg-brand-950/40 dark:text-brand-300 dark:border-none px-2 py-0.5 text-2xs font-medium">{{ $alt['ragam'] }}</span>
                                    @endif
                                    @if (! empty($alt['catatan']))
                                        <div class="text-2xs text-slate-400 dark:text-slate-500 mt-0.5">{{ $alt['catatan'] }}</div>
                                    @endif
                                </div>
                                @if ($maju)
                                    <button wire:click="mulaiPerbaiki(@js($hasil['input'] ?? $kalimat), @js($alt['teks'] ?? null))"
                                        class="shrink-0 text-xs text-brand-700 hover:text-brand-800 dark:text-brand-400 hover:underline cursor-pointer">Ajukan Koreksi</button>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Word Breakdown (Rincian Kata) -->
        @if (! empty($kata))
            <div class="rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-sm border border-brand-100/30 dark:border-slate-800/80">
                <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">Analisis Rincian Kata</div>
                <div class="grid gap-2.5 sm:grid-cols-2">
                    @foreach ($kata as $k)
                        @php($srcKata = $maju ? ($k['indonesia'] ?? '') : ($k['tolaki'] ?? ''))
                        @php($dstKata = $maju ? ($k['tolaki'] ?? '') : ($k['indonesia'] ?? ''))
                        <div class="flex items-start justify-between gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850">
                            <div class="min-w-0">
                                <div class="text-sm">
                                    <span class="text-slate-500 dark:text-slate-400">{{ $srcKata }}</span>
                                    <span class="text-slate-400 mx-1">➜</span>
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $dstKata }}</span>
                                </div>
                                
                                @php($sumber = $k['sumber'] ?? '')
                                @if ($sumber)
                                    <span @class([
                                        'inline-block mt-1.5 rounded-full px-2 py-0.5 text-3xs font-medium uppercase tracking-wider',
                                        'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300' => $sumber === 'kamus',
                                        'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-300' => in_array($sumber, ['pinjaman', 'tak_dikenal']),
                                        'bg-purple-50 text-purple-700 dark:bg-purple-950/30 dark:text-purple-300' => $sumber === 'koreksi',
                                    ])>{{ $sumber }}</span>
                                @endif
                                
                                @if (! empty($k['sinonim']))
                                    <div class="text-3xs text-slate-400 dark:text-slate-500 mt-1">Sinonim: {{ implode(', ', $k['sinonim']) }}</div>
                                @endif
                                @if (! empty($k['catatan']))
                                    <div class="text-3xs text-slate-450 dark:text-slate-500 mt-1">{{ $k['catatan'] }}</div>
                                @endif
                            </div>
                            @if ($maju)
                                <button wire:click="mulaiPerbaiki(@js($k['indonesia'] ?? ''), @js($k['tolaki'] ?? null))"
                                    class="shrink-0 text-xs text-brand-700 hover:text-brand-800 dark:text-brand-400 hover:underline cursor-pointer">Perbaiki</button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if (! empty($hasil['disclaimer']))
            <div class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1">
                <svg class="h-3.5 w-3.5 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $hasil['disclaimer'] }}</span>
            </div>
        @endif
    @endif

    <!-- Interactive Correction Panel -->
    @if ($perbaikiTeks !== null)
        <div class="rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-md border-2 border-brand-500/80 dark:border-brand-600/80 animate-in fade-in slide-in-from-bottom-4 duration-300">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-800/50 pb-3">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-brand-700 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span class="font-serif text-lg font-bold text-slate-900 dark:text-white">Koreksi Terjemahan Sistem</span>
                </div>
                <button wire:click="batalPerbaiki" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-xl text-sm border border-slate-100 dark:border-slate-850 space-y-1 mb-4">
                <div><span class="text-slate-400 font-medium">Teks Sumber:</span> <span class="font-medium text-slate-800 dark:text-slate-200">"{{ $perbaikiTeks }}"</span></div>
                @if ($perbaikiSistem)
                    <div><span class="text-slate-400 font-medium">Terjemahan Sistem:</span> <span class="italic text-slate-550">"{{ $perbaikiSistem }}"</span></div>
                @endif
            </div>
            
            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-450 dark:text-slate-500 uppercase tracking-wide">Usulan Terjemahan Tolaki yang Benar</label>
                    <input type="text" wire:model="tolakiBenar" placeholder="Masukkan kata/kalimat Tolaki usulan Anda..."
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-800 dark:bg-slate-950 focus:border-brand-500 focus:ring-brand-500 focus:ring-2 focus:outline-none transition-all duration-150 text-slate-900 dark:text-white text-base">
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-450 dark:text-slate-500 uppercase tracking-wide">Catatan atau Konteks Penggunaan (Opsional)</label>
                    <input type="text" wire:model="catatan" placeholder="Tambahkan alasan atau konteks (mis. dialek Konawe/Mekongga)..."
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-800 dark:bg-slate-950 focus:border-brand-500 focus:ring-brand-500 focus:ring-2 focus:outline-none transition-all duration-150 text-slate-900 dark:text-white text-base">
                </div>
                
                <div class="flex items-center gap-3 pt-2">
                    <button wire:click="kirimKoreksi"
                        class="rounded-xl bg-brand-700 px-5 py-3 text-sm font-semibold text-white hover:bg-brand-800 dark:bg-brand-600 dark:hover:bg-brand-700 shadow-md shadow-brand-700/10 dark:shadow-none transition-all duration-150 cursor-pointer">
                        Kirim Usulan Koreksi
                    </button>
                    <button wire:click="batalPerbaiki"
                        class="rounded-xl border border-slate-200 dark:border-slate-800 px-5 py-3 text-sm font-semibold text-slate-650 dark:text-slate-350 hover:bg-slate-50 dark:hover:bg-slate-850 transition-all duration-150 cursor-pointer">
                        Batal
                    </button>
                </div>
                
                <p class="text-xs text-slate-450 dark:text-slate-500 italic mt-2">ⓘ Usulan koreksi Anda akan diverifikasi oleh tim moderator bahasa sebelum diintegrasikan ke dalam sistem.</p>
            </div>
        </div>
    @endif
</div>
