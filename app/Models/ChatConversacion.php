<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatConversacion extends Model
{
    protected $table = 'chat_conversaciones';

    protected $fillable = ['user_id', 'titulo', 'ultimo_mensaje_en', 'total_mensajes'];

    protected $casts = [
        'ultimo_mensaje_en' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mensajes()
    {
        return $this->hasMany(ChatMensaje::class, 'conversacion_id')->orderBy('created_at');
    }
}