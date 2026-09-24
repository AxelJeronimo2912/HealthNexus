<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    protected $table = 'hospitales';

    protected $fillable = [
        'nombre', 'tipo', 'nivel', 'direccion', 'telefono',
        'latitud', 'longitud', 'servicios',
        'tiene_urgencias', 'tiene_uci', 'activo',
    ];

    protected $casts = [
        'latitud' => 'float',
        'longitud' => 'float',
        'tiene_urgencias' => 'boolean',
        'tiene_uci' => 'boolean',
        'activo' => 'boolean',
    ];

    /**
     * Distancia en km desde un punto (Haversine).
     */
    public function distanciaDesde(float $lat, float $lng): float
    {
        $R = 6371;
        $dLat = deg2rad($this->latitud - $lat);
        $dLng = deg2rad($this->longitud - $lng);

        $a = sin($dLat / 2) ** 2 +
             cos(deg2rad($lat)) * cos(deg2rad($this->latitud)) *
             sin($dLng / 2) ** 2;

        return round($R * 2 * atan2(sqrt($a), sqrt(1 - $a)), 2);
    }
}