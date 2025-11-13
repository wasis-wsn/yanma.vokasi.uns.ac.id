<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dapil extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the prodis in this dapil
     */
    public function prodis(): BelongsToMany
    {
        return $this->belongsToMany(Prodi::class, 'dapil_prodi')->withTimestamps();
    }

    /**
     * Get candidates assigned to this dapil
     */
    public function candidates(): HasMany
    {
        return $this->hasMany(PemilihanCandidate::class);
    }
}
