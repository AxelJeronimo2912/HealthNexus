<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use App\Enums\AuditEvent;
use App\Services\AuditoriaService;

class SolicitudEstudio extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_estudio';

    protected $fillable = [
        'folio', 'paciente_id', 'servicio_id', 'cita_id', 'cuenta_id',
        'cuenta_item_id', 'medico_solicita_id', 'user_id',
        'tipo_estudio', 'indicaciones', 'notas', 'estado',
        'solicitado_en', 'resultado_en', 'entregado_en',
    ];

    protected $casts = [
        'solicitado_en' => 'datetime',
        'resultado_en'  => 'datetime',
        'entregado_en'  => 'datetime',
    ];

    // ==============================
    // RELACIONES
    // ==============================

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }

    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(Cuenta::class);
    }

    public function cuentaItem(): BelongsTo
    {
        return $this->belongsTo(CuentaItem::class);
    }

    public function medicoSolicita(): BelongsTo
    {
        return $this->belongsTo(User::class, 'medico_solicita_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function archivos(): HasMany
    {
        return $this->hasMany(ArchivoEstudio::class);
    }

    // ==============================
    // LÓGICA DE NEGOCIO
    // ==============================

    public static function generarFolio(): string
    {
        $prefijo = 'EST-' . now()->format('Ymd');
        $ultimo = static::where('folio', 'like', $prefijo . '%')
            ->orderByDesc('id')->first();

        $consecutivo = $ultimo
            ? ((int) substr($ultimo->folio, -4)) + 1
            : 1;

        return $prefijo . '-' . str_pad($consecutivo, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Crea la solicitud y la carga automáticamente a la cuenta del paciente.
     */
    public static function crearYCargar(array $data, bool $cargarACuenta = true): self
    {
        return DB::transaction(function () use ($data, $cargarACuenta) {
            $paciente = Paciente::findOrFail($data['paciente_id']);
            $servicio = Servicio::findOrFail($data['servicio_id']);

            // 1. Crear la solicitud
            $solicitud = static::create([
                'folio'              => static::generarFolio(),
                'paciente_id'        => $paciente->id,
                'servicio_id'        => $servicio->id,
                'cita_id'            => $data['cita_id'] ?? null,
                'medico_solicita_id' => $data['medico_solicita_id'] ?? auth()->id(), // 👈 SE GUARDA EL ID
                'user_id'            => auth()->id() ?? 1,
                'tipo_estudio'       => $data['tipo_estudio'],
                'indicaciones'       => $data['indicaciones'] ?? null,
                'notas'              => $data['notas'] ?? null,
                'estado'             => 'solicitado',
                'solicitado_en'      => now(),
            ]);

            // 2. Cargar a la cuenta
            if ($cargarACuenta && (float) $servicio->precio > 0) {
                $cuenta = $paciente->obtenerCuentaAbierta();

                if ($cuenta && $cuenta->estado === 'abierta') {
                    $item = $cuenta->items()->create([
                        'servicio_id'     => $servicio->id,
                        'user_id'         => auth()->id() ?? 1,
                        'concepto'        => "Estudio: {$servicio->nombre} - {$solicitud->tipo_estudio}",
                        'cantidad'        => 1,
                        'precio_unitario' => (float) $servicio->precio,
                        'descuento'       => 0,
                        'notas'           => "Solicitud {$solicitud->folio}",
                    ]);

                    $solicitud->update([
                        'cuenta_id'      => $cuenta->id,
                        'cuenta_item_id' => $item->id,
                    ]);

                    AuditoriaService::registrar(
                        AuditEvent::CUENTA_CARGO_AGREGADO,
                        'cuentas',
                        "Cargo por estudio {$solicitud->folio} en cuenta {$cuenta->folio}",
                        $cuenta,
                        [],
                        [
                            'item_id'   => $item->id,
                            'concepto'  => $item->concepto,
                            'solicitud' => $solicitud->folio,
                        ]
                    );
                }
            }

            return $solicitud;
        });
    }
}