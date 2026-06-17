<?php

use App\Enums\StatusKoreksi;
use App\Models\Koreksi;
use App\Models\PenyumbangDiblokir;
use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\{computed, state, usesPagination};

usesPagination();

// Admin: lihat SEMUA koreksi (default tab 'semua'). Moderator: riwayat miliknya.
state(['tab' => fn () => Auth::user()->isAdmin() ? 'semua' : 'menunggu']);

$gantiTab = function (string $tab): void {
    $this->tab = $tab;
    $this->resetPage();
};

$daftar = computed(function () {
    $admin = Auth::user()->isAdmin();

    $q = match ($this->tab) {
        // Admin melihat semua koreksi; moderator hanya yang ditinjau olehnya.
        'disetujui' => $admin
            ? Koreksi::where('status', StatusKoreksi::Approved)->latest('ditinjau_pada')
            : Koreksi::where('ditinjau_oleh', Auth::id())
                ->where('status', StatusKoreksi::Approved)->latest('ditinjau_pada'),
        'ditolak' => $admin
            ? Koreksi::where('status', StatusKoreksi::Rejected)->latest('ditinjau_pada')
            : Koreksi::where('ditinjau_oleh', Auth::id())
                ->where('status', StatusKoreksi::Rejected)->latest('ditinjau_pada'),
        'dicabut' => Koreksi::dicabut()->latest('dicabut_pada'),
        'semua' => Koreksi::latest(),
        default => Koreksi::pending()->latest(),
    };

    return $q->with(['peninjau', 'pencabut'])->paginate(15);
});

// Sinyal moderasi: jumlah koreksi DITOLAK per penyumbang yang tampil di halaman ini.
// Membantu admin membedakan "satu orang nakal" vs "banyak orang berbeda".
$tolakanPenyumbang = computed(function () {
    $ids = collect($this->daftar->items())->pluck('penyumbang_id')->filter()->unique();
    if ($ids->isEmpty()) {
        return [];
    }

    return Koreksi::whereIn('penyumbang_id', $ids)
        ->where('status', StatusKoreksi::Rejected)
        ->selectRaw('penyumbang_id, count(*) as c')
        ->groupBy('penyumbang_id')
        ->pluck('c', 'penyumbang_id')
        ->all();
});

// Himpunan penyumbang_id yang SUDAH diblokir (di antara yang tampil), untuk badge.
$penyumbangDiblokir = computed(function () {
    $ids = collect($this->daftar->items())->pluck('penyumbang_id')->filter()->unique();
    if ($ids->isEmpty()) {
        return [];
    }

    return PenyumbangDiblokir::whereIn('penyumbang_id', $ids)
        ->pluck('penyumbang_id')
        ->all();
});

$setujui = function (int $id, bool $utama = false): void {
    $k = Koreksi::findOrFail($id);
    $k->update([
        'status' => StatusKoreksi::Approved,
        'utama' => $utama,
        'ditinjau_oleh' => Auth::id(),
        'ditinjau_pada' => now(),
    ]);
    session()->flash('ok', "Disetujui: {$k->teks_sumber} → {$k->tolaki_usulan}");
};

$tolak = function (int $id): void {
    $k = Koreksi::findOrFail($id);
    $k->update([
        'status' => StatusKoreksi::Rejected,
        'ditinjau_oleh' => Auth::id(),
        'ditinjau_pada' => now(),
    ]);
    session()->flash('ok', "Ditolak: {$k->teks_sumber}");
};

// Cabut koreksi yang sudah disetujui — HANYA admin. Status → superseded
// (keluar dari mesin RAG dalam ≤60 dtk), approver awal tetap di ditinjau_oleh.
$cabut = function (int $id): void {
    abort_unless(Auth::user()->isAdmin(), 403);

    $k = Koreksi::where('status', StatusKoreksi::Approved)->findOrFail($id);
    $k->update([
        'status' => StatusKoreksi::Superseded,
        'dicabut_oleh' => Auth::id(),
        'dicabut_pada' => now(),
    ]);
    session()->flash('ok', "Dicabut: {$k->teks_sumber} → {$k->tolaki_usulan}");
};

// Blokir penyumbang — HANYA admin. Tindakan tegas & TERPISAH dari penolakan koreksi:
// hanya untuk spam/asal-asalan berulang. Mencatat penyumbang_id + ip_hash koreksi ini
// agar submit berikutnya (web/API) langsung ditolak.
$blokir = function (int $id): void {
    abort_unless(Auth::user()->isAdmin(), 403);

    $k = Koreksi::findOrFail($id);
    if (! $k->penyumbang_id && ! $k->ip_hash) {
        session()->flash('ok', 'Koreksi ini tidak punya identitas penyumbang, tidak dapat diblokir.');

        return;
    }

    PenyumbangDiblokir::firstOrCreate(
        ['penyumbang_id' => $k->penyumbang_id],
        ['ip_hash' => $k->ip_hash, 'diblokir_oleh' => Auth::id()],
    );

    session()->flash('ok', "Penyumbang diblokir. Koreksi baru darinya akan otomatis ditolak.");
};

// Buka blokir penyumbang — HANYA admin.
$bukaBlokir = function (int $id): void {
    abort_unless(Auth::user()->isAdmin(), 403);

    $k = Koreksi::findOrFail($id);
    if (! $k->penyumbang_id && ! $k->ip_hash) {
        return;
    }

    PenyumbangDiblokir::query()
        ->when($k->penyumbang_id, fn ($q) => $q->orWhere('penyumbang_id', $k->penyumbang_id))
        ->when($k->ip_hash, fn ($q) => $q->orWhere('ip_hash', $k->ip_hash))
        ->delete();

    session()->flash('ok', 'Blokir penyumbang dibuka.');
};

?>

<div>
    @if (session('ok'))
        <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-300">
            {{ session('ok') }}
        </div>
    @endif

    {{-- Tab (admin: semua koreksi; moderator: riwayat sendiri) --}}
    @php
        $tabs = auth()->user()->isAdmin()
            ? ['semua' => 'Semua', 'menunggu' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'dicabut' => 'Dicabut']
            : ['menunggu' => 'Menunggu', 'disetujui' => 'Disetujui saya', 'ditolak' => 'Ditolak saya'];
    @endphp
    <div class="mb-4 flex gap-1 rounded-lg bg-gray-100 dark:bg-gray-800 p-1">
        @foreach ($tabs as $key => $label)
            <button wire:click="gantiTab('{{ $key }}')"
                @class([
                    'flex-1 rounded-md px-3 py-1.5 text-sm font-medium transition',
                    'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm' => $tab === $key,
                    'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' => $tab !== $key,
                ])>
                {{ $label }}
            </button>
        @endforeach
    </div>

    @if ($this->daftar->isEmpty())
        <div class="rounded-lg bg-white dark:bg-gray-800 p-10 text-center text-gray-500 dark:text-gray-400 shadow-sm">
            {{ $tab === 'menunggu' ? 'Tidak ada koreksi yang menunggu tinjauan.' : 'Belum ada koreksi pada daftar ini.' }}

        </div>
    @else
        <div class="space-y-3">
            @foreach ($this->daftar as $k)
                <div wire:key="k-{{ $k->id }}"
                    class="rounded-lg bg-white dark:bg-gray-800 p-4 shadow-sm ring-1 ring-gray-100 dark:ring-gray-700">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                        <div class="min-w-0">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Indonesia
                                @if ($k->suara > 1)
                                    <span class="ml-1 rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-0.5 text-xs">
                                        {{ $k->suara }}× diusulkan
                                    </span>
                                @endif
                            </div>
                            <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $k->teks_sumber }}</div>

                            <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-1 text-sm">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Usulan Tolaki:</span>
                                    <span class="font-medium text-green-700 dark:text-green-400">{{ $k->tolaki_usulan }}</span>
                                </div>
                                @if ($k->tolaki_sistem)
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Dari sistem:</span>
                                        <span class="text-gray-700 dark:text-gray-300 line-through">{{ $k->tolaki_sistem }}</span>
                                    </div>
                                @endif
                                @if ($k->konteks_kalimat)
                                    <div class="sm:col-span-2">
                                        <span class="text-gray-500 dark:text-gray-400">Konteks:</span>
                                        <span class="text-gray-700 dark:text-gray-300">"{{ $k->konteks_kalimat }}"</span>
                                    </div>
                                @endif
                                @if ($k->catatan)
                                    <div class="sm:col-span-2">
                                        <span class="text-gray-500 dark:text-gray-400">Catatan:</span>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $k->catatan }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($k->status === StatusKoreksi::Pending)
                            <div class="flex shrink-0 flex-wrap gap-2 border-t border-gray-100 dark:border-gray-700 pt-3 sm:flex-col sm:border-0 sm:pt-0">
                                <button wire:click="setujui({{ $k->id }}, true)"
                                    wire:confirm="Setujui sebagai bentuk UTAMA?"
                                    class="flex-1 whitespace-nowrap rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 sm:flex-none">
                                    Setujui (utama)
                                </button>
                                <button wire:click="setujui({{ $k->id }})"
                                    class="flex-1 whitespace-nowrap rounded-md bg-green-50 dark:bg-green-900/30 px-3 py-1.5 text-sm font-medium text-green-700 dark:text-green-300 hover:bg-green-100 sm:flex-none">
                                    Setujui (varian)
                                </button>
                                <button wire:click="tolak({{ $k->id }})"
                                    wire:confirm="Tolak koreksi ini?"
                                    class="flex-1 whitespace-nowrap rounded-md bg-red-50 dark:bg-red-900/30 px-3 py-1.5 text-sm font-medium text-red-700 dark:text-red-300 hover:bg-red-100 sm:flex-none">
                                    Tolak
                                </button>
                            </div>
                        @else
                            <div class="flex shrink-0 flex-wrap items-center gap-x-2 gap-y-1 border-t border-gray-100 dark:border-gray-700 pt-3 sm:flex-col sm:items-end sm:gap-1 sm:border-0 sm:pt-0 sm:text-right">
                                @if ($k->status === StatusKoreksi::Approved)
                                    <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/40 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:text-green-300">
                                        Disetujui{{ $k->utama ? ' (utama)' : ' (varian)' }}
                                    </span>
                                @elseif ($k->status === StatusKoreksi::Superseded)
                                    <span class="inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-900/40 px-2.5 py-0.5 text-xs font-medium text-amber-700 dark:text-amber-300">
                                        Dicabut
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/40 px-2.5 py-0.5 text-xs font-medium text-red-700 dark:text-red-300">
                                        Ditolak
                                    </span>
                                @endif

                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $k->status === StatusKoreksi::Approved ? 'disetujui' : 'ditinjau' }} oleh {{ $k->peninjau?->name ?? '—' }}
                                </span>
                                @if ($k->ditinjau_pada)
                                    <span class="text-xs text-gray-400 dark:text-gray-500">
                                        {{ $k->ditinjau_pada->diffForHumans() }}
                                    </span>
                                @endif

                                {{-- Jejak pencabutan (audit) --}}
                                @if ($k->status === StatusKoreksi::Superseded)
                                    <span class="w-full text-xs text-amber-600 dark:text-amber-400 sm:mt-1 sm:w-auto">
                                        dicabut oleh {{ $k->pencabut?->name ?? '—' }}{{ $k->dicabut_pada ? ' · ' . $k->dicabut_pada->diffForHumans() : '' }}
                                    </span>
                                @endif

                                {{-- Tombol cabut: hanya admin, hanya item yang masih disetujui --}}
                                @if ($k->status === StatusKoreksi::Approved && auth()->user()->isAdmin())
                                    <button wire:click="cabut({{ $k->id }})"
                                        wire:confirm="Cabut koreksi ini? Akan dihapus dari mesin penerjemah (≤60 dtk)."
                                        class="ml-auto rounded-md bg-amber-50 dark:bg-amber-900/30 px-3 py-1.5 text-sm font-medium text-amber-700 dark:text-amber-300 hover:bg-amber-100 sm:ml-0 sm:mt-1">
                                        Cabut
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Footer admin: identitas penyumbang + blokir (TERPISAH dari tolak) --}}
                    @if (auth()->user()->isAdmin() && $k->penyumbang_id)
                        @php
                            $tolakan = $this->tolakanPenyumbang[$k->penyumbang_id] ?? 0;
                            $sudahDiblokir = in_array($k->penyumbang_id, $this->penyumbangDiblokir, true);
                        @endphp
                        <div class="mt-3 flex flex-col gap-2 border-t border-dashed border-gray-100 dark:border-gray-700 pt-3 text-xs sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex flex-wrap items-center gap-2 text-gray-500 dark:text-gray-400">
                                <span class="font-mono">penyumbang: {{ \Illuminate\Support\Str::limit($k->penyumbang_id, 8, '…') }}</span>
                                @if ($tolakan > 0)
                                    <span class="inline-flex items-center rounded-full bg-orange-50 dark:bg-orange-900/30 px-2 py-0.5 font-medium text-orange-700 dark:text-orange-300">
                                        {{ $tolakan }} koreksi ditolak sebelumnya
                                    </span>
                                @endif
                                @if ($sudahDiblokir)
                                    <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/40 px-2 py-0.5 font-semibold text-red-700 dark:text-red-300">
                                        Diblokir
                                    </span>
                                @endif
                            </div>

                            @if ($sudahDiblokir)
                                <button wire:click="bukaBlokir({{ $k->id }})"
                                    wire:confirm="Buka blokir penyumbang ini? Ia bisa mengirim koreksi lagi."
                                    class="shrink-0 rounded-md bg-gray-50 dark:bg-gray-700/50 px-3 py-1.5 font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Buka blokir
                                </button>
                            @else
                                <button wire:click="blokir({{ $k->id }})"
                                    wire:confirm="Blokir penyumbang ini? Hanya untuk spam/asal-asalan — koreksi barunya akan otomatis ditolak. Tindakan ini terpisah dari menolak koreksi."
                                    class="shrink-0 rounded-md bg-red-600 px-3 py-1.5 font-medium text-white hover:bg-red-700">
                                    Blokir penyumbang
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $this->daftar->links() }}</div>
    @endif
</div>
