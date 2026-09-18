<script>
    function generarPassword() {
        const chars = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%";
        let pass = "";
        for (let i = 0; i < 12; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));
        document.getElementById('password').value = pass;
        document.getElementById('password_confirmation').value = pass;
        alert("Contraseña generada: " + pass + "\nCompártela de forma segura con el colaborador.");
    }

    const canvas = document.getElementById('firmaCanvas');
    const ctx = canvas.getContext('2d');
    const inputFirma = document.getElementById('firma_canvas_input');
    let dibujando = false;

    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.strokeStyle = '#111827';

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const x = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
        const y = (e.touches ? e.touches[0].clientY : e.clientY) - rect.top;
        return {
            x,
            y
        };
    }

    function iniciar(e) {
        dibujando = true;
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
        inputFirma.value = canvas.toDataURL('image/png');
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

    function limpiarFirma() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        inputFirma.value = "";
    }

    document.querySelector('form').addEventListener('submit', function() {
        if (!inputFirma.value) inputFirma.value = canvas.toDataURL('image/png');
    });
</script>

<script>
    // ---- Mostrar/ocultar PIN según rol ----
    document.addEventListener('DOMContentLoaded', function() {
        const rolSelect = document.getElementById('role_select');
        const pinContainer = document.getElementById('pin-container');
        const pinInput = document.getElementById('pin_input');

        if (!rolSelect || !pinContainer || !pinInput) return;

        const esEdicion = {{ $u ? 'true' : 'false' }};
        const yaTienePin = {{ $u && $u->pin ? 'true' : 'false' }};

        function rolEsMedico(valor) {
            const v = (valor || '').toLowerCase().trim();
            return v === 'medico' || v === 'médico' || v === 'm' || v.startsWith('medic');
        }

        function togglePin() {
            const esMedico = rolEsMedico(rolSelect.value);
            pinContainer.classList.toggle('hidden', !esMedico);

            if (esMedico) {
                if (!pinInput.value) {
                    if (!esEdicion || !yaTienePin) {
                        pinInput.value = generarPinAleatorio();
                    }
                }
            } else {
                pinInput.value = '';
            }
        }

        rolSelect.addEventListener('change', togglePin);
        togglePin();

        // ---- Generar PIN con mensaje de éxito ----
        window.generarPin = function() {
            pinInput.value = generarPinAleatorio();
            alert(' PIN generado exitosamente: ' + pinInput.value +
                '\n\nCompártelo con el médico de forma segura.');
        };

        // ---- Copiar PIN (con fallback para HTTP) ----
        window.copiarPin = function() {
            if (!pinInput.value) {
                alert(' Primero genera un PIN.');
                return;
            }

            const pin = pinInput.value;

            // Método moderno (requiere HTTPS o localhost)
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(pin).then(() => {
                    alert(' PIN copiado al portapapeles: ' + pin);
                }).catch(() => {
                    copiarFallback(pin);
                });
            } else {
                // Fallback para HTTP
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
                alert(' PIN copiado al portapapeles: ' + texto);
            } catch (err) {
                alert(' No se pudo copiar. PIN: ' + texto);
            }
            document.body.removeChild(textarea);
        }
    });

    // ---- Generador de PIN de 4 dígitos ----
    function generarPinAleatorio() {
        return String(Math.floor(1000 + Math.random() * 9000));
    }
</script>
