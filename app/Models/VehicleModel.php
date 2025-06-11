<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand_id',
    ];

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'vehicle_model_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(VehicleBrand::class);
    }
}
