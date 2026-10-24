<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 rounded-xl font-semibold text-sm text-white shadow-sm hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 active:scale-[.98] transition']) }}>
    {{ $slot }}
</button>
