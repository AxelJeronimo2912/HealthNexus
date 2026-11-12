<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ArchivoEstudio extends Model
{
    use HasFactory;

    protected $table = 'archivos_estudio';

    protected $fillable = [
        'solicitud_estudio_id', 'user_id', 'nombre_original',
        'ruta', 'mime_type', 'tamano_bytes', 'tipo', 'descripcion',
    ];

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(SolicitudEstudio::class, 'solicitud_estudio_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('local')->url($this->ruta);
    }

    public function getEsPdfAttribute(): bool
    {
        return $this->tipo === 'pdf';
    }

    public function getEsImagenAttribute(): bool
    {
        return $this->tipo === 'imagen';
    }
}