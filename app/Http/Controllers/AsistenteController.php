<?php

namespace App\Http\Controllers;

use App\Models\ChatConversacion;
use App\Models\ChatMensaje;
use App\Services\AsistenteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AsistenteController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $conversaciones = ChatConversacion::where('user_id', $user->id)
            ->orderByDesc('ultimo_mensaje_en')
            ->limit(20)
            ->get();

        $conversacionActiva = null;
        if ($request->filled('c')) {
            $conversacionActiva = ChatConversacion::where('user_id', $user->id)
                ->with('mensajes')
                ->find($request->c);
        }

        return view('asistente.index', compact('conversaciones', 'conversacionActiva'));
    }

    public function lista(): JsonResponse
    {
        $conversaciones = ChatConversacion::where('user_id', auth()->id())
            ->orderByDesc('ultimo_mensaje_en')
            ->limit(30)
            ->get(['id', 'titulo', 'ultimo_mensaje_en', 'total_mensajes']);

        return response()->json($conversaciones);
    }

    public function ver(ChatConversacion $conversacion): JsonResponse
    {
        if ($conversacion->user_id !== auth()->id()) {
            abort(403);
        }

        return response()->json([
            'id'     => $conversacion->id,
            'titulo' => $conversacion->titulo,
            'mensajes' => $conversacion->mensajes()
                ->orderBy('created_at')
                ->get()
                ->map(fn($m) => [
                    'role'       => $m->rol,
                    'content'    => $m->contenido,
                    'created_at' => $m->created_at->format('H:i'),
                ]),
        ]);
    }

    public function nuevaConversacion(): JsonResponse
    {
        $conversacion = ChatConversacion::create([
            'user_id'           => auth()->id(),
            'titulo'            => 'Nueva conversación',
            'ultimo_mensaje_en' => now(),
        ]);

        return response()->json(['id' => $conversacion->id]);
    }

        public function enviar(Request $request): JsonResponse
    {
        $request->validate([
            'message'              => ['required', 'string', 'max:2000'],
            'conversacion_id'      => ['nullable', 'exists:chat_conversaciones,id'],
            'sin_sentido'          => ['nullable', 'boolean'],
            'contador_sin_sentido' => ['nullable', 'integer', 'min:0'],
        ]);

        $user = auth()->user();
        $mensajeUsuario = $request->message;
        $textoMin = mb_strtolower($mensajeUsuario);

        // 1. DETECTAR EL TEMA DEL MENSAJE ACTUAL
        $temaActual = 'otro';
        if (str_contains($textoMin, 'cama')) $temaActual = 'camas';
        elseif (str_contains($textoMin, 'cita')) $temaActual = 'citas';
        elseif (str_contains($textoMin, 'medicamento') || str_contains($textoMin, 'farmaco')) $temaActual = 'medicamentos';
        elseif (str_contains($textoMin, 'paciente')) $temaActual = 'pacientes';

        $conversacion = null;
        if ($request->filled('conversacion_id')) {
            $conversacion = ChatConversacion::where('user_id', $user->id)
                ->find($request->conversacion_id);
        }

        if (!$conversacion) {
            $conversacion = ChatConversacion::create([
                'user_id'           => $user->id,
                'titulo'            => mb_substr($request->message, 0, 50),
                'ultimo_mensaje_en' => now(),
            ]);
        }

        // Guardar mensaje del usuario
        ChatMensaje::create([
            'conversacion_id' => $conversacion->id,
            'rol'             => 'user',
            'contenido'       => $request->message,
        ]);

        // 2. LÓGICA DE HISTORIAL INTELIGENTE
        $historial = [];
        
        // Si es un tema nuevo o no hay historial, NO enviamos contexto anterior
        $ultimoMensajeBot = $conversacion->mensajes()
            ->where('rol', 'assistant')
            ->orderByDesc('created_at')
            ->first();

        $temaAnterior = 'otro';
        if ($ultimoMensajeBot) {
            $textoBot = mb_strtolower($ultimoMensajeBot->contenido);
            if (str_contains($textoBot, 'cama')) $temaAnterior = 'camas';
            elseif (str_contains($textoBot, 'cita')) $temaAnterior = 'citas';
            elseif (str_contains($textoBot, 'medicamento')) $temaAnterior = 'medicamentos';
        }

        // Si el tema cambió, enviamos SOLO el mensaje actual (historial vacío)
        // Si es el mismo tema, enviamos el historial corto para dar contexto
        if ($temaActual !== 'otro' && $temaActual !== $temaAnterior) {
            // Cambio de tema: historial vacío para forzar al modelo a empezar de cero
            $historial = [
                ['role' => 'user', 'content' => $mensajeUsuario]
            ];
        } else {
            // Mismo tema o tema desconocido: enviamos el último mensaje del bot y el nuevo del usuario
            $historial = $conversacion->mensajes()
                ->whereIn('rol', ['user', 'assistant'])
                ->orderByDesc('created_at')
                ->limit(2)
                ->get()
                ->reverse()
                ->map(fn($m) => ['role' => $m->rol, 'content' => $m->contenido])
                ->values()
                ->toArray();
        }

        $sinSentido         = (bool) $request->input('sin_sentido', false);
        $contadorSinSentido = (int)  $request->input('contador_sin_sentido', 0);

        // Enviar al asistente
        $respuesta = AsistenteService::responder(
            $historial,
            null,
            $sinSentido,
            $contadorSinSentido
        );

        // Guardar respuesta del asistente
        ChatMensaje::create([
            'conversacion_id' => $conversacion->id,
            'rol'             => 'assistant',
            'contenido'       => $respuesta ?? 'Sin respuesta.',
        ]);

        $conversacion->update([
            'ultimo_mensaje_en' => now(),
            'total_mensajes'    => $conversacion->mensajes()->count(),
        ]);

        return response()->json([
            'response'        => $respuesta ?? 'Sin respuesta.',
            'conversacion_id' => $conversacion->id,
        ]);
    }

    public function destruir(ChatConversacion $conversacion)
    {
        if ($conversacion->user_id !== auth()->id()) {
            abort(403);
        }

        $conversacion->delete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('asistente.index')
            ->with('success', 'Conversación eliminada.');
    }
}