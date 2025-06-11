<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; // Necessário para DB::raw, ST_X, ST_Y

class VehicleLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'location',
        'speed',
        'heading',
        'altitude',
        'tracking_session_id',
    ];

    /**
     * Define o relacionamento com o modelo Vehicle.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Mutator para a coluna 'location' (para gravar no banco)
     * Converte Lat/Lng em formato WKT (Well-Known Text) para o banco.
     * Exemplo: $vehicleLocation->location = ['latitude' => -23.55, 'longitude' => -46.63];
     */
    public function setLocationAttribute($value)
    {
        if (is_array($value) && isset($value['latitude']) && isset($value['longitude'])) {
            $latitude = $value['latitude'];
            $longitude = $value['longitude'];
            $this->attributes['location'] = DB::raw("ST_SetSRID(ST_MakePoint($longitude, $latitude), 4326)");
        } else if (is_string($value)) {
            // Permite atribuir diretamente uma string WKT
            // Assegura que o SRID seja definido se a string WKT não o incluir
            $this->attributes['location'] = DB::raw("ST_SetSRID(ST_GeomFromText('$value'), 4326)");
        }
    }

    /**
     * Accessor para a coluna 'location' (para ler do banco)
     * Converte o dado GEOMETRY para um formato PHP mais amigável (ex: array [lat, lng]).
     */
    public function getLocationAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        // AQUI ESTÁ A CORREÇÃO: Passar o $value duas vezes para os dois placeholders (?)
        // E, idealmente, a coluna 'location' já deveria estar vindo como texto (WKT)
        // se você a seleciona como ST_AsText(location) em alguma consulta.
        // No entanto, para o Eloquent, ela pode vir como um objeto binário.
        // Vamos usar uma função PostGIS para extrair as coordenadas da forma mais segura.
        // O Laravel 10/11+ e o PostGIS costumam lidar bem com isso.
        // Se a coluna 'location' for passada diretamente como binária, o ST_X e ST_Y
        // conseguem interpretar. O problema era a contagem de parâmetros.

        $coordinates = DB::selectOne("SELECT ST_X(?) as longitude, ST_Y(?) as latitude", [$value, $value]);

        if ($coordinates) {
            return [
                'latitude' => (float) $coordinates->latitude,
                'longitude' => (float) $coordinates->longitude,
            ];
        }

        return null;
    }
}
