<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TahunAkademik extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tahun_akademiks';
    protected $guarded = ['id'];

    /**
     * Get all of the suratKeterangan for the TahunAkademik
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function suratKeterangan()
    {
        return $this->hasMany(SuratKeterangan::class, 'tahun_akademik_id', 'id');
    }

    public function skmk()
    {
        return $this->hasMany(SKMK::class, 'tahun_akademik_id', 'id');
    }
}
