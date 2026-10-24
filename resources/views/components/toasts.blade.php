{{--
    Notificaciones flotantes. Lee los mensajes flash de la sesión
    (success, error, warning, status) y expone window.hnToast(mensaje, tipo)
    para usarlo desde JavaScript.
--}}
@php
    $flash = collect(['success', 'error', 'warning', 'status'])
        ->filter(fn ($k) => session()->has($k) && is_string(session($k)))
        ->map(fn ($k) => ['tipo' => $k === 'status' ? 'success' : $k, 'mensaje' => session($k)])
        ->values();
@endphp

<div x-data="{
        items: [],
        n: 0,
        push(mensaje, tipo = 'success') {
            const id = ++this.n;
            this.items.push({ id, mensaje, tipo });
            setTimeout(() => this.remove(id), tipo === 'error' ? 7000 : 4500);
        },
        remove(id) { this.items = this.items.filter(i => i.id !== id); },
        init() {
            window.hnToast = (m, t) => this.push(m, t);
            @js($flash).forEach(f => this.push(f.mensaje, f.tipo));
        }
    }"
    class="pointer-events-none fixed bottom-4 right-4 z-[100] flex w-[calc(100%-2rem)] max-w-sm flex-col gap-2"
    aria-live="polite">
    <template x-for="t in items" :key="t.id">
        <div class="pointer-events-auto flex items-start gap-3 rounded-xl bg-white p-3.5 pr-2.5 shadow-pop ring-1 ring-slate-200 animate-toast-in">
            <span class="mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-full"
                :class="{
                    'bg-emerald-50 text-emerald-600': t.tipo === 'success',
                    'bg-red-50 text-red-600': t.tipo === 'error',
                    'bg-amber-50 text-amber-600': t.tipo === 'warning'
                }">
                <template x-if="t.tipo === 'success'"><x-heroicon-m-check class="h-4 w-4" /></template>
                <template x-if="t.tipo === 'error'"><x-heroicon-m-x-mark class="h-4 w-4" /></template>
                <template x-if="t.tipo === 'warning'"><x-heroicon-m-exclamation-triangle class="h-4 w-4" /></template>
            </span>
            <p class="flex-1 pt-0.5 text-sm text-slate-700" x-text="t.mensaje"></p>
            <button type="button" @click="remove(t.id)" class="rounded-md p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600" aria-label="Cerrar">
                <x-heroicon-m-x-mark class="h-4 w-4" />
            </button>
        </div>
    </template>
</div>
