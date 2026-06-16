@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200 focus:border-brand-500 dark:focus:border-brand-500 focus:ring-brand-500 dark:focus:ring-brand-500 rounded-xl shadow-2xs transition-all duration-150']) }}>
