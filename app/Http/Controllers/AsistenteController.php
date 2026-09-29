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
    /**
     * Vista principal del asistente.
     */
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

    /**
     * Lista todas las conversaciones del usuario autenticado (para el sidebar).
     */
    public function lista(): JsonResponse
    {
        $conversaciones = ChatConversacion::where('user_id', auth()->id())
            ->orderByDesc('ultimo_mensaje_en')
            ->limit(30)
            ->get(['id', 'titulo', 'ultimo_mensaje_en', 'total_mensajes']);

        return response()->json($conversaciones);
    }

    /**
     * Devuelve los mensajes de una conversación específica.
     * Solo si pertenece al usuario autenticado.
     */
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

    /**
     * Crea una conversación nueva vacía.
     */
    public function nuevaConversacion(): JsonResponse
    {
        $conversacion = ChatConversacion::create([
            'user_id'           => auth()->id(),
            'titulo'            => 'Nueva conversación',
            'ultimo_mensaje_en' => now(),
        ]);

        return response()->json(['id' => $conversacion->id]);
    }

    /**
     * Envía un mensaje al asistente y devuelve su respuesta.
     */
    public function enviar(Request $request): JsonResponse
    {
        $request->validate([
            'message'              => ['required', 'string', 'max:2000'],
            'conversacion_id'      => ['nullable', 'exists:chat_conversaciones,id'],
            'sin_sentido'          => ['nullable', 'boolean'],
            'contador_sin_sentido' => ['nullable', 'integer', 'min:0'],
        ]);

        $user = auth()->user();

        // Crear o recuperar la conversación (validando propiedad)
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

        // Obtener historial (últimos 10 mensajes)
        $historial = $conversacion->mensajes()
            ->whereIn('rol', ['user', 'assistant'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->reverse()
            ->map(fn($m) => ['role' => $m->rol, 'content' => $m->contenido])
            ->values()
            ->toArray();

        // Datos de humor
        $sinSentido         = (bool) $request->input('sin_sentido', false);
        $contadorSinSentido = (int)  $request->input('contador_sin_sentido', 0);

        // Enviar al asistente
        $respuesta = AsistenteService::responder(
            $historial,
            null,
            $sinSentido,
            $contadorSinSentido
        );

        // Guardar respuesta
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

    /**
     * Elimina una conversación (solo si es del usuario).
     */
    public function destruir(ChatConversacion $conversacion)
    {
        if ($conversacion->user_id !== auth()->id()) {
            abort(403);
        }

        $conversacion->delete();

        // Si la petición es AJAX/JSON, devolver JSON; si no, redirigir
        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('asistente.index')
            ->with('success', 'Conversación eliminada.');
    }
}