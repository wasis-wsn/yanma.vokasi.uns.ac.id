<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusHeregistrasi extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'status_heregistrasis';
    protected $fillable = ['name'];

    /**
     * Get all of the diluarJadwal for the StatusHeregistrasi
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function diluarJadwal()
    {
        return $this->hasMany(DiluarJadwal::class, 'status_id', 'id');
    }

    /**
     * Get all of the penundaan for the StatusHeregistrasi
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function penundaan()
    {
        return $this->hasMany(Penundaan::class, 'status_id', 'id');
    }

    /**
     * Get all of the perpanjanganStudi for the StatusHeregistrasi
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function perpanjanganStudi()
    {
        return $this->hasMany(PerpanjanganStudi::class, 'status_id', 'id');
    }

    /**
     * Get all of the selangCuti for the StatusHeregistrasi
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function selangCuti()
    {
        return $this->hasMany(SelangCuti::class, 'status_id', 'id');
    }

    /**
     * Get all of the undurDiri for the StatusHeregistrasi
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function undurDiri()
    {
        return $this->hasMany(UndurDiri::class, 'status_id', 'id');
    }
}
