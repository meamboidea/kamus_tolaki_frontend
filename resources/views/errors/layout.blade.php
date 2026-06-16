<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title') — Penerjemah Tolaki</title>
        
        <!-- Favicon -->
        <link rel="icon" type="image/jpeg" href="{{ asset('images/kalo sara.jpeg') }}">
        
        <!-- Google Fonts: Outfit (Sans) & Playfair Display (Serif) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

        <!-- Dark Mode Initializer Script -->
        <script>
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-800 dark:text-slate-200 antialiased bg-[#FAF8F5] dark:bg-slate-950 min-h-screen flex flex-col justify-center items-center px-4 pt-[calc(env(safe-area-inset-top,0px)+3rem)] pb-12 selection:bg-brand-100 selection:text-brand-900 dark:selection:bg-brand-900/30 dark:selection:text-brand-300">
        <div class="max-w-md w-full text-center space-y-6">
            <!-- Brand Logo -->
            <div class="flex flex-col items-center gap-2">
                <a href="/" class="flex flex-col items-center gap-2 group transition-transform duration-200 hover:scale-[1.02]">
                    <img src="{{ asset('images/kalo sara.jpeg') }}" alt="Kalo Sara Logo" class="h-16 w-16 rounded-full object-cover border-2 border-brand-500 shadow-md">
                    <div class="flex flex-col items-center mt-1">
                        <span class="text-xs font-semibold tracking-wider uppercase text-brand-700 dark:text-brand-400">Penerjemah</span>
                        <span class="font-serif text-xl font-bold tracking-tight text-slate-900 dark:text-white leading-none">Tolaki</span>
                    </div>
                </a>
            </div>

            <!-- Error Details -->
            <div class="space-y-3">
                <div class="font-serif text-7xl sm:text-8xl font-black text-brand-700 dark:text-brand-600 tracking-tighter leading-none">
                    @yield('code')
                </div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                    @yield('message')
                </h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm max-w-xs mx-auto leading-relaxed">
                    @yield('description')
                </p>
            </div>

            <!-- Action Button -->
            <div class="pt-2">
                <a href="/" class="inline-flex items-center gap-2 rounded-xl bg-brand-700 px-6 py-3 text-sm font-semibold text-white hover:bg-brand-800 dark:bg-brand-600 dark:hover:bg-brand-700 shadow-md shadow-brand-700/10 dark:shadow-none transition-all duration-150 cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </body>
</html>
