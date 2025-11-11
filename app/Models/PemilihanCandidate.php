<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PemilihanCandidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'pemilihan_id',
        'nomor_urut',
        'name',
        'foto',
        'visi',
        'misi',
        'deskripsi',
    ];

    public function pemilihan(): BelongsTo
    {
        return $this->belongsTo(Pemilihan::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PemilihanVote::class, 'candidate_id');
    }
}
