<script>
    document.addEventListener('DOMContentLoaded', function() {
        const campos = [
            'temperatura', 'frecuencia_cardiaca', 'frecuencia_respiratoria',
            'presion_arterial', 'saturacion_oxigeno', 'glucosa', 'escala_dolor'
        ];

        const preview = document.getElementById('triage-preview');
        const valor = document.getElementById('triage-valor');
        const descripcion = document.getElementById('triage-descripcion');
        const manualCheck = document.getElementById('triage_manual');
        const selectContainer = document.getElementById('triage-select-container');

        const labels = {
            rojo: {
                texto: '🔴 Rojo — Emergencia',
                desc: 'Requiere atención inmediata.',
                color: 'border-red-400 bg-red-50'
            },
            naranja: {
                texto: '🟠 Naranja — Muy urgente',
                desc: 'Debe atenderse rápidamente.',
                color: 'border-orange-400 bg-orange-50'
            },
            amarillo: {
                texto: '🟡 Amarillo — Urgente',
                desc: 'Urgente, pero puede esperar cierto tiempo.',
                color: 'border-yellow-400 bg-yellow-50'
            },
            verde: {
                texto: '🟢 Verde — No urgente',
                desc: 'No urgente. Puede esperar.',
                color: 'border-green-400 bg-green-50'
            },
            azul: {
                texto: '🔵 Azul — Baja prioridad',
                desc: 'Atención de baja prioridad.',
                color: 'border-blue-400 bg-blue-50'
            },
        };

        function calcularTriage() {
            const d = {};
            campos.forEach(c => {
                const el = document.getElementById(c);
                if (el && el.value !== '') d[c] = el.value;
            });

            // Mismo algoritmo que el backend
            let puntos = 0;
            const num = v => parseFloat(v);

            if (d.temperatura !== undefined) {
                const t = num(d.temperatura);
                if (t >= 40 || t < 35) puntos += 4;
                else if (t >= 39 || t < 36) puntos += 2;
                else if (t >= 38) puntos += 1;
            }
            if (d.frecuencia_cardiaca !== undefined) {
                const fc = num(d.frecuencia_cardiaca);
                if (fc > 130 || fc < 40) puntos += 4;
                else if (fc > 110 || fc < 50) puntos += 2;
                else if (fc > 100) puntos += 1;
            }
            if (d.frecuencia_respiratoria !== undefined) {
                const fr = num(d.frecuencia_respiratoria);
                if (fr > 30 || fr < 8) puntos += 4;
                else if (fr > 24 || fr < 10) puntos += 2;
                else if (fr > 20) puntos += 1;
            }
            if (d.saturacion_oxigeno !== undefined) {
                const s = num(d.saturacion_oxigeno);
                if (s < 85) puntos += 4;
                else if (s < 90) puntos += 3;
                else if (s < 94) puntos += 1;
            }
            if (d.presion_arterial && d.presion_arterial.includes('/')) {
                const [sis, dia] = d.presion_arterial.split('/').map(num);
                if (sis >= 180 || sis < 80 || dia >= 120 || dia < 50) puntos += 4;
                else if (sis >= 160 || sis < 90 || dia >= 100 || dia < 60) puntos += 2;
                else if (sis >= 140 || dia >= 90) puntos += 1;
            }
            if (d.glucosa !== undefined) {
                const g = num(d.glucosa);
                if (g > 400 || g < 50) puntos += 4;
                else if (g > 250 || g < 70) puntos += 2;
                else if (g > 180) puntos += 1;
            }
            if (d.escala_dolor !== undefined) {
                const p = num(d.escala_dolor);
                if (p >= 8) puntos += 3;
                else if (p >= 6) puntos += 2;
                else if (p >= 4) puntos += 1;
            }

            let nivel;
            if (puntos >= 12) nivel = 'rojo';
            else if (puntos >= 8) nivel = 'naranja';
            else if (puntos >= 5) nivel = 'amarillo';
            else if (puntos >= 2) nivel = 'verde';
            else nivel = 'azul';

            const info = labels[nivel];
            valor.textContent = info.texto;
            descripcion.textContent = info.desc;
            preview.className = 'mb-4 p-4 rounded border-2 ' + info.color;
        }

        campos.forEach(c => {
            const el = document.getElementById(c);
            if (el) el.addEventListener('input', calcularTriage);
        });

        if (manualCheck) {
            manualCheck.addEventListener('change', function() {
                selectContainer.classList.toggle('hidden', !this.checked);
            });
            selectContainer.classList.toggle('hidden', !manualCheck.checked);
        }

        calcularTriage();
    });
</script>
