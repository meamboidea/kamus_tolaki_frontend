<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @php
        $user = auth()->user();
        $disetujui = \App\Models\Koreksi::where('ditinjau_oleh', $user->id)
            ->where('status', \App\Enums\StatusKoreksi::Approved)->count();
        $ditolak = \App\Models\Koreksi::where('ditinjau_oleh', $user->id)
            ->where('status', \App\Enums\StatusKoreksi::Rejected)->count();
        $total = $disetujui + $ditolak;
        $menunggu = $user->canModerate()
            ? \App\Models\Koreksi::where('status', \App\Enums\StatusKoreksi::Pending)->count()
            : 0;

        // Deskripsi proyek dirender dari DESKRIPSI_PROYEK.md. Baca berlapis: salinan
        // di resources/ (pasti ikut ter-deploy) dulu, fallback ke folder induk repo.
        // Baris "Sangat Hemat Biaya" sengaja disaring keluar.
        $descPath = collect([
            resource_path('DESKRIPSI_PROYEK.md'),
            dirname(base_path()) . '/DESKRIPSI_PROYEK.md',
        ])->first(fn ($p) => is_file($p));
        $deskripsi = '';
        if ($descPath) {
            $baris = preg_split('/\r?\n/', file_get_contents($descPath));
            $baris = array_filter($baris, fn ($l) => ! str_contains($l, 'Sangat Hemat Biaya (Cost-Effective)'));
            $deskripsi = \Illuminate\Support\Str::markdown(implode("\n", $baris));
        }
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Notifikasi koreksi masuk --}}
            @if ($menunggu > 0)
                <a href="{{ route('moderasi') }}" wire:navigate
                    class="block rounded-lg bg-amber-50 dark:bg-amber-900/30 ring-1 ring-amber-200 dark:ring-amber-800 shadow-sm p-4 hover:bg-amber-100 dark:hover:bg-amber-900/50 transition">
                    <div class="flex items-center gap-3">
                        <div class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-800/50">
                            <svg class="h-5 w-5 text-amber-600 dark:text-amber-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span class="absolute -top-1 -right-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-xs font-bold text-white">
                                {{ $menunggu }}
                            </span>
                        </div>
                        <div class="min-w-0">
                            <div class="font-semibold text-amber-800 dark:text-amber-200">
                                {{ $menunggu }} koreksi menunggu tinjauan
                            </div>
                            <div class="text-sm text-amber-700 dark:text-amber-300">
                                Klik untuk membuka halaman moderasi →
                            </div>
                        </div>
                    </div>
                </a>
            @endif

            {{-- Profil pengguna --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 flex items-center gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-full bg-brand-100 dark:bg-brand-900/40 text-xl font-bold text-brand-700 dark:text-brand-300">
                        @if ($user->foto_url)
                            <img src="{{ $user->foto_url }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="text-lg font-semibold text-gray-900 dark:text-gray-100 truncate">
                            {{ $user->name }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</div>
                        <span class="mt-1 inline-flex items-center rounded-full bg-brand-50 dark:bg-brand-900/30 px-2.5 py-0.5 text-xs font-medium text-brand-700 dark:text-brand-300">
                            {{ $user->role->label() }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Statistik moderasi --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Disetujui</div>
                    <div class="mt-1 text-3xl font-bold text-green-600 dark:text-green-400">{{ $disetujui }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Ditolak</div>
                    <div class="mt-1 text-3xl font-bold text-red-600 dark:text-red-400">{{ $ditolak }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total ditinjau</div>
                    <div class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $total }}</div>
                </div>
            </div>

            {{-- Tentang Proyek (dari DESKRIPSI_PROYEK.md, untuk semua role) --}}
            @if ($deskripsi)
                <details open class="group bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <summary class="flex cursor-pointer list-none items-center justify-between p-6 font-semibold text-gray-900 dark:text-gray-100">
                        <span>Tentang Project</span>
                        <svg class="h-5 w-5 text-gray-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <div class="deskripsi-proyek border-t border-gray-100 dark:border-gray-700 px-6 py-5">
                        {!! $deskripsi !!}
                    </div>
                </details>
            @endif

        </div>
    </div>

    @push('styles')
        <style>
            .deskripsi-proyek { color:#374151; font-size:.875rem; line-height:1.65; }
            .dark .deskripsi-proyek { color:#d1d5db; }
            .deskripsi-proyek h1 { font-size:1.25rem; font-weight:700; color:#111827; margin:0 0 .5rem; }
            .dark .deskripsi-proyek h1 { color:#f9fafb; }
            .deskripsi-proyek h2 { font-size:1.05rem; font-weight:600; color:#111827; margin:1.25rem 0 .5rem; }
            .dark .deskripsi-proyek h2 { color:#f3f4f6; }
            .deskripsi-proyek p { margin:.5rem 0; }
            .deskripsi-proyek ul { list-style:disc; padding-left:1.25rem; margin:.5rem 0; }
            .deskripsi-proyek ol { list-style:decimal; padding-left:1.25rem; margin:.5rem 0; }
            .deskripsi-proyek li { margin:.25rem 0; }
            .deskripsi-proyek strong { font-weight:600; color:#111827; }
            .dark .deskripsi-proyek strong { color:#f3f4f6; }
            .deskripsi-proyek em { font-style:italic; }
            .deskripsi-proyek hr { margin:1rem 0; border:0; border-top:1px solid #e5e7eb; }
            .dark .deskripsi-proyek hr { border-top-color:#374151; }
            .deskripsi-proyek a { color:#4f6bed; text-decoration:underline; text-underline-offset:2px; }
            .dark .deskripsi-proyek a { color:#818cf8; }
            .deskripsi-proyek a:hover { color:#3b54d4; }
            .dark .deskripsi-proyek a:hover { color:#a5b4fc; }
        </style>
    @endpush
</x-app-layout>
