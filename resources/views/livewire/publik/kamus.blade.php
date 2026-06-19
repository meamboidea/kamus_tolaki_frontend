<?php

use App\Enums\StatusKoreksi;
use App\Models\Koreksi;
use App\Services\TerjemahService;

use function Livewire\Volt\{state};

state([
    'q' => '',
    'bidang' => 'auto', // 'auto' | 'id' (Indonesia→Tolaki) | 'tolaki' (Tolaki→Indonesia)
    'hasil' => [],          // entri kamus riset (dari FastAPI)
    'hasilKoreksi' => [],   // kontribusi penutur disetujui (dari DB Laravel) — terpisah
    'memuat' => false,
    'sudahCari' => false,
    'galat' => null,
]);

$cari = function (): void {
    $this->q = trim($this->q);
    $this->galat = null;
    if ($this->q === '') {
        $this->hasil = [];
        $this->hasilKoreksi = [];
        $this->sudahCari = false;

        return;
    }
    $this->memuat = true;

    // 1) Kamus riset (terverifikasi) lewat mesin FastAPI.
    try {
        $this->hasil = app(TerjemahService::class)->cari($this->q, $this->bidang);
    } catch (\Throwable $e) {
        $this->hasil = [];
        $this->galat = 'Gagal mencari di kamus. Pastikan layanan terjemah (FastAPI) aktif.';
    }

    // 2) Kontribusi penutur (koreksi disetujui) dari DB Laravel — sumber TERPISAH,
    //    sengaja tidak digabung dengan kamus riset; tetap jalan walau FastAPI mati.
    $istilah = '%' . $this->q . '%';
    $bidang = $this->bidang;
    $this->hasilKoreksi = Koreksi::where('status', StatusKoreksi::Approved)
        ->where(function ($w) use ($istilah, $bidang) {
            if ($bidang === 'id') {
                $w->where('teks_sumber', 'like', $istilah);
            } elseif ($bidang === 'tolaki') {
                $w->where('tolaki_usulan', 'like', $istilah);
            } else {
                $w->where('teks_sumber', 'like', $istilah)
                    ->orWhere('tolaki_usulan', 'like', $istilah);
            }
        })
        ->orderByDesc('utama')
        ->orderByDesc('suara')
        ->limit(20)
        ->get(['teks_sumber', 'tolaki_usulan', 'catatan', 'utama', 'suara'])
        ->toArray();

    $this->memuat = false;
    $this->sudahCari = true;
};

$pilihBidang = function (string $b): void {
    $this->bidang = $b;
    if (trim($this->q) !== '') {
        $this->cari();
    }
};

?>

<div class="space-y-6">
    <div class="space-y-2">
        <h1 class="font-serif text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white">Telusur Kamus Tolaki</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 max-w-xl">Cari kosakata bahasa Tolaki atau cari maknanya dari bahasa Indonesia dengan database kosakata yang terverifikasi.</p>
    </div>

    <!-- Direction Tabs -->
    <div class="flex flex-wrap gap-2 text-sm">
        @foreach (['auto' => 'Semua Kategori', 'id' => 'Indonesia ➜ Tolaki', 'tolaki' => 'Tolaki ➜ Indonesia'] as $val => $lbl)
            <button wire:click="pilihBidang('{{ $val }}')" type="button"
                @class([
                    'rounded-xl px-4 py-2 font-medium transition-all duration-200 cursor-pointer border',
                    'bg-brand-700 text-white border-brand-700 shadow-sm shadow-brand-700/15 dark:bg-brand-600 dark:border-brand-600 dark:shadow-none' => $bidang === $val,
                    'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-800 hover:border-brand-200 dark:hover:border-brand-800 hover:text-brand-700 dark:hover:text-brand-400' => $bidang !== $val,
                ])>{{ $lbl }}</button>
        @endforeach
    </div>

    <!-- Search Input Area -->
    <div class="rounded-2xl bg-white dark:bg-slate-900 p-5 shadow-sm border border-brand-100/30 dark:border-slate-800/80">
        <form wire:submit="cari" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" wire:model="q" placeholder="Masukkan kata pencarian... (mis. rumah, laika, dll.)"
                    class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 dark:border-slate-850 dark:bg-slate-950 focus:border-brand-500 focus:ring-brand-500 focus:ring-2 focus:outline-none transition-all duration-150 text-slate-900 dark:text-white text-base">
            </div>
            <button type="submit" wire:loading.attr="disabled"
                class="rounded-xl bg-brand-700 px-6 py-3 text-sm font-semibold text-white hover:bg-brand-800 dark:bg-brand-600 dark:hover:bg-brand-700 shadow-md shadow-brand-700/10 dark:shadow-none transition-all duration-150 disabled:opacity-60 cursor-pointer min-w-[100px] flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="cari">Temukan</span>
                <span wire:loading wire:target="cari" class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                <span wire:loading wire:target="cari">Mencari...</span>
            </button>
        </form>
    </div>

    @if ($galat)
        <div class="rounded-xl bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/30 px-4 py-3 text-sm text-rose-700 dark:text-rose-400 flex items-center gap-2">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ $galat }}</span>
        </div>
    @endif

    @if ($sudahCari && empty($hasil) && empty($hasilKoreksi))
        <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-900 p-12 text-center shadow-sm">
            <svg class="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-slate-500 dark:text-slate-400">Tidak ada hasil yang cocok untuk kata <span class="font-semibold text-slate-800 dark:text-slate-200">"{{ $q }}"</span>.</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Coba gunakan kata dasar atau periksa kembali ejaan kata tersebut.</p>
        </div>
    @endif

    @if (! empty($hasil))
        <div class="space-y-3">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Kamus Tolaki — Terverifikasi ({{ count($hasil) }})</div>
            <div class="grid gap-3">
                @foreach ($hasil as $e)
                    <div class="group rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-sm border border-brand-100/30 dark:border-slate-800/80 transition-all duration-200 hover:shadow-md hover:border-brand-200/50 dark:hover:border-slate-800">
                        <div class="flex flex-wrap items-baseline justify-between gap-3">
                            <div class="text-xl font-bold text-brand-800 dark:text-brand-400 group-hover:text-brand-700 transition-colors duration-150">{{ $e['kata_tolaki'] ?? '' }}</div>
                            @if (! empty($e['induk_kata']))
                                <span class="rounded-full bg-brand-50 text-brand-800 border border-brand-100/30 dark:bg-brand-950/40 dark:text-brand-300 dark:border-none px-2.5 py-0.5 text-xs font-medium">
                                    induk: {{ $e['induk_kata'] }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="text-slate-700 dark:text-slate-300 mt-2 text-base leading-relaxed">{{ $e['arti_indonesia'] ?? '' }}</div>
                        
                        @if (! empty($e['contoh']))
                            <div class="mt-4 border-t border-slate-100 dark:border-slate-800/50 pt-3">
                                <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 mb-2 uppercase tracking-wide">Contoh Penggunaan</div>
                                <ul class="space-y-2.5">
                                    @foreach ($e['contoh'] as $c)
                                        <li class="border-l-2 border-brand-600 pl-3 py-0.5">
                                            <div class="italic text-slate-900 dark:text-slate-100 font-medium">{{ $c['tolaki'] ?? '' }}</div>
                                            <div class="text-sm text-slate-500 dark:text-slate-400">{{ $c['indonesia'] ?? '' }}</div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Kontribusi penutur (koreksi disetujui) — SENGAJA dipisah dari kamus riset. --}}
    @if (! empty($hasilKoreksi))
        <div class="space-y-3">
            <div class="text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                Kontribusi Penutur ({{ count($hasilKoreksi) }})
            </div>
            <div class="flex items-start gap-2 rounded-xl bg-amber-50/60 dark:bg-amber-950/10 border border-amber-200/40 dark:border-amber-900/20 px-3.5 py-2.5 text-xs text-amber-700 dark:text-amber-400">
                <svg class="h-4 w-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Usulan dari penutur asli yang telah disetujui moderator — <strong>bukan</strong> entri kamus hasil penelitian, melainkan kontribusi komunitas.</span>
            </div>
            <div class="grid gap-3">
                @foreach ($hasilKoreksi as $k)
                    <div class="rounded-2xl bg-amber-50/40 dark:bg-amber-950/10 p-6 shadow-sm border border-amber-200/50 dark:border-amber-900/30">
                        <div class="flex flex-wrap items-baseline justify-between gap-3">
                            <div class="text-xl font-bold text-amber-800 dark:text-amber-300">{{ $k['tolaki_usulan'] }}</div>
                            <div class="flex items-center gap-1.5">
                                @if ($k['utama'])
                                    <span class="rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-200 px-2.5 py-0.5 text-xs font-medium">utama</span>
                                @endif
                                @if (($k['suara'] ?? 0) > 1)
                                    <span class="rounded-full bg-amber-100/70 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 px-2.5 py-0.5 text-xs font-medium">{{ $k['suara'] }}× diusulkan</span>
                                @endif
                            </div>
                        </div>

                        <div class="text-slate-700 dark:text-slate-300 mt-2 text-base leading-relaxed">{{ $k['teks_sumber'] }}</div>

                        @if (! empty($k['catatan']))
                            <div class="mt-3 border-t border-amber-200/40 dark:border-amber-900/20 pt-2.5 text-sm text-slate-500 dark:text-slate-400">
                                <span class="font-medium text-amber-700 dark:text-amber-400">Catatan penutur:</span> {{ $k['catatan'] }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
