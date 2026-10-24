<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Notas de Enfermería
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Registro de notas y seguimiento de pacientes</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="abrirModalAdministrar()"
                    class="bg-white border border-pink-200 hover:bg-pink-50 text-pink-700 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transition-all shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    Administrar Medicamento
                </button>

                <button type="button" onclick="abrirModalNota('{{ route('enfermeria.notas.create') }}')"
                    class="bg-pink-600 hover:bg-pink-700 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transition-all shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Nota
                </button>
            </div>
        </div>
    </x-slot>

    @php
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-pink-500 focus:ring-0 outline-none';
        $labelCls = 'block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5';
        $thCls = 'px-5 py-3.5 text-xs font-bold text-slate-400 uppercase tracking-wider';
    @endphp

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- STATS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-card">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total notas</p>
                <p class="text-3xl font-black text-slate-800 tracking-tight mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-card">
                <p class="text-xs font-bold text-pink-600/70 uppercase tracking-wider">Hoy</p>
                <p class="text-3xl font-black text-pink-600 tracking-tight mt-1">{{ $stats['hoy'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-card">
                <p class="text-xs font-bold text-brand-600/70 uppercase tracking-wider">Esta semana</p>
                <p class="text-3xl font-black text-brand-600 tracking-tight mt-1">{{ $stats['esta_semana'] }}</p>
            </div>
        </div>

        {{-- FILTROS --}}
        <div class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-card p-5">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                <div class="md:col-span-2">
                    <label class="{{ $labelCls }}">Buscar paciente</label>
                    <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Nombre o apellidos"
                        class="{{ $inputCls }}">
                </div>
                <div>
                    <label class="{{ $labelCls }}">Paciente</label>
                    <select name="paciente_id" class="{{ $inputCls }}">
                        <option value="">Todos</option>
                        @foreach ($pacientes as $p)
                            <option value="{{ $p->id }}" @selected($pacienteId == $p->id)>
                                {{ $p->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $labelCls }}">Desde</label>
                    <input type="date" name="desde" value="{{ $desde }}" class="{{ $inputCls }}">
                </div>
                <div>
                    <label class="{{ $labelCls }}">Hasta</label>
                    <input type="date" name="hasta" value="{{ $hasta }}" class="{{ $inputCls }}">
                </div>
                <div class="md:col-span-5 flex flex-wrap gap-2 pt-2 border-t border-slate-100">
                    <button type="submit"
                        class="px-5 py-2.5 bg-pink-600 hover:bg-pink-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                        Filtrar
                    </button>
                    <a href="{{ route('enfermeria.notas.index') }}"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        {{-- TABLA --}}
        <div class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Notas registradas</h3>
                <p class="text-xs text-slate-400">{{ $notas->total() }} notas en total</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="{{ $thCls }} text-left">Fecha</th>
                            <th class="{{ $thCls }} text-left">Paciente</th>
                            <th class="{{ $thCls }} text-left">Estado</th>
                            <th class="{{ $thCls }} text-left">Nota</th>
                            <th class="{{ $thCls }} text-left">Enfermero/a</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($notas as $nota)
                            <tr class="hover:bg-pink-50/30 transition-colors">
                                <td class="px-5 py-3.5 text-xs font-medium text-slate-400 whitespace-nowrap">
                                    {{ $nota->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-bold text-slate-800">
                                        {{ $nota->paciente?->nombre_completo ?? '—' }}</p>
                                    @if ($nota->cama)
                                        <span
                                            class="inline-block mt-1 px-2 py-0.5 bg-pink-50 text-pink-600 rounded-full text-xs font-bold">
                                            Cama: {{ $nota->cama->codigo }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($nota->estado_paciente)
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $nota->estado_color }}">
                                            {{ ucfirst($nota->estado_paciente) }}
                                        </span>
                                    @else
                                        <span class="text-slate-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-medium text-slate-600">
                                        {{ Str::limit($nota->contenido, 60) }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-medium text-slate-600">
                                        {{ $nota->user?->nombre_completo ?? '—' }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex justify-end gap-2">
                                        <button type="button"
                                            onclick="abrirModalVerNota('{{ route('enfermeria.notas.show', $nota) }}')"
                                            class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-all">
                                            Ver
                                        </button>
                                        @if ($nota->user_id === auth()->id() || auth()->user()->hasRole('administrador'))
                                            <form action="{{ route('enfermeria.notas.destroy', $nota) }}"
                                                method="POST" onsubmit="return confirm('¿Eliminar esta nota?')">
                                                @csrf @method('DELETE')
                                                <button
                                                    class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-bold transition-all">
                                                    Eliminar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <p class="text-xs font-semibold text-slate-400">Sin notas de enfermería
                                        registradas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $notas->links() }}</div>
    </div>

    {{-- MODAL DE NUEVA NOTA --}}
    <template x-teleport="body">
        <div id="modal-nota-create" x-data="{ open: false }" x-show="open" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div @click.away="open = false"
                class="bg-white rounded-2xl max-w-3xl w-full p-6 shadow-xl border border-slate-100 max-h-[90vh] overflow-y-auto relative">
                <div id="modal-nota-create-content">
                    <div class="p-10 text-center text-slate-400">Cargando formulario...</div>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL DE VER NOTA --}}
    <template x-teleport="body">
        <div id="modal-nota-show" x-data="{ open: false }" x-show="open" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div @click.away="open = false"
                class="bg-white rounded-2xl max-w-3xl w-full shadow-xl border border-slate-100 max-h-[90vh] overflow-y-auto relative custom-scrollbar">
                <div id="modal-nota-show-content">
                    <div class="p-10 text-center text-slate-400">Cargando nota...</div>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL DE ADMINISTRAR --}}
    <template x-teleport="body">
        <div id="modal-administrar" x-data="{ open: false }" x-show="open" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div @click.away="open = false"
                class="bg-white rounded-2xl max-w-xl w-full shadow-xl border border-slate-100 max-h-[90vh] overflow-y-auto relative custom-scrollbar">
                <div id="modal-administrar-content">
                    <div class="p-10 text-center text-slate-400">Cargando formulario...</div>
                </div>
            </div>
        </div>
    </template>

    <script>
        // ==========================================
        // LÓGICA PARA EL MODAL DE VER NOTA
        // ==========================================
        function abrirModalVerNota(url) {
            const modalElement = document.getElementById('modal-nota-show');
            Alpine.$data(modalElement).open = true;
            const contenedor = document.getElementById('modal-nota-show-content');
            contenedor.innerHTML =
                '<div class="p-10 text-center text-slate-400"><svg class="animate-spin h-6 w-6 mx-auto text-pink-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';
            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(html => contenedor.innerHTML = html);
        }

        // ==========================================
        // LÓGICA PARA CREAR NUEVA NOTA
        // ==========================================
        function abrirModalNota(url) {
            const modalElement = document.getElementById('modal-nota-create');
            Alpine.$data(modalElement).open = true;
            const contenedor = document.getElementById('modal-nota-create-content');
            contenedor.innerHTML =
                '<div class="p-10 text-center text-slate-400"><svg class="animate-spin h-6 w-6 mx-auto text-pink-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';
            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(html => contenedor.innerHTML = html);
        }

        // Esta función se ejecuta cuando el formulario se inyecta por AJAX
        window.guardarNota = function(form) {
            const btn = document.getElementById('btn-submit-nota');
            const txtOriginal = btn.innerHTML;
            btn.innerHTML = 'Guardando...';
            btn.disabled = true;

            const formData = new FormData(form);

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) {
                        // Si hay errores de validación, mostramos el mensaje
                        let mensaje = data.message || 'Error al guardar';
                        if (data.errors) {
                            const primerError = Object.values(data.errors)[0][0];
                            mensaje = primerError;
                        }
                        throw new Error(mensaje);
                    }
                    return data;
                })
                .then(data => {
                    if (data.success) {
                        Alpine.$data(document.getElementById('modal-nota-create')).open = false;
                        window.location.reload();
                    }
                })
                .catch(err => {
                    alert(err.message); // Muestra el error en un alert
                    btn.innerHTML = txtOriginal;
                    btn.disabled = false;
                });
        };

        // ==========================================
        // LÓGICA PARA ADMINISTRAR MEDICAMENTO
        // ==========================================
        function abrirModalAdministrar() {
            const modalElement = document.getElementById('modal-administrar');
            Alpine.$data(modalElement).open = true;
            const contenedor = document.getElementById('modal-administrar-content');
            contenedor.innerHTML =
                '<div class="p-10 text-center text-slate-400"><svg class="animate-spin h-6 w-6 mx-auto text-pink-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';

            fetch('{{ route('enfermeria.notas.create') }}?modal=administrar', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(html => contenedor.innerHTML = html);
        }

        window.guardarMedicamento = function(form) {
            const btn = document.getElementById('btn-submit-administrar');
            const txtOriginal = btn.innerHTML;
            btn.innerHTML = 'Procesando...';
            btn.disabled = true;

            const formData = new FormData(form);

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) {
                        let mensaje = data.message || 'Error al administrar';
                        if (data.errors) {
                            mensaje = Object.values(data.errors)[0][0];
                        }
                        throw new Error(mensaje);
                    }
                    return data;
                })
                .then(data => {
                    if (data.success) {
                        Alpine.$data(document.getElementById('modal-administrar')).open = false;
                        abrirModalVerNota(data.redirect);
                        setTimeout(() => window.location.reload(), 1500);
                    }
                })
                .catch(err => {
                    alert(err.message);
                    btn.innerHTML = txtOriginal;
                    btn.disabled = false;
                });
        };
    </script>
</x-app-layout>

