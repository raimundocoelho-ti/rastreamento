<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_plate',
        'vehicle_model_id',
        'vehicle_type_id',
        'color',
        'number_of_seats',
        'status',
        'notes',
        'image', // Adicionado
    ];

    protected $casts = [
        'number_of_seats' => 'integer',
    ];

    public function vehicleModel(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class);
    }

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return Storage::url($this->image);
        }
        return null;
    }

    public function getIdentifierAttribute(): string
    {
        return $this->license_plate ?? 'Veículo ID: ' . $this->id;
    }
}
