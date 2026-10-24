<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-brand-600 rounded-xl font-semibold text-sm text-white shadow-sm hover:bg-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 active:scale-[.98] disabled:opacity-50 transition']) }}>
    {{ $slot }}
</button>
