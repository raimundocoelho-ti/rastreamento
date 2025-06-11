<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'requires_license_plate',
        'controlled_by_odometer',
        'controlled_by_hour_meter',
    ];

    protected $casts = [
        'requires_license_plate' => 'boolean',
        'controlled_by_odometer' => 'boolean',
        'controlled_by_hour_meter' => 'boolean',
    ];

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }
}
