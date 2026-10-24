@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block rounded-xl border-0 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-brand-500 disabled:bg-slate-50 disabled:text-slate-500 transition']) }}>
