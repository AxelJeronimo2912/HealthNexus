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

        $payloadMessages = [
            [
                'role'    => 'system',
                'content' => ($systemPrompt ?? self::systemPromptPorDefecto()) . $contextoUsuario,
            ]
        ];

        // Añadimos el mensaje actual (que es el único que viene en el array)
        foreach ($mensajes as $m) {
            $payloadMessages[] = $m;
        }

        if ($sinSentido) {
            $payloadMessages[0]['content'] .= self::instruccionHumor($contadorSinSentido);
            $respuesta = self::llamarApi($url, $apiKey, $model, $payloadMessages, false);
            return $respuesta['choices'][0]['message']['content']
                ?? 'Mi estetoscopio no detecta esa pregunta. ¿Probamos con algo del hospital?';
        }

        $respuesta = self::llamarApi($url, $apiKey, $model, $payloadMessages, true);

        if (!$respuesta) {
            return 'No se pudo contactar al asistente (Error de API).';
        }

        $toolCalls = $respuesta['choices'][0]['message']['tool_calls'] ?? null;

        if (!$toolCalls || count($toolCalls) === 0) {
            return $respuesta['choices'][0]['message']['content'] 
                ?? 'El asistente no generó una respuesta.';
        }

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

        $respuesta2 = self::llamarApi($url, $apiKey, $model, $payloadMessages, false);

        if ($respuesta2) {
            return $respuesta2['choices'][0]['message']['content'] ?? 'No pude interpretar los datos obtenidos.';
        }

        return 'Se ejecutaron las acciones, pero el asistente falló al generar la respuesta final.';
    }

    private static function llamarApi(
        string $url, 
        string $apiKey, 
        string $model, 
        array $messages, 
        bool $conHerramientas,
        int $intento = 0
    ): ?array {
        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0.1, // Temperatura bajísima para evitar que el modelo "alucine"
            'max_tokens' => 1024,
        ];

        if ($conHerramientas) {
            $payload['tools'] = AsistenteTools::definiciones();
            $payload['tool_choice'] = 'auto';
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
                return self::llamarApi($url, $apiKey, $model, $messages, $conHerramientas, $intento + 1);
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
1. Cuando te pregunten por datos REALES del sistema (pacientes, citas, camas, medicamentos, turnos, admisiones), USA las herramientas disponibles. NUNCA inventes números.
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
18. Si te piden un listado general (de pacientes, citas, camas, medicamentos, personal, admisiones), usa las herramientas 'listar_*' correspondientes.
19. Si te piden estadísticas generales del hospital, usa 'estadisticas_generales'.
20. Si te piden medicamentos con stock alto, usa 'medicamentos_stock_alto'. Si te piden stock bajo, usa 'medicamentos_stock_bajo'.
21. Si te piden el catálogo completo de medicamentos, usa 'listar_medicamentos'. Si te piden ver un medicamento específico, usa 'buscar_medicamento'.
22. Antes de decir "no tengo esa herramienta", REVISA la lista completa de herramientas disponibles.
PROMPT;
    }
}