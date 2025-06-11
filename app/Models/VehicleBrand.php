<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleBrand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get all vehicle models for the brand.
     */
    public function vehicleModels(): HasMany
    {
        return $this->hasMany(VehicleModel::class);
    }

    /**
     * Get all vehicles for the brand through its models.
     */
    public function vehicles()
    {
        return $this->hasManyThrough(Vehicle::class, VehicleModel::class);
    }
}
