<x-public-layout>
    <div class="max-w-2xl mx-auto space-y-8 py-4">
        <!-- Header Section -->
        <div class="text-center space-y-3">
            <h1 class="font-serif text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white">Dukung Pengembangan</h1>
            <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400 max-w-lg mx-auto">
                Aplikasi Kamus & Penerjemah Bahasa Tolaki ini dikembangkan secara sukarela untuk melestarikan warisan budaya. Dukungan Anda sangat berarti untuk biaya pemeliharaan server dan pengembangan fitur.
            </p>
        </div>

        <!-- Donation Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <!-- E-Wallet DANA Card -->
            <div x-data="{ copied: false, nohp: '{{ config('donasi.dana_no') }}' }" 
                class="relative rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-sm border border-brand-100/30 dark:border-slate-800/80 flex flex-col justify-between gap-4 transition-all duration-200 hover:scale-[1.01] overflow-hidden">
                <div class="absolute -right-6 -top-6 w-16 h-16 bg-sky-500/5 dark:bg-sky-600/5 rounded-full filter blur-lg"></div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-sky-500 dark:text-sky-400 uppercase tracking-widest font-sans">E-Wallet DANA</span>
                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-lg font-mono font-bold tracking-wider text-slate-900 dark:text-white mt-2" x-text="nohp"></p>
                    <p class="text-xs text-slate-400 dark:text-slate-500">Atas Nama: <span class="font-semibold text-slate-700 dark:text-slate-350">{{ config('donasi.dana_an') }}</span></p>
                </div>
                <button @click="navigator.clipboard.writeText(nohp); copied = true; setTimeout(() => copied = false, 2000)" type="button"
                    class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl px-4 py-2.5 text-xs font-semibold border transition-all duration-150 cursor-pointer"
                    :class="copied ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-300 dark:border-emerald-900/30' : 'bg-slate-55/40 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-750 hover:bg-slate-50 dark:hover:bg-slate-850'">
                    <span x-show="!copied" class="inline-flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                        Salin Nomor DANA
                    </span>
                    <span x-show="copied" class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Tersalin!
                    </span>
                </button>
            </div>

            <!-- E-Wallet GoPay Card -->
            <div x-data="{ copied: false, nohp: '{{ config('donasi.gopay_no') }}' }" 
                class="relative rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-sm border border-brand-100/30 dark:border-slate-800/80 flex flex-col justify-between gap-4 transition-all duration-200 hover:scale-[1.01] overflow-hidden">
                <div class="absolute -right-6 -top-6 w-16 h-16 bg-emerald-500/5 dark:bg-emerald-600/5 rounded-full filter blur-lg"></div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest font-sans">E-Wallet GoPay</span>
                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-lg font-mono font-bold tracking-wider text-slate-900 dark:text-white mt-2" x-text="nohp"></p>
                    <p class="text-xs text-slate-400 dark:text-slate-500">Atas Nama: <span class="font-semibold text-slate-700 dark:text-slate-350">{{ config('donasi.gopay_an') }}</span></p>
                </div>
                <button @click="navigator.clipboard.writeText(nohp); copied = true; setTimeout(() => copied = false, 2000)" type="button"
                    class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl px-4 py-2.5 text-xs font-semibold border transition-all duration-150 cursor-pointer"
                    :class="copied ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-300 dark:border-emerald-900/30' : 'bg-slate-55/40 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-750 hover:bg-slate-50 dark:hover:bg-slate-850'">
                    <span x-show="!copied" class="inline-flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                        Salin Nomor GoPay
                    </span>
                    <span x-show="copied" class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Tersalin!
                    </span>
                </button>
            </div>

            <!-- E-Wallet OVO Card -->
            <div x-data="{ copied: false, nohp: '{{ config('donasi.ovo_no') }}' }" 
                class="relative rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-sm border border-brand-100/30 dark:border-slate-800/80 flex flex-col justify-between gap-4 transition-all duration-200 hover:scale-[1.01] overflow-hidden">
                <div class="absolute -right-6 -top-6 w-16 h-16 bg-purple-500/5 dark:bg-purple-600/5 rounded-full filter blur-lg"></div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-widest font-sans">E-Wallet OVO</span>
                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-lg font-mono font-bold tracking-wider text-slate-900 dark:text-white mt-2" x-text="nohp"></p>
                    <p class="text-xs text-slate-400 dark:text-slate-500">Atas Nama: <span class="font-semibold text-slate-700 dark:text-slate-350">{{ config('donasi.ovo_an') }}</span></p>
                </div>
                <button @click="navigator.clipboard.writeText(nohp); copied = true; setTimeout(() => copied = false, 2000)" type="button"
                    class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl px-4 py-2.5 text-xs font-semibold border transition-all duration-150 cursor-pointer"
                    :class="copied ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-300 dark:border-emerald-900/30' : 'bg-slate-55/40 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-750 hover:bg-slate-50 dark:hover:bg-slate-850'">
                    <span x-show="!copied" class="inline-flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                        Salin Nomor OVO
                    </span>
                    <span x-show="copied" class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Tersalin!
                    </span>
                </button>
            </div>

            <!-- Bank Jago Card -->
            <div x-data="{ copied: false, norek: '{{ config('donasi.jago_no') }}' }" 
                class="relative rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-sm border border-brand-100/30 dark:border-slate-800/80 flex flex-col justify-between gap-4 transition-all duration-200 hover:scale-[1.01] overflow-hidden">
                <div class="absolute -right-6 -top-6 w-16 h-16 bg-orange-500/5 dark:bg-orange-600/5 rounded-full filter blur-lg"></div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-orange-600 dark:text-orange-400 uppercase tracking-widest font-sans">Bank Jago</span>
                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <p class="text-lg font-mono font-bold tracking-wider text-slate-900 dark:text-white mt-2" x-text="norek"></p>
                    <p class="text-xs text-slate-400 dark:text-slate-500">Atas Nama: <span class="font-semibold text-slate-700 dark:text-slate-350">{{ config('donasi.jago_an') }}</span></p>
                </div>
                <button @click="navigator.clipboard.writeText(norek); copied = true; setTimeout(() => copied = false, 2000)" type="button"
                    class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl px-4 py-2.5 text-xs font-semibold border transition-all duration-150 cursor-pointer"
                    :class="copied ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-300 dark:border-emerald-900/30' : 'bg-slate-55/40 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-750 hover:bg-slate-50 dark:hover:bg-slate-850'">
                    <span x-show="!copied" class="inline-flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                        Salin Rekening
                    </span>
                    <span x-show="copied" class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Tersalin!
                    </span>
                </button>
            </div>
        </div>

        <!-- Sincere Note -->
        <div class="rounded-2xl bg-[#F5F2EB]/50 dark:bg-slate-900/40 p-5 border border-brand-100/20 dark:border-slate-800/40 text-center text-xs text-slate-500 dark:text-slate-400 italic">
            "Suku Tolaki memegang erat semboyan 'Inae Konasara Ie Pinokonasara, Inae Lia Nasara Ie Pinekasiri' yang menjunjung tinggi adab dan penghormatan. Setiap saweran Anda secara tulus membantu kami terus menjaga warisan peradaban leluhur agar abadi di era digital."
        </div>
    </div>
</x-public-layout>
