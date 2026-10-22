<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white rounded-xl font-semibold text-sm text-slate-700 shadow-sm ring-1 ring-inset ring-slate-200 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 disabled:opacity-50 transition']) }}>
    {{ $slot }}
</button>
