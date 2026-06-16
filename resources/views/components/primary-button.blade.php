<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-brand-700 dark:bg-brand-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-800 dark:hover:bg-brand-700 active:bg-brand-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition ease-in-out duration-150 cursor-pointer shadow-sm']) }}>
    {{ $slot }}
</button>
