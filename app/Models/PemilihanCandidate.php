<?php

namespace App\Models;

use App\Models\Prodi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PemilihanCandidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'pemilihan_id',
        'prodi_id',
        'dapil_id',
        'nomor_urut',
        'name',
        'ketua_nama',
        'ketua_prodi',
        'ketua_angkatan',
        'wakil_nama',
        'wakil_prodi',
        'wakil_angkatan',
        'foto',
        'visi',
        'misi',
        'deskripsi',
    ];

    protected $casts = [
        //
    ];

    protected $appends = [
        'photo_url',
    ];

    public function pemilihan(): BelongsTo
    {
        return $this->belongsTo(Pemilihan::class);
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }

    public function dapil(): BelongsTo
    {
        return $this->belongsTo(Dapil::class, 'dapil_id');
    }

    public function dapilProdis(): BelongsToMany
    {
        return $this->belongsToMany(Prodi::class, 'pemilihan_candidate_dapils', 'candidate_id', 'prodi_id')
            ->withTimestamps();
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PemilihanVote::class, 'candidate_id');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->foto) {
            return null;
        }

        if (Str::startsWith($this->foto, ['http://', 'https://', '//', 'data:'])) {
            return $this->foto;
        }

        if (Storage::disk('public')->exists($this->foto)) {
            return Storage::disk('public')->url($this->foto);
        }

        return asset($this->foto);
    }
}
