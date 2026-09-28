<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMensaje extends Model
{
    protected $table = 'chat_mensajes';

    protected $fillable = ['conversacion_id', 'rol', 'contenido', 'tool_calls', 'tool_call_id'];

    protected $casts = [
        'tool_calls' => 'array',
    ];

    public function conversacion()
    {
        return $this->belongsTo(ChatConversacion::class, 'conversacion_id');
    }
}