<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PembinaOrmawa extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'pembina';
    protected $guarded = ['id'];

    /**
     * Get all of the SIK for the Ormawa
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sik()
    {
        return $this->hasMany(SIK::class, 'pembina_ormawa_id', 'id');
    }

    /**
     * Get all of the SIK for the Ormawa
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user()
    {
        return $this->hasMany(User::class, 'pembina_id', 'id');
    }

    /**
     * Get the prodi that owns the PembinaOrmawa
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'unit_id', 'id');
    }
}
