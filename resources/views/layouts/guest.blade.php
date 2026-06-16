<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Penerjemah Tolaki') }}</title>

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
    <body class="font-sans text-slate-800 dark:text-slate-200 antialiased bg-[#FAF8F5] dark:bg-slate-950 min-h-screen selection:bg-brand-100 selection:text-brand-900 dark:selection:bg-brand-900/30 dark:selection:text-brand-300">
        <div class="min-h-screen flex flex-col justify-center items-center py-12 pt-[calc(env(safe-area-inset-top,0px)+3.5rem)] bg-[#FAF8F5] dark:bg-slate-950 px-4">
            <div>
                <a href="/" wire:navigate class="flex flex-col items-center gap-2 group transition-transform duration-200 hover:scale-[1.02]">
                    <x-application-logo class="w-16 h-16" />
                    <div class="flex flex-col items-center mt-1">
                        <span class="text-xs font-semibold tracking-wider uppercase text-brand-700 dark:text-brand-400">Penerjemah</span>
                        <span class="font-serif text-xl font-bold tracking-tight text-slate-900 dark:text-white leading-none">Tolaki</span>
                    </div>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white dark:bg-slate-900 shadow-sm border border-brand-100/30 dark:border-slate-800/80 overflow-hidden sm:rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
