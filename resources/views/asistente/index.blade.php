<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-full bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v6m3-3H9m10 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Asistente HealthNexus</h2>
                    <p class="text-xs text-teal-600 font-medium">En línea</p>
                </div>
            </div>
            <button id="btn-nueva-conv"
                class="text-xs text-teal-600 hover:text-teal-800 transition-colors px-3 py-1.5 rounded-md hover:bg-teal-50 font-medium">
                + Nueva conversación
            </button>
        </div>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- SIDEBAR HISTORIAL --}}
            <aside
                class="md:col-span-1 bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden flex flex-col"
                style="height: 75vh;">
                <div class="p-3 border-b border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Historial</h3>
                </div>
                <div id="lista-conversaciones" class="flex-1 overflow-y-auto p-2 space-y-1">
                    <p class="text-xs text-gray-400 text-center py-4">Cargando...</p>
                </div>
            </aside>

            {{-- CHAT --}}
            <div class="md:col-span-3 bg-white rounded-xl shadow-md overflow-hidden flex flex-col border border-gray-100"
                style="height: 75vh;">

                <div id="chat-mensajes" class="flex-1 overflow-y-auto p-4 space-y-4"
                    style="background: linear-gradient(180deg, #F8FAFC 0%, #F1F5F9 100%);">
                    <div class="flex justify-start items-end gap-2">
                        <div
                            class="w-7 h-7 rounded-full bg-teal-600 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                            HN</div>
                        <div
                            class="max-w-[80%] px-4 py-2.5 rounded-2xl rounded-bl-sm text-sm bg-white border border-gray-100 text-gray-700 shadow-sm">
                            ¡Hola! Soy el asistente de HealthNexus. ¿En qué puedo ayudarte?
                        </div>
                    </div>
                </div>

                <div id="chat-typing" class="hidden px-4 pb-2 -mt-1">
                    <div class="flex items-center gap-2">
                        <div
                            class="w-7 h-7 rounded-full bg-teal-600 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                            HN</div>
                        <div
                            class="bg-white border border-gray-100 rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm flex gap-1">
                            <span class="w-1.5 h-1.5 bg-gray-300 rounded-full animate-bounce"
                                style="animation-delay: 0ms"></span>
                            <span class="w-1.5 h-1.5 bg-gray-300 rounded-full animate-bounce"
                                style="animation-delay: 150ms"></span>
                            <span class="w-1.5 h-1.5 bg-gray-300 rounded-full animate-bounce"
                                style="animation-delay: 300ms"></span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 p-4 bg-white">
                    <form id="chat-form" class="flex gap-2 items-center">
                        <input type="text" id="chat-input" placeholder="Escribe tu pregunta..."
                            class="flex-1 border-gray-200 bg-gray-50 rounded-full px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-teal-500 focus:bg-white transition-colors"
                            autocomplete="off">
                        <button type="submit" id="chat-enviar"
                            class="w-10 h-10 flex-shrink-0 flex items-center justify-center bg-teal-600 hover:bg-teal-700 disabled:bg-gray-300 text-white rounded-full transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6 12L3.269 3.126A59.77 59.77 0 0121.485 12 59.77 59.77 0 013.27 20.874L6 12zm0 0h7.5" />
                            </svg>
                        </button>
                    </form>
                    <p class="text-[11px] text-gray-400 mt-2 text-center">Este asistente no sustituye el consejo médico
                        profesional.</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        #chat-mensajes::-webkit-scrollbar,
        #lista-conversaciones::-webkit-scrollbar {
            width: 6px;
        }

        #chat-mensajes::-webkit-scrollbar-thumb,
        #lista-conversaciones::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 999px;
        }

        @keyframes mensajeEntrada {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .mensaje-nuevo {
            animation: mensajeEntrada 0.25s ease-out;
        }

        .conv-item {
            cursor: pointer;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 12px;
            color: #334155;
            transition: background 0.15s;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
        }

        .conv-item:hover {
            background: #F1F5F9;
        }

        .conv-item.activa {
            background: #CCFBF1;
            color: #0F766E;
            font-weight: 600;
        }

        .conv-titulo {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            min-width: 0;
        }

        .conv-borrar {
            flex-shrink: 0;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            color: #94A3B8;
            opacity: 0;
            transition: all 0.15s;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
        }

        .conv-item:hover .conv-borrar {
            opacity: 1;
        }

        .conv-borrar:hover {
            background: #FEE2E2;
            color: #DC2626;
        }

        .conv-borrar:active {
            transform: scale(0.9);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('chat-form');
            const input = document.getElementById('chat-input');
            const mensajes = document.getElementById('chat-mensajes');
            const btnEnviar = document.getElementById('chat-enviar');
            const typing = document.getElementById('chat-typing');
            const listaConv = document.getElementById('lista-conversaciones');
            const btnNueva = document.getElementById('btn-nueva-conv');

            let conversacionId = null;
            let contadorSinSentido = 0;

            const PALABRAS_FUERA_DE_CONTEXTO = [
                'chiste', 'broma', 'política', 'politica', 'presidente', 'fútbol', 'futbol',
                'clima', 'receta', 'cocina', 'amor', 'novia', 'novio', 'partido',
                'música', 'musica', 'película', 'pelicula', 'quién ganó', 'quien gano',
                'horóscopo', 'horoscopo', 'signo zodiacal'
            ];

            function esPreguntaSinSentido(texto) {
                const t = texto.toLowerCase().trim();
                if (t.length < 4) return true;
                return PALABRAS_FUERA_DE_CONTEXTO.some(p => t.includes(p));
            }

            function horaActual() {
                return new Date().toLocaleTimeString('es-ES', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            function escaparHTML(texto) {
                return texto
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
            }

            function agregarMensaje(texto, esUsuario, animar = true) {
                const fila = document.createElement('div');
                fila.className =
                    `flex items-end gap-2 ${esUsuario ? 'justify-end' : 'justify-start'} ${animar ? 'mensaje-nuevo' : ''}`;

                const pdfMatch = texto.match(/(https?:\/\/[^\s]+\.pdf)/);
                let contenidoHTML = escaparHTML(texto);

                if (pdfMatch) {
                    const url = pdfMatch[1];
                    contenidoHTML = contenidoHTML.replace(url,
                        `<a href="${url}" target="_blank"
                            class="inline-flex items-center gap-1 mt-2 px-3 py-1 bg-red-600 text-white rounded-full text-xs hover:bg-red-700 transition-colors">
                            📄 Descargar PDF
                        </a>`);
                }

                const avatar = !esUsuario ?
                    `<div class="w-7 h-7 rounded-full bg-teal-600 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">HN</div>` :
                    '';

                const burbuja = `
                    <div class="max-w-[80%] px-4 py-2.5 rounded-2xl text-sm whitespace-pre-line shadow-sm
                        ${esUsuario ? 'bg-teal-600 text-white rounded-br-sm' : 'bg-white border border-gray-100 text-gray-700 rounded-bl-sm'}">
                        ${contenidoHTML}
                        <div class="text-[10px] mt-1 ${esUsuario ? 'text-teal-100' : 'text-gray-400'}">${horaActual()}</div>
                    </div>
                `;

                fila.innerHTML = esUsuario ? burbuja : avatar + burbuja;
                mensajes.appendChild(fila);
                mensajes.scrollTop = mensajes.scrollHeight;
            }

            function limpiarChatVisual(mensajeBienvenida = true) {
                mensajes.innerHTML = mensajeBienvenida ?
                    `<div class="flex justify-start items-end gap-2">
                        <div class="w-7 h-7 rounded-full bg-teal-600 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">HN</div>
                        <div class="max-w-[80%] px-4 py-2.5 rounded-2xl rounded-bl-sm text-sm bg-white border border-gray-100 text-gray-700 shadow-sm">
                            ¡Hola! Soy el asistente de HealthNexus. ¿En qué puedo ayudarte?
                        </div>
                    </div>` :
                    '';
            }

            // ===== CARGAR LISTA DE CONVERSACIONES =====
            async function cargarConversaciones() {
                try {
                    const res = await fetch('{{ route('asistente.lista') }}', {
                        headers: {
                            'Accept': 'application/json'
                        },
                    });
                    const data = await res.json();

                    if (!data.length) {
                        listaConv.innerHTML =
                            '<p class="text-xs text-gray-400 text-center py-4">Sin conversaciones aún.</p>';
                        return;
                    }

                    listaConv.innerHTML = data.map(c => `
                        <div class="conv-item ${c.id === conversacionId ? 'activa' : ''}"
                             data-id="${c.id}"
                             title="${escaparHTML(c.titulo)}">
                            <span class="conv-titulo">${escaparHTML(c.titulo)}</span>
                            <button class="conv-borrar" data-borrar="${c.id}" title="Eliminar conversación">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3" />
                                </svg>
                            </button>
                        </div>
                    `).join('');

                    // Click en el item → cargar conversación
                    listaConv.querySelectorAll('.conv-item').forEach(el => {
                        el.addEventListener('click', (e) => {
                            if (e.target.closest('.conv-borrar')) return;
                            cargarConversacion(parseInt(el.dataset.id));
                        });
                    });

                    // Click en el botón borrar → eliminar
                    listaConv.querySelectorAll('.conv-borrar').forEach(btn => {
                        btn.addEventListener('click', async (e) => {
                            e.stopPropagation();
                            const id = parseInt(btn.dataset.borrar);
                            await eliminarConversacion(id);
                        });
                    });
                } catch (e) {
                    listaConv.innerHTML =
                        '<p class="text-xs text-red-400 text-center py-4">Error al cargar.</p>';
                }
            }

            // ===== CARGAR UNA CONVERSACIÓN =====
            async function cargarConversacion(id) {
                try {
                    const res = await fetch(`/asistente/${id}`, {
                        headers: {
                            'Accept': 'application/json'
                        },
                    });
                    if (!res.ok) throw new Error('No encontrada');

                    const data = await res.json();
                    conversacionId = data.id;
                    contadorSinSentido = 0;

                    limpiarChatVisual(false);
                    data.mensajes.forEach(m => {
                        agregarMensaje(m.content, m.role === 'user', false);
                    });

                    // Resaltar activa
                    listaConv.querySelectorAll('.conv-item').forEach(el => {
                        el.classList.toggle('activa', parseInt(el.dataset.id) === conversacionId);
                    });
                } catch (e) {
                    console.error(e);
                }
            }

            // ===== ELIMINAR CONVERSACIÓN =====
            async function eliminarConversacion(id) {
                if (!confirm('¿Eliminar esta conversación? Esta acción no se puede deshacer.')) return;

                try {
                    const res = await fetch(`/asistente/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                    });

                    if (!res.ok) throw new Error('Error al eliminar');

                    // Si la conversación eliminada era la activa, resetear chat
                    if (conversacionId === id) {
                        conversacionId = null;
                        contadorSinSentido = 0;
                        limpiarChatVisual(true);
                    }

                    // Recargar sidebar
                    cargarConversaciones();
                } catch (e) {
                    alert('No se pudo eliminar la conversación.');
                    console.error(e);
                }
            }

            // ===== NUEVA CONVERSACIÓN =====
            btnNueva.addEventListener('click', () => {
                conversacionId = null;
                contadorSinSentido = 0;
                limpiarChatVisual(true);
                listaConv.querySelectorAll('.conv-item').forEach(el => el.classList.remove('activa'));
                input.focus();
            });

            // ===== ENVIAR MENSAJE =====
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const mensaje = input.value.trim();
                if (!mensaje) return;

                agregarMensaje(mensaje, true);
                input.value = '';
                btnEnviar.disabled = true;
                typing.classList.remove('hidden');
                mensajes.scrollTop = mensajes.scrollHeight;

                const sinSentido = esPreguntaSinSentido(mensaje);
                contadorSinSentido = sinSentido ? contadorSinSentido + 1 : 0;

                try {
                    const res = await fetch('{{ route('asistente.enviar') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            message: mensaje,
                            conversacion_id: conversacionId,
                            sin_sentido: sinSentido,
                            contador_sin_sentido: contadorSinSentido,
                        }),
                    });

                    const data = await res.json();
                    const respuesta = data.response || 'Sin respuesta.';

                    typing.classList.add('hidden');
                    agregarMensaje(respuesta, false);

                    if (!conversacionId && data.conversacion_id) {
                        conversacionId = data.conversacion_id;
                    }
                    cargarConversaciones();
                } catch (error) {
                    typing.classList.add('hidden');
                    agregarMensaje('Error al conectar con el asistente.', false);
                } finally {
                    btnEnviar.disabled = false;
                    input.focus();
                }
            });

            // ===== INICIALIZAR =====
            limpiarChatVisual(true);
            cargarConversaciones();
        });
    </script>
</x-app-layout>
