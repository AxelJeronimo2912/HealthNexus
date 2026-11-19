<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AsistenteService
{
    public static function responder(
        array $mensajes,
        ?string $systemPrompt = null,
        bool $sinSentido = false,
        int $contadorSinSentido = 0
    ): ?string {
        $apiKey = config('services.groq.key');
        $model  = config('services.groq.model', 'llama-3.3-70b-versatile');
        $url    = config('services.groq.url', 'https://api.groq.com/openai/v1');

        if (!$apiKey) {
            return 'Error de configuración: falta la clave API de Groq.';
        }

        $user = auth()->user();
        $contextoUsuario = '';
        if ($user) {
            $contextoUsuario = "\n\nCONTEXTO DEL USUARIO ACTUAL:\n" .
                "- Nombre: {$user->nombre_completo}\n" .
                "- Rol: " . ($user->getRoleNames()->first() ?? 'sin rol') . "\n";
        }

        // 1. Obtener el último mensaje del usuario
        $ultimoMensajeUsuario = '';
        foreach (array_reverse($mensajes) as $m) {
            if ($m['role'] === 'user') {
                $ultimoMensajeUsuario = $m['content'];
                break;
            }
        }

        // 2. Instrucción dinámica
        $instruccionContexto = "\n\n### INSTRUCCIÓN CRÍTICA ###\n" .
                               "El usuario ACABA DE PREGUNTAR: \"{$ultimoMensajeUsuario}\".\n" .
                               "REGLA DE ORO: Responde ÚNICAMENTE a esta pregunta. Ignora cualquier tema anterior.\n";

        // =========================================================
        // ENRUTADOR MANUAL INTELIGENTE (CON MÁS PALABRAS CLAVE)
        // =========================================================
        $toolForzada = null;
        $argsForzados = null;
        $textoMin = mb_strtolower($ultimoMensajeUsuario);
        $esPdf = str_contains($textoMin, 'pdf') || str_contains($textoMin, 'reporte');

        // --- CAMAS ---
        if (str_contains($textoMin, 'cama')) {
            if ($esPdf) {
                $toolForzada = 'generar_pdf_camas';
            } else {
                if (str_contains($textoMin, 'ocupad') || str_contains($textoMin, 'disponible') || str_contains($textoMin, 'muestra') || str_contains($textoMin, 'list')) {
                    $toolForzada = 'listar_camas';
                    if (str_contains($textoMin, 'ocupad')) $argsForzados = ['estado' => 'ocupada'];
                    elseif (str_contains($textoMin, 'disponible')) $argsForzados = ['estado' => 'disponible'];
                } else {
                    $toolForzada = 'camas_disponibles';
                }
            }
        } 
        // --- CITAS ---
        elseif (str_contains($textoMin, 'cita')) {
            if ($esPdf) {
                $toolForzada = 'generar_pdf_citas_dia';
            } else {
                if (str_contains($textoMin, 'list') || str_contains($textoMin, 'muestra')) {
                    $toolForzada = 'listar_citas';
                } else {
                    $toolForzada = 'citas_hoy';
                }
            }
        } 
        // --- MEDICAMENTOS (Incluye catálogo) ---
        elseif (str_contains($textoMin, 'medicamento') || str_contains($textoMin, 'farmaco') || str_contains($textoMin, 'catalogo')) {
            if ($esPdf) {
                if (str_contains($textoMin, 'bajo') || str_contains($textoMin, 'poco')) {
                    $toolForzada = 'generar_pdf_inventario_bajo';
                } else {
                    $toolForzada = 'generar_pdf_medicamentos_stock_alto';
                }
            } else {
                if (str_contains($textoMin, 'bajo') || str_contains($textoMin, 'poco')) {
                    $toolForzada = 'medicamentos_stock_bajo';
                } elseif (str_contains($textoMin, 'alto')) {
                    $toolForzada = 'medicamentos_stock_alto';
                } elseif (str_contains($textoMin, 'caduc')) {
                    $toolForzada = 'medicamentos_proximos_caducar';
                } else {
                    $toolForzada = 'listar_medicamentos';
                }
            }
        } 
        // --- PACIENTES ---
        elseif (str_contains($textoMin, 'paciente')) {
            $toolForzada = $esPdf ? 'generar_pdf_pacientes' : 'listar_pacientes';
        }
        // --- MÉDICOS / PERSONAL / TURNOS ---
        elseif (str_contains($textoMin, 'medico') || str_contains($textoMin, 'médico') || str_contains($textoMin, 'doctor')) {
            if (str_contains($textoMin, 'activo') || str_contains($textoMin, 'cuantos')) {
                $toolForzada = 'medicos_activos';
            } else {
                $toolForzada = 'listar_personal';
                $argsForzados = ['rol' => 'médico'];
            }
        }
        elseif (str_contains($textoMin, 'trabaja') || str_contains($textoMin, 'turno') || str_contains($textoMin, 'quien esta')) {
            $toolForzada = 'turnos_de_hoy';
        }
        // --- ADMISIONES ---
        elseif (str_contains($textoMin, 'admision') || str_contains($textoMin, 'ingreso')) {
            if (str_contains($textoMin, 'hoy') || str_contains($textoMin, 'cuantas')) {
                $toolForzada = 'admisiones_de_hoy';
            } else {
                $toolForzada = 'listar_admisiones';
            }
        }
        // --- ESTADÍSTICAS ---
        elseif (str_contains($textoMin, 'estadistica') || str_contains($textoMin, 'resumen')) {
            $toolForzada = 'estadisticas_generales';
        }
        // =========================================================

        $payloadMessages = [
            [
                'role'    => 'system',
                'content' => ($systemPrompt ?? self::systemPromptPorDefecto()) . $contextoUsuario . $instruccionContexto,
            ]
        ];

        // =========================================================
        // SI HAY HERRAMIENTA FORZADA, BORRAMOS EL HISTORIAL POR COMPLETO
        // =========================================================
        if ($toolForzada) {
            // Solo enviamos el mensaje actual. Cero contexto anterior.
            $payloadMessages[] = ['role' => 'user', 'content' => $ultimoMensajeUsuario];
            
            // Inyectamos los argumentos forzados si existen
            if ($argsForzados) {
                $payloadMessages[0]['content'] .= "\nARGUMENTOS OBLIGATORIOS PARA LA HERRAMIENTA: " . json_encode($argsForzados);
            }
        } else {
            // Si no hay herramienta forzada (pregunta general), enviamos el historial normal
            foreach ($mensajes as $m) {
                $payloadMessages[] = $m;
            }
        }

        // MODO HUMOR
        if ($sinSentido) {
            $payloadMessages[0]['content'] .= self::instruccionHumor($contadorSinSentido);
            $respuesta = self::llamarApi($url, $apiKey, $model, $payloadMessages, false);
            return $respuesta['choices'][0]['message']['content']
                ?? 'Mi estetoscopio no detecta esa pregunta. ¿Probamos con algo del hospital?';
        }

        // LLAMADA A GROQ
        $respuesta = self::llamarApi($url, $apiKey, $model, $payloadMessages, true, false, 0, $toolForzada);

        // MODO RESPALDO (FALLBACK)
        if (!$respuesta && $toolForzada) {
            Log::warning("Groq falló. Activando Modo Respaldo para: {$toolForzada}");
            $resultado = AsistenteTools::ejecutar($toolForzada, $argsForzados ?? []);
            
            if (isset($resultado['error'])) {
                return "Error al obtener los datos: " . $resultado['error'];
            }

            if (isset($resultado['url'])) {
                return "He generado el reporte. Puedes descargarlo aquí: " . $resultado['url'];
            }

            $textoRespaldo = "Aquí tienes la información solicitada (Modo Respaldo):\n\n";
            foreach ($resultado as $key => $value) {
                if (is_array($value)) {
                    $textoRespaldo .= ucfirst($key) . ":\n";
                    foreach ($value as $item) {
                        $textoRespaldo .= "- " . (is_array($item) ? implode(', ', array_map(fn($k, $v) => "$k: $v", array_keys($item), $item)) : $item) . "\n";
                    }
                } else {
                    $textoRespaldo .= ucfirst($key) . ": " . $value . "\n";
                }
            }
            return trim($textoRespaldo);
        }

        if (!$respuesta) {
            return 'No se pudo contactar al asistente (Error de API).';
        }

        $toolCalls = $respuesta['choices'][0]['message']['tool_calls'] ?? null;

        if (!$toolCalls || count($toolCalls) === 0) {
            return $respuesta['choices'][0]['message']['content'] 
                ?? 'El asistente no generó una respuesta.';
        }

        // EJECUTAMOS HERRAMIENTAS
        foreach ($toolCalls as $toolCall) {
            $nombreTool = $toolCall['function']['name'];
            $argumentos = json_decode($toolCall['function']['arguments'] ?? '{}', true) ?? [];

            Log::info("Groq solicita herramienta: {$nombreTool}", $argumentos);
            $resultado = AsistenteTools::ejecutar($nombreTool, $argumentos);

            $payloadMessages[] = [
                'role'       => 'assistant',
                'content'    => null,
                'tool_calls' => [$toolCall],
            ];

            $payloadMessages[] = [
                'role'         => 'tool',
                'tool_call_id' => $toolCall['id'],
                'content'      => json_encode($resultado, JSON_UNESCAPED_UNICODE),
            ];
        }

        // SEGUNDA LLAMADA: Forzamos texto
        $respuesta2 = self::llamarApi($url, $apiKey, $model, $payloadMessages, true, true, 0, null);

        if ($respuesta2) {
            $textoFinal = $respuesta2['choices'][0]['message']['content'] ?? '';
            if (trim($textoFinal) === '') {
                return 'He generado la información solicitada, pero el asistente no pudo redactar un resumen.';
            }
            return $textoFinal;
        }

        return 'Se ejecutaron las acciones, pero el asistente falló al generar la respuesta final.';
    }

    private static function llamarApi(
        string $url, 
        string $apiKey, 
        string $model, 
        array $messages, 
        bool $conHerramientas,
        bool $forzarTexto = false, 
        int $intento = 0,
        ?string $toolForzada = null
    ): ?array {
        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0.1,
            'max_tokens' => 1024,
            'frequency_penalty' => 0.5,
            'presence_penalty' => 0.5,
        ];

        $herramientas = $conHerramientas ? AsistenteTools::definiciones() : [];
        
        if ($herramientas) {
            $payload['tools'] = $herramientas;
            
            if ($forzarTexto) {
                $payload['tool_choice'] = 'none';
            } elseif ($toolForzada) {
                $payload['tool_choice'] = [
                    'type' => 'function',
                    'function' => ['name' => $toolForzada]
                ];
            } else {
                $payload['tool_choice'] = 'auto'; 
            }
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post(rtrim($url, '/') . '/chat/completions', $payload);

            if ($response->status() === 429 && $intento < 3) {
                $body = $response->body();
                $espera = 3;
                if (preg_match('/try again in\s+([\d.]+)s/i', $body, $m)) {
                    $espera = (float) $m[1] + 1;
                }
                Log::warning("Groq Rate Limit alcanzado. Reintentando en {$espera} segundos...");
                sleep((int) ceil($espera));
                return self::llamarApi($url, $apiKey, $model, $messages, $conHerramientas, $forzarTexto, $intento + 1, $toolForzada);
            }

            if ($response->failed()) {
                Log::error('Groq API Error: ' . $response->body());
                return null;
            }

            return $response->json();

        } catch (\Throwable $e) {
            Log::error('Excepción de red en AsistenteService: ' . $e->getMessage());
            return null;
        }
    }

    private static function instruccionHumor(int $nivel): string
    {
        $nivel = min($nivel, 5);
        return match (true) {
            $nivel >= 3 => "\n\nMODO HUMOR ACTIVADO (nivel {$nivel}): el usuario lleva {$nivel} preguntas fuera de contexto. " .
                "Responde con un mensaje claramente divertido, burlón pero amable, sin groserías ni insultos reales. " .
                "Puedes usar frases como '¿Pero tú eres tonto?' SOLO si el tono es claramente de broma " .
                "y seguido de una invitación a volver al tema del hospital. Máximo 3 líneas. " .
                "Nunca uses esa frase si el usuario hizo una pregunta legítima del sistema. " .
                "Sin emojis. Sin símbolos. Solo texto plano.",
            $nivel === 2 => "\n\nMODO HUMOR SUAVE: segunda pregunta fuera de contexto. " .
                "Responde con un comentario ingenioso y breve, con humor hospitalario, sin groserías. " .
                "Sin emojis. Sin símbolos. Solo texto plano.",
            default => "\n\nMODO HUMOR LEVE: primera pregunta fuera de contexto. " .
                "Responde con un comentario breve, ingenioso y hospitalario, invitando a preguntar algo del sistema. " .
                "Sin emojis. Sin símbolos. Solo texto plano.",
        };
    }

    private static function systemPromptPorDefecto(): string
    {
        return <<<PROMPT
Eres el asistente virtual del sistema hospitalario HealthNexus, desarrollado en Laravel. Ayudas a médicos, enfermeros, farmacéuticos y personal administrativo.

REGLAS:
1. Cuando te pregunten por datos REALES del sistema (pacientes, citas, camas, medicamentos, turnos, admisiones, médicos), USA las herramientas disponibles. NUNCA inventes números.
2. Si la pregunta es general o conceptual (ej. '¿qué es HealthNexus?'), responde sin usar herramientas.
3. Si una herramienta devuelve un error, informa al usuario claramente.
4. Responde siempre en ESPAÑOL, de forma clara, profesional y concisa.
5. Usa lenguaje hospitalario apropiado.
6. Si te piden datos de un paciente específico, primero usa 'buscar_paciente' para encontrarlo.
7. Si te piden el historial médico, usa 'historial_consultas_paciente' con el ID obtenido.
8. Cuando des datos, agrega una interpretación útil (ej. 'Esto representa el 75% de ocupación').
9. Nunca reveles información sensible que no se te haya pedido explícitamente.
10. Si te preguntan por predicciones o "qué va a faltar", usa 'prediccion_agotamiento'.
11. Si te preguntan por tendencias o "qué se usa más", usa 'medicamentos_mayor_demanda'.
12. Si te piden generar un reporte o PDF, usa las herramientas 'generar_pdf_*'. Devuelve la URL del PDF generado al usuario para que pueda descargarlo.
13. Después de generar un PDF, indica claramente: "He generado el reporte. Puedes descargarlo aquí: [URL]".
14. Si la pregunta NO tiene relación con el sistema hospitalario (chistes, política, clima, recetas de cocina, quién ganó el partido, etc.), NO uses herramientas. En su lugar responde con un mensaje breve, ingenioso y con humor hospitalario.
15. Si el usuario insiste con preguntas sin sentido, sube el tono humorístico (pero nunca insultes de verdad, ni uses groserías, ni discrimines).
16. Después de 3 preguntas consecutivas sin sentido, responde con un mensaje claramente divertido y "burlón" pero amable, e invita al usuario a volver al tema del hospital.
17. NUNCA uses emojis en tus respuestas. Responde siempre en texto plano, sin símbolos, sin caritas y sin iconos.
18. Si te piden un listado general (de pacientes, citas, camas, medicamentos, personal, admisiones, médicos), usa las herramientas 'listar_*' o 'medicos_activos' correspondientes.
19. Si te piden estadísticas generales del hospital, usa 'estadisticas_generales'.
20. Si te piden medicamentos con stock alto, usa 'medicamentos_stock_alto'. Si te piden stock bajo, usa 'medicamentos_stock_bajo'. Si te piden los próximos a caducar, usa 'medicamentos_proximos_caducar'.
21. Si te piden el catálogo completo de medicamentos, usa 'listar_medicamentos'. Si te piden ver un medicamento específico, usa 'buscar_medicamento'.
22. Antes de decir "no tengo esa herramienta", REVISA la lista completa de herramientas disponibles.
23. ATENCIÓN EXTREMA AL CONTEXTO: Lee siempre el ÚLTIMO mensaje del usuario. Si el último mensaje pide algo diferente a los anteriores (ej. pasar de camas a medicamentos), DEBES usar la herramienta del nuevo tema. Bajo ninguna circunstancia repitas la respuesta de la pregunta anterior.
24. Si el usuario pide un "resumen", "estado", "reporte" o "expediente" de un paciente específico (ej. "dime el estado de Juan"), usa SIEMPRE la herramienta 'reporte_completo_paciente'.
25. Si el usuario proporciona un nombre con acentos (ej. Verónica) y no se encuentra, el sistema ya intentará buscarlo automáticamente. Si no lo encuentra, pídele al usuario el CURP o el ID del paciente.
26. MEMORIA Y CONTEXTO: Si el usuario te acaba de decir un dato (como un nombre, CURP o ID) y su siguiente mensaje dice "hazme el reporte completo", "de él", "del mismo" o similares, DEBES usar la herramienta 'reporte_completo_paciente' con el término de búsqueda que se mencionó en el mensaje anterior. NO le vuelvas a pedir los datos si ya los dio hace un momento.
PROMPT;
    }
}