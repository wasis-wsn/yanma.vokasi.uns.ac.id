<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusKemahasiswaan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'status_kemahasiswaans';
    protected $fillable = ['name'];

    /**
     * Get all of the suratKeterangan for the StatusKemahasiswaan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function suratKeterangan()
    {
        return $this->hasMany(SuratKeterangan::class, 'status_id', 'id');
    }

    /**
     * Get all of the skmk for the StatusKemahasiswaan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skmk()
    {
        return $this->hasMany(SKMK::class, 'status_id', 'id');
    }

    /**
     * Get all of the suratTugas for the StatusKemahasiswaan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function suratTugas()
    {
        return $this->hasMany(SuratTugas::class, 'status_id', 'id');
    }

    /**
     * Get all of the SIK for the StatusKemahasiswaan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function SIK()
    {
        return $this->hasMany(SIK::class, 'status_id', 'id');
    }
}
