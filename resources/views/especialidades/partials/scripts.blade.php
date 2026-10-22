<script>
    // ============ Datos globales para los modales ============
    window.MEDICOS_MAP = {!! $medicosMapJson !!};
    window.SERVICIOS_MAP = {!! $serviciosMapJson !!};
    window.SERVICIOS_DISPONIBLES = {!! $serviciosDisponiblesJson !!};
    window.MEDICOS_DISPONIBLES = {!! $medicosDisponiblesJson !!};

    // ============ Abrir / Cerrar ============
    function openModal(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.add('hidden');
        if (document.querySelectorAll('.fixed.inset-0.z-50:not(.hidden)').length === 0) {
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            ['modal-create', 'modal-edit', 'modal-show', 'modal-medicos', 'modal-servicios']
            .forEach(closeModal);
        }
    });

    function parseEsp(el) {
        try {
            return JSON.parse(el.dataset.especialidad);
        } catch (e) {
            console.error('Error parseando data-especialidad:', e);
            return null;
        }
    }

    // ============ Modal EDITAR ============
    function openEditModal(el) {
        const esp = parseEsp(el);
        if (!esp) return;

        const form = document.getElementById('form-edit');
        form.action = `{{ url('especialidades') }}/${esp.id}`;

        const setVal = (id, val) => {
            const e = document.getElementById(id);
            if (e) e.value = val ?? '';
        };

        setVal('edit_especialidad_id', esp.id);
        document.getElementById('edit-subtitle').textContent = `Editando: ${esp.nombre}`;
        setVal('edit_codigo', esp.codigo);
        setVal('edit_nombre', esp.nombre);
        setVal('edit_descripcion', esp.descripcion);
        setVal('edit_grupo', esp.grupo || 'clinica');
        setVal('edit_duracion_consulta_default', esp.duracion_consulta_default || 30);

        const color = esp.color ? '#' + String(esp.color).replace('#', '') : '#3B82F6';
        setVal('edit_color', color);
        const picker = document.getElementById('edit_color_picker');
        if (picker) picker.value = color;

        const activo = document.getElementById('edit_activo');
        if (activo) activo.checked = esp.activo == 1 || esp.activo === true;

        openModal('modal-edit');
    }

    // ============ Modal SHOW ============
    function openShowModal(el) {
        const esp = parseEsp(el);
        if (!esp) return;

        const medicos = window.MEDICOS_MAP[esp.id] || [];
        const servicios = window.SERVICIOS_MAP[esp.id] || [];

        document.getElementById('show-title').textContent = `Especialidad: ${esp.nombre}`;
        document.getElementById('show-subtitle').textContent = 'Información completa';
        document.getElementById('show-codigo').textContent = esp.codigo || '';
        document.getElementById('show-nombre').textContent = esp.nombre || '';
        document.getElementById('show-descripcion').textContent = esp.descripcion || 'Sin descripción.';
        document.getElementById('show-duracion').textContent = (esp.duracion_consulta_default || 0) + ' min';
        document.getElementById('show-total-medicos').textContent = medicos.length;

        const colorSpan = document.getElementById('show-color');
        if (esp.color) {
            colorSpan.classList.remove('hidden');
            colorSpan.style.backgroundColor = '#' + String(esp.color).replace('#', '');
        } else {
            colorSpan.classList.add('hidden');
        }

        const grupoLabels = {
            clinica: 'Clínica',
            quirurgica: 'Quirúrgica',
            diagnostica: 'Diagnóstica',
            basica: 'Básica',
            otra: 'Otra'
        };
        document.getElementById('show-grupo').textContent = grupoLabels[esp.grupo] || esp.grupo;

        const estado = document.getElementById('show-estado');
        estado.textContent = esp.activo == 1 ? 'Activa' : 'Inactiva';
        estado.className = 'px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide ' +
            (esp.activo == 1 ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' :
                'bg-rose-50 text-rose-700 border border-rose-100');

        // Médicos
        const medicosList = document.getElementById('show-medicos-list');
        document.getElementById('show-medicos-count').textContent = medicos.length + ' médicos';
        medicosList.innerHTML = '';

        if (medicos.length === 0) {
            medicosList.innerHTML =
                '<li class="px-5 py-8 text-center"><p class="text-xs font-semibold text-slate-400">Sin médicos asignados.</p></li>';
        } else {
            medicos.forEach(m => {
                const nombre = m.nombre_completo || m.name || 'Sin nombre';
                const rol = (m.roles && m.roles.length) ? m.roles[0].name : (m.rol || 'sin rol');
                const pivot = m.pivot || {};
                const principal = pivot.es_principal ?
                    '<span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-100 rounded-full text-xs font-bold uppercase">Principal</span>' :
                    '';
                const cedula = pivot.numero_cedula_especialidad ?
                    `<p class="text-xs text-slate-400 mt-0.5">Cédula: ${pivot.numero_cedula_especialidad}</p>` :
                    '';
                medicosList.innerHTML += `
                    <li class="px-5 py-3.5 flex flex-wrap justify-between items-center gap-3">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-xs font-bold text-slate-800">${nombre}</p>
                                ${principal}
                            </div>
                            ${cedula}
                        </div>
                        <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-bold uppercase">${rol}</span>
                    </li>`;
            });
        }

        // Servicios
        const serviciosList = document.getElementById('show-servicios-list');
        document.getElementById('show-servicios-count').textContent = servicios.length + ' servicios';
        serviciosList.innerHTML = '';

        if (servicios.length === 0) {
            serviciosList.innerHTML = '<p class="text-xs font-semibold text-slate-400">Sin servicios asociados.</p>';
        } else {
            servicios.forEach(s => {
                serviciosList.innerHTML += `
                    <span class="px-3 py-1.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold">
                        ${s.nombre}
                    </span>`;
            });
        }

        document.getElementById('show-btn-editar').onclick = () => {
            closeModal('modal-show');
            openEditModal(el);
        };
        document.getElementById('show-btn-medicos').onclick = () => {
            closeModal('modal-show');
            openMedicosModal(el);
        };
        document.getElementById('show-btn-servicios').onclick = () => {
            closeModal('modal-show');
            openServiciosModal(el);
        };

        openModal('modal-show');
    }

    // ============ Modal MÉDICOS ============
    function openMedicosModal(el) {
        const esp = parseEsp(el);
        if (!esp) return;

        document.getElementById('medicos-subtitle').textContent = `Especialidad: ${esp.nombre}`;
        document.getElementById('form-asignar-medico').action = `{{ url('especialidades') }}/${esp.id}/medicos`;

        renderMedicosTable(esp.id);
        openModal('modal-medicos');
    }

    function renderMedicosTable(espId) {
        const medicos = window.MEDICOS_MAP[espId] || [];
        const tbody = document.getElementById('medicos-tbody');
        const emptyHint = document.getElementById('medicos-empty-hint');

        document.getElementById('medicos-count').textContent = medicos.length + ' médicos';
        tbody.innerHTML = '';

        // Mostrar/ocultar el hint de vacío
        if (emptyHint) emptyHint.classList.toggle('hidden', medicos.length > 0);

        if (medicos.length === 0) {
            tbody.innerHTML = '';
            return;
        }

        medicos.forEach(m => {
            const nombre = m.nombre_completo || m.name || 'Sin nombre';
            const email = m.email || '';
            const rol = (m.roles && m.roles.length) ? m.roles[0].name : (m.rol || 'sin rol');
            const pivot = m.pivot || {};
            const cedula = pivot.numero_cedula_especialidad ?
                `<span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-mono font-bold">${pivot.numero_cedula_especialidad}</span>` :
                '<span class="text-slate-300 text-xs">—</span>';
            const principal = pivot.es_principal ?
                '<span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-100 rounded-full text-xs font-bold uppercase">★ Principal</span>' :
                '<span class="text-slate-300 text-xs">—</span>';

            tbody.innerHTML += `
                <tr class="hover:bg-brand-50/30 transition-colors">
                    <td class="px-5 py-3.5">
                        <p class="text-xs font-bold text-slate-800">${nombre}</p>
                        <p class="text-xs font-medium text-slate-400 mt-0.5">${email}</p>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="px-2.5 py-1 bg-brand-50 text-brand-700 border border-brand-100 rounded-full text-xs font-bold uppercase">${rol}</span>
                    </td>
                    <td class="px-5 py-3.5">${cedula}</td>
                    <td class="px-5 py-3.5 text-center">${principal}</td>
                    <td class="px-5 py-3.5">
                        <div class="flex justify-end">
                            <form action="{{ url('especialidades') }}/${espId}/medicos/${pivot.id}" method="POST"
                                onsubmit="return confirm('¿Quitar este médico de la especialidad?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-bold transition-all">Quitar</button>
                            </form>
                        </div>
                    </td>
                </tr>`;
        });
    }

    // ============ Modal SERVICIOS ============
    function openServiciosModal(el) {
        const esp = parseEsp(el);
        if (!esp) return;

        document.getElementById('servicios-subtitle').textContent = `Especialidad: ${esp.nombre}`;
        document.getElementById('form-asignar-servicio').action = `{{ url('especialidades') }}/${esp.id}/servicios`;

        renderServiciosTable(esp.id);
        openModal('modal-servicios');
    }

    function renderServiciosTable(espId) {
        const servicios = window.SERVICIOS_MAP[espId] || [];
        const tbody = document.getElementById('servicios-tbody');
        document.getElementById('servicios-count').textContent = servicios.length + ' servicios';
        tbody.innerHTML = '';

        if (servicios.length === 0) {
            tbody.innerHTML =
                '<tr><td colspan="4" class="px-5 py-8 text-center text-xs font-semibold text-slate-400">Sin servicios asociados.</td></tr>';
            return;
        }

        servicios.forEach(s => {
            const pivot = s.pivot || {};
            const tipoLabel = s.tipo_label || s.tipo || '—';
            const ubicacion = s.ubicacion || '<span class="text-slate-300 text-xs">—</span>';

            tbody.innerHTML += `
                <tr class="hover:bg-brand-50/30 transition-colors">
                    <td class="px-5 py-3.5"><p class="text-xs font-bold text-slate-800">${s.nombre}</p></td>
                    <td class="px-5 py-3.5">
                        <span class="px-2.5 py-1 bg-accent-50 text-accent-700 border border-accent-100 rounded-full text-xs font-bold uppercase">${tipoLabel}</span>
                    </td>
                    <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-600">${ubicacion}</p></td>
                    <td class="px-5 py-3.5">
                        <div class="flex justify-end">
                            <form action="{{ url('especialidades') }}/${espId}/servicios/${pivot.id}" method="POST"
                                onsubmit="return confirm('¿Quitar este servicio de la especialidad?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-bold transition-all">Quitar</button>
                            </form>
                        </div>
                    </td>
                </tr>`;
        });
    }

    @if ($hasErrors)
        document.addEventListener('DOMContentLoaded', function() {
            @if ($isEditError)
                const espId = @json($oldEspId);
                if (espId) {
                    const form = document.getElementById('form-edit');
                    form.action = `{{ url('especialidades') }}/${espId}`;
                }
                const subtitle = document.getElementById('edit-subtitle');
                if (subtitle && @json($oldNombre)) {
                    subtitle.textContent = 'Editando: ' + @json($oldNombre);
                }
                openModal('modal-edit');
            @else
                openModal('modal-create');
            @endif
        });
    @endif
    
</script>
