<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignoVital extends Model
{
    protected $table = 'signos_vitales';

    protected $fillable = [
        'paciente_id', 'user_id',
        'temperatura', 'frecuencia_cardiaca', 'frecuencia_respiratoria',
        'presion_arterial', 'saturacion_oxigeno', 'glucosa',
        'peso', 'talla', 'escala_dolor',
        'triage', 'triage_manual',
        'motivo_consulta', 'notas',
    ];

    protected $casts = [
        'temperatura' => 'decimal:1',
        'peso' => 'decimal:2',
        'talla' => 'decimal:2',
        'triage_manual' => 'boolean',
    ];

    // ---------------- Relaciones ----------------

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function asignaciones()
    {
        return $this->hasMany(CamaPaciente::class);
    }

    // ---------------- Accessors seguros ----------------

    public function getImcAttribute(): ?float
    {
        if (!$this->peso || !$this->talla || $this->talla <= 0) return null;
        return round($this->peso / ($this->talla ** 2), 1);
    }

    public function getTriageLabelAttribute(): string
    {
        return match ($this->triage) {
            'rojo' => '🔴 Rojo — Emergencia',
            'naranja' => '🟠 Naranja — Muy urgente',
            'amarillo' => '🟡 Amarillo — Urgente',
            'verde' => '🟢 Verde — No urgente',
            'azul' => '🔵 Azul — Baja prioridad',
            default => 'Sin clasificar',
        };
    }

    public function getTriageDescripcionAttribute(): string
    {
        return match ($this->triage) {
            'rojo' => 'Requiere atención inmediata.',
            'naranja' => 'Debe atenderse rápidamente.',
            'amarillo' => 'Urgente, pero puede esperar cierto tiempo.',
            'verde' => 'No urgente. Puede esperar.',
            'azul' => 'Atención de baja prioridad, según el sistema utilizado.',
            default => '',
        };
    }

    public function getTriageColorAttribute(): string
    {
        return match ($this->triage) {
            'rojo' => 'bg-red-100 text-red-800 border-red-300',
            'naranja' => 'bg-orange-100 text-orange-800 border-orange-300',
            'amarillo' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            'verde' => 'bg-green-100 text-green-800 border-green-300',
            'azul' => 'bg-blue-100 text-blue-800 border-blue-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }

    public function getTriagePrioridadAttribute(): int
    {
        return match ($this->triage) {
            'rojo' => 1,
            'naranja' => 2,
            'amarillo' => 3,
            'verde' => 4,
            'azul' => 5,
            default => 99,
        };
    }

    // ---------------- Helpers ----------------

    /**
     * Devuelve el último signo vital de un paciente, o null si no tiene.
     * Uso: SignoVital::ultimoDe($pacienteId)
     */
    public static function ultimoDe(?int $pacienteId): ?self
    {
        if (!$pacienteId) return null;

        return static::where('paciente_id', $pacienteId)
            ->orderByDesc('created_at')
            ->first();
    }
}