<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'status';
    protected $fillable = ['name'];

    /**
     * Get all of the suratKeterangan for the Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function suratKeterangan()
    {
        return $this->hasMany(SuratKeterangan::class, 'status_id', 'id');
    }

    /**
     * Get all of the SKMK for the Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skmk()
    {
        return $this->hasMany(SKMK::class, 'status_id', 'id');
    }

    /**
     * Get all of the SKL for the Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skl()
    {
        return $this->hasMany(SKL::class, 'status_id', 'id');
    }

    /**
     * Get all of the PengajuanTTDTA for the Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pengajuanTTDTA()
    {
        return $this->hasMany(PengajuanTTDTA::class, 'status_id', 'id');
    }

    /**
     * Get all of the VerifikasiWisuda for the Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function verifikasiWisuda()
    {
        return $this->hasMany(VerifikasiWisuda::class, 'status_id', 'id');
    }

    /**
     * Get all of the TranskripNilai for the Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transkripNilai()
    {
        return $this->hasMany(TranskripNilai::class, 'status_id', 'id');
    }

    /**
     * Get all of the SIK for the Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sik()
    {
        return $this->hasMany(SIK::class, 'status_id', 'id');
    }

    /**
     * Get all of the Legalisir for the Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function legalisir()
    {
        return $this->hasMany(Legalisir::class, 'status_id', 'id');
    }

    /**
     * Get all of the perpanjanganStudi for the Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function perpanjanganStudi()
    {
        return $this->hasMany(PerpanjanganStudi::class, 'status_id', 'id');
    }

    /**
     * Get all of the diluarJadwal for the Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function diluarJadwal()
    {
        return $this->hasMany(DiluarJadwal::class, 'status_id', 'id');
    }

    /**
     * Get all of the penundaan for the Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function penundaan()
    {
        return $this->hasMany(Penundaan::class, 'status_id', 'id');
    }

    /**
     * Get all of the selangCuti for the Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function selangCuti()
    {
        return $this->hasMany(SelangCuti::class, 'status_id', 'id');
    }

    /**
     * Get all of the undurDiri for the Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function undurDiri()
    {
        return $this->hasMany(UndurDiri::class, 'status_id', 'id');
    }

    /**
     * Get all of the suratTugas for the Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function suratTugas()
    {
        return $this->hasMany(SuratTugas::class, 'status_id', 'id');
    }
}
