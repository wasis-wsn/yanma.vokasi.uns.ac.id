<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pemilihan extends Model
{
    use HasFactory;

    public const JENIS_OPTIONS = [
        'presbem' => 'Pemilihan Presbem',
        'caleg' => 'Pemilihan Caleg',
    ];

    protected $fillable = [
        'name',
        'slug',
        'jenis',
        'deskripsi',
        'mulai_at',
        'selesai_at',
        'is_active',
    ];

    protected $casts = [
        'mulai_at' => 'datetime',
        'selesai_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function candidates(): HasMany
    {
        return $this->hasMany(PemilihanCandidate::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PemilihanVote::class);
    }

    public function scopeOpen($query)
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('mulai_at')->orWhere('mulai_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('selesai_at')->orWhere('selesai_at', '>=', $now);
            });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function votingWindowIsOpen(): bool
    {
        $now = now();

        if (!$this->is_active) {
            return false;
        }

        if (!is_null($this->mulai_at) && $this->mulai_at->isFuture()) {
            return false;
        }

        if (!is_null($this->selesai_at) && $this->selesai_at->isPast()) {
            return false;
        }

        return true;
    }

    public static function jenisOptions(): array
    {
        return self::JENIS_OPTIONS;
    }
}
