<x-public-layout :title="'Tentang — Penerjemah Tolaki'">
    @php
        // Dokumen dirender dari DESKRIPSI_PROYEK.md (resources/ dulu, fallback folder induk).
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

    <div class="max-w-3xl mx-auto">
        <a href="{{ route('terjemah') }}" wire:navigate
            class="inline-flex items-center gap-1 mb-6 text-sm font-medium text-brand-700 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300">
            ← Kembali ke Terjemah
        </a>

        @if ($deskripsi)
            <article class="deskripsi-proyek rounded-2xl bg-white dark:bg-slate-900 p-6 sm:p-10 shadow-sm ring-1 ring-brand-100/50 dark:ring-slate-800">
                {!! $deskripsi !!}
            </article>
        @else
            <p class="text-slate-500 dark:text-slate-400">Dokumen deskripsi belum tersedia.</p>
        @endif
    </div>

    <style>
        .deskripsi-proyek { color:#475569; font-size:.95rem; line-height:1.7; }
        .dark .deskripsi-proyek { color:#cbd5e1; }
        .deskripsi-proyek h1 { font-size:1.6rem; font-weight:700; color:#0f172a; margin:0 0 .75rem; font-family:'Playfair Display',serif; }
        .dark .deskripsi-proyek h1 { color:#f8fafc; }
        .deskripsi-proyek h2 { font-size:1.2rem; font-weight:600; color:#0f172a; margin:1.75rem 0 .6rem; }
        .dark .deskripsi-proyek h2 { color:#f1f5f9; }
        .deskripsi-proyek p { margin:.6rem 0; }
        .deskripsi-proyek ul { list-style:disc; padding-left:1.4rem; margin:.6rem 0; }
        .deskripsi-proyek ol { list-style:decimal; padding-left:1.4rem; margin:.6rem 0; }
        .deskripsi-proyek li { margin:.35rem 0; }
        .deskripsi-proyek strong { font-weight:600; color:#0f172a; }
        .dark .deskripsi-proyek strong { color:#f1f5f9; }
        .deskripsi-proyek em { font-style:italic; }
        .deskripsi-proyek hr { margin:1.5rem 0; border:0; border-top:1px solid #e2e8f0; }
        .dark .deskripsi-proyek hr { border-top-color:#334155; }
        .deskripsi-proyek a { color:#4f6bed; text-decoration:underline; text-underline-offset:2px; }
        .dark .deskripsi-proyek a { color:#818cf8; }
        .deskripsi-proyek a:hover { color:#3b54d4; }
        .dark .deskripsi-proyek a:hover { color:#a5b4fc; }
    </style>
</x-public-layout>
