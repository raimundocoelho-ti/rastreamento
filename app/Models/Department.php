<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;


class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'manager_id',
        'image',
        'description',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return Storage::url($this->image);
        }
        return null;
    }

    /**
     * Get the manager associated with the department.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
