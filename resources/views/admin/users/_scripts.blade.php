<script>
    // 1. GENERADOR DE CONTRASEÑA
    function generarPassword() {
        const chars = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%";
        let pass = "";
        for (let i = 0; i < 12; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));
        document.getElementById('password').value = pass;
        document.getElementById('password_confirmation').value = pass;
        alert("Contraseña generada: " + pass + "\nCompártela de forma segura con el colaborador.");
    }

    // 2. CANVAS DE FIRMA
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('firmaCanvas');
        const inputFirma = document.getElementById('firma_canvas_input');

        if (!canvas || !inputFirma) return;

        const ctx = canvas.getContext('2d');
        let dibujando = false;
        let huboTrazo = false;

        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#111827';

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const x = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
            const y = (e.touches ? e.touches[0].clientY : e.clientY) - rect.top;
            return { x, y };
        }

        function iniciar(e) {
            dibujando = true;
            huboTrazo = true;
            const p = getPos(e);
            ctx.beginPath();
            ctx.moveTo(p.x, p.y);
            e.preventDefault();
        }

        function dibujar(e) {
            if (!dibujando) return;
            const p = getPos(e);
            ctx.lineTo(p.x, p.y);
            ctx.stroke();
            e.preventDefault();
        }

        function terminar() {
            dibujando = false;

            if (huboTrazo) {
                inputFirma.value = canvas.toDataURL('image/png');
            }
        }

        canvas.addEventListener('mousedown', iniciar);
        canvas.addEventListener('mousemove', dibujar);
        canvas.addEventListener('mouseup', terminar);
        canvas.addEventListener('mouseleave', terminar);

        canvas.addEventListener('touchstart', iniciar, {
            passive: false
        });

        canvas.addEventListener('touchmove', dibujar, {
            passive: false
        });

        canvas.addEventListener('touchend', terminar);

        window.limpiarFirma = function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            inputFirma.value = '';
            huboTrazo = false;
        };

        const form = canvas.closest('form');

        if (form) {
            form.addEventListener('submit', function() {
                if (huboTrazo && !inputFirma.value) {
                    inputFirma.value = canvas.toDataURL('image/png');
                }
            });
        }
    });

    // TOGGLE DE PIN Y CÉDULA SEGÚN ROL
    document.addEventListener('DOMContentLoaded', function() {
        const rolSelect = document.getElementById('role_select');
        const pinContainer = document.getElementById('pin-container');
        const pinInput = document.getElementById('pin_input');
        const cedulaInput = document.getElementById('cedula_input');
        const cedulaReq = document.getElementById('cedula-required');

        if (!rolSelect || !pinContainer || !pinInput) return;

        const esEdicion = {{ $u ? 'true' : 'false' }};
        const yaTienePin = {{ $u && $u->pin ? 'true' : 'false' }};

        function rolEsMedico(valor) {
            const v = (valor || '').toLowerCase().trim();
            return v === 'm' || v.startsWith('medic');
        }

        function togglePin() {
            const esMedico = rolEsMedico(rolSelect.value);

            pinContainer.classList.toggle('hidden', !esMedico);
            pinInput.required = esMedico;

            // Cédula obligatoria solo para médicos
            if (cedulaInput && cedulaReq) {
                cedulaInput.required = esMedico;
                cedulaReq.classList.toggle('hidden', !esMedico);
            }

            if (esMedico) {
                // En creación o edición sin PIN previo → autogenerar
                if (!pinInput.value && (!esEdicion || !yaTienePin)) {
                    pinInput.value = generarPinAleatorio();
                }
            } else {
                pinInput.value = '';
            }
        }

        rolSelect.addEventListener('change', togglePin);
        togglePin();

        // Generar PIN con mensaje de éxito
        window.generarPin = function() {
            pinInput.value = generarPinAleatorio();

            alert(
                'PIN generado exitosamente: ' + pinInput.value +
                '\n\nCompártelo con el médico de forma segura.'
            );
        };

        // Copiar PIN con fallback para HTTP
        window.copiarPin = function() {
            if (!pinInput.value) {
                alert('Primero genera un PIN.');
                return;
            }

            const pin = pinInput.value;

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(pin)
                    .then(() => {
                        alert('PIN copiado al portapapeles: ' + pin);
                    })
                    .catch(() => {
                        copiarFallback(pin);
                    });
            } else {
                copiarFallback(pin);
            }
        };

        function copiarFallback(texto) {
            const textarea = document.createElement('textarea');

            textarea.value = texto;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';

            document.body.appendChild(textarea);
            textarea.select();

            try {
                document.execCommand('copy');
                alert('PIN copiado al portapapeles: ' + texto);
            } catch (err) {
                alert('No se pudo copiar. PIN: ' + texto);
            }

            document.body.removeChild(textarea);
        }
    });

    // GENERADOR DE PIN DE 4 DÍGITOS
    function generarPinAleatorio() {
        return String(Math.floor(1000 + Math.random() * 9000));
    }
</script>