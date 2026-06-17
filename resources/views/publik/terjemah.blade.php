<x-public-layout :title="'Terjemah — Penerjemah Tolaki'">
    <livewire:publik.terjemah />

    {{-- Ringkasan singkat tentang proyek + tautan ke halaman lengkap --}}
    <div class="max-w-2xl mx-auto mt-10 text-center">
        <p class="text-sm leading-relaxed text-slate-500 dark:text-slate-400">
            Penerjemah dua arah Indonesia ⇄ Tolaki berbasis AI (RAG) untuk melestarikan
            dan mendigitalisasi bahasa daerah Tolaki, Sulawesi Tenggara.
        </p>
        <a href="{{ route('tentang') }}" wire:navigate
            class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-brand-700 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300">
            Pelajari selengkapnya →
        </a>
    </div>
</x-public-layout>
