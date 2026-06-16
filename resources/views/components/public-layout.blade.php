<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Penerjemah Tolaki') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('images/kalo sara.jpeg') }}">

    <!-- Google Fonts: Outfit (Sans) & Playfair Display (Serif) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Dark Mode Initializer Script -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body class="font-sans antialiased bg-[#FAF8F5] dark:bg-slate-950 text-slate-800 dark:text-slate-200 min-h-screen flex flex-col selection:bg-brand-100 selection:text-brand-900 dark:selection:bg-brand-900/30 dark:selection:text-brand-300">
    <header class="sticky top-0 z-50 border-b border-brand-100/50 dark:border-slate-800/80 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md pt-[env(safe-area-inset-top,0px)]">
        <div class="mx-auto max-w-5xl px-3 sm:px-6">
            <div class="flex h-16 sm:h-20 items-center justify-between">
                <a href="{{ route('terjemah') }}" wire:navigate class="flex items-center gap-2 sm:gap-3 group transition-transform duration-200 hover:scale-[1.01] shrink-0">
                    <img src="{{ asset('images/kalo sara.jpeg') }}" alt="Kalo Sara Logo" class="h-10 w-10 sm:h-11 sm:w-11 rounded-full object-cover border-2 border-brand-500 shadow-md transition-all duration-300 group-hover:border-brand-600 group-hover:shadow-brand-100 dark:group-hover:shadow-none">
                    <div class="hidden sm:flex flex-col">
                        <span class="text-xs font-semibold tracking-wider uppercase text-brand-700 dark:text-brand-400 leading-none mb-1">Penerjemah</span>
                        <span class="font-serif text-lg sm:text-xl font-bold tracking-tight text-slate-900 dark:text-white leading-none">Tolaki</span>
                    </div>
                </a>
                <nav class="flex items-center gap-1.5 sm:gap-2 text-xs sm:text-sm">
                    <a href="{{ route('terjemah') }}" wire:navigate
                        @class([
                            'rounded-lg px-3 py-2 font-medium transition-all duration-200',
                            'bg-brand-700 text-white shadow-sm shadow-brand-700/20 dark:bg-brand-600 dark:shadow-none' => request()->routeIs('terjemah'),
                            'text-slate-600 dark:text-slate-300 hover:bg-brand-50/60 hover:text-brand-700 dark:hover:bg-slate-800 dark:hover:text-white' => ! request()->routeIs('terjemah'),
                        ])>Terjemah</a>
                    <a href="{{ route('kamus') }}" wire:navigate
                        @class([
                            'rounded-lg px-3 py-2 font-medium transition-all duration-200',
                            'bg-brand-700 text-white shadow-sm shadow-brand-700/20 dark:bg-brand-600 dark:shadow-none' => request()->routeIs('kamus'),
                            'text-slate-600 dark:text-slate-300 hover:bg-brand-50/60 hover:text-brand-700 dark:hover:bg-slate-800 dark:hover:text-white' => ! request()->routeIs('kamus'),
                        ])>Kamus</a>
                    <div class="w-px h-5 bg-slate-200 dark:bg-slate-800 mx-1"></div>
                    @auth
                        <a href="{{ route('dashboard') }}" wire:navigate
                            class="rounded-lg px-3 py-2 font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200">Panel</a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate
                            class="rounded-lg px-3 py-2 font-medium text-brand-700 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-slate-800 transition-all duration-200">Masuk</a>
                    @endauth
                    
                    <div class="w-px h-5 bg-slate-200 dark:bg-slate-800 mx-1"></div>
                    
                    <!-- Theme Toggle Button -->
                    <button id="theme-toggle" type="button" class="text-slate-500 dark:text-slate-400 hover:bg-brand-50/60 dark:hover:bg-slate-800 p-2 rounded-lg transition-colors duration-200 cursor-pointer flex items-center justify-center shrink-0" title="Ubah Tema">
                        <!-- Sun Icon (shown in dark mode) -->
                        <svg id="theme-toggle-light-icon" class="hidden h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.46 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z" />
                        </svg>
                        <!-- Moon Icon (shown in light mode) -->
                        <svg id="theme-toggle-dark-icon" class="hidden h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </header>

    <main class="flex-1 mx-auto w-full max-w-5xl px-4 sm:px-6 py-10">
        {{ $slot }}
    </main>

    <footer class="border-t border-brand-100/30 dark:border-slate-900 bg-white/40 dark:bg-slate-950/40 py-8 mt-12">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500 dark:text-slate-400">
            <div class="flex flex-col sm:flex-row items-center justify-center sm:justify-start gap-2 text-center sm:text-left">
                <img src="{{ asset('images/kalo sara.jpeg') }}" alt="Kalo Sara" class="h-6 w-6 rounded-full object-cover filter grayscale opacity-60 shrink-0">
                <span class="leading-relaxed">© {{ date('Y') }} Penerjemah Bahasa Tolaki. Melestarikan Budaya Lewat Teknologi.</span>
            </div>
            <div class="flex flex-wrap items-center gap-4 justify-center md:justify-end">
                <a href="{{ route('donasi') }}" wire:navigate 
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-bold shadow-sm transition-all duration-150 transform hover:scale-[1.02] cursor-pointer"
                    style="background-color: #FFDD00 !important; color: #0f172a !important;">
                    Buy me a ☕
                </a>
                <span class="hover:text-brand-600 transition-colors duration-200 cursor-pointer">Syarat & Ketentuan</span>
                <span class="hover:text-brand-600 transition-colors duration-200 cursor-pointer">Kebijakan Privasi</span>
            </div>
        </div>
    </footer>

    <!-- Theme Switcher Controller Script -->
    <script>
        function applyTheme() {
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }

        function setupThemeToggle() {
            // Re-apply theme class on DOM swap to prevent Livewire from stripping it
            applyTheme();

            const toggleBtn = document.getElementById('theme-toggle');
            if (!toggleBtn) return;
            
            const lightIcon = document.getElementById('theme-toggle-light-icon');
            const darkIcon = document.getElementById('theme-toggle-dark-icon');
            
            function updateIcons() {
                if (document.documentElement.classList.contains('dark')) {
                    lightIcon.classList.remove('hidden');
                    darkIcon.classList.add('hidden');
                } else {
                    lightIcon.classList.add('hidden');
                    darkIcon.classList.remove('hidden');
                }
            }
            
            updateIcons();
            
            // Remove any existing event listeners before binding to avoid duplicates
            toggleBtn.replaceWith(toggleBtn.cloneNode(true));
            const newToggleBtn = document.getElementById('theme-toggle');
            
            newToggleBtn.addEventListener('click', function() {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
                updateIcons();
            });
        }

        // Setup theme toggle on initial load
        setupThemeToggle();

        // Setup theme toggle on Livewire navigation
        document.addEventListener('livewire:navigated', setupThemeToggle);
    </script>
</body>

</html>
