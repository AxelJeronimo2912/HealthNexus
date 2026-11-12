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

        // Guardar mensaje del usuario en la base de datos
        ChatMensaje::create([
            'conversacion_id' => $conversacion->id,
            'rol'             => 'user',
            'contenido'       => $request->message,
        ]);

        // ¡AQUÍ ESTÁ EL CAMBIO CLAVE!
        // Enviamos SOLO el mensaje actual, sin historial.
        // Esto obliga al modelo a concentrarse exclusivamente en lo que acaba de preguntar.
        $mensajesParaGroq = [
            ['role' => 'user', 'content' => $request->message]
        ];

        $sinSentido         = (bool) $request->input('sin_sentido', false);
        $contadorSinSentido = (int)  $request->input('contador_sin_sentido', 0);

        // Enviar al asistente
        $respuesta = AsistenteService::responder(
            $mensajesParaGroq,
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