<?php

use App\Enums\StatusKoreksi;
use App\Models\Koreksi;
use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\{computed, usesPagination};

usesPagination();

$daftar = computed(fn () => Koreksi::pending()->latest()->paginate(15));

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

?>

<div>
    @if (session('ok'))
        <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-300">
            {{ session('ok') }}
        </div>
    @endif

    @if ($this->daftar->isEmpty())
        <div class="rounded-lg bg-white dark:bg-gray-800 p-10 text-center text-gray-500 dark:text-gray-400 shadow-sm">
            Tidak ada koreksi yang menunggu tinjauan.
        </div>
    @else
        <div class="space-y-3">
            @foreach ($this->daftar as $k)
                <div wire:key="k-{{ $k->id }}"
                    class="rounded-lg bg-white dark:bg-gray-800 p-4 shadow-sm ring-1 ring-gray-100 dark:ring-gray-700">
                    <div class="flex flex-wrap items-start justify-between gap-4">
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

                        <div class="flex shrink-0 flex-col gap-2">
                            <button wire:click="setujui({{ $k->id }}, true)"
                                wire:confirm="Setujui sebagai bentuk UTAMA?"
                                class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700">
                                Setujui (utama)
                            </button>
                            <button wire:click="setujui({{ $k->id }})"
                                class="rounded-md bg-green-50 dark:bg-green-900/30 px-3 py-1.5 text-sm font-medium text-green-700 dark:text-green-300 hover:bg-green-100">
                                Setujui (varian)
                            </button>
                            <button wire:click="tolak({{ $k->id }})"
                                wire:confirm="Tolak koreksi ini?"
                                class="rounded-md bg-red-50 dark:bg-red-900/30 px-3 py-1.5 text-sm font-medium text-red-700 dark:text-red-300 hover:bg-red-100">
                                Tolak
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $this->daftar->links() }}</div>
    @endif
</div>
