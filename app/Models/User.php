<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'foto',
        'google_id',
        'google_token',
        'google_refresh_token',
        'prodi',
        'nim',
        'role',
        'pangkat',
        'jabatan',
        'no_wa',
        'pembina_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the role that owns the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function roles()
    {
        return $this->belongsTo(Role::class, 'role');
    }

    /**
     * Get the prodi that owns the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prodis()
    {
        return $this->belongsTo(Prodi::class, 'prodi', 'id');
    }

    /**
     * Get all of the suratKeterangan for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function suratKeterangan()
    {
        return $this->hasMany(SuratKeterangan::class, 'user_id', 'id');
    }

    /**
     * Get all of the skmk for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skmk()
    {
        return $this->hasMany(SKMK::class, 'user_id', 'id');
    }

    /**
     * Get all of the SKL for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function skl()
    {
        return $this->hasOne(SKL::class, 'user_id', 'id');
    }

    /**
     * Get all of the PengajuanTTDTA for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function pengajuanTTDTA()
    {
        return $this->hasOne(PengajuanTTDTA::class, 'user_id', 'id');
    }

    /**
     * Get all of the VerifikasiWisuda for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function verifikasiWisuda()
    {
        return $this->hasOne(VerifikasiWisuda::class, 'user_id', 'id');
    }

    /**
     * Get all of the TranskripNilai for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function transkripNilai()
    {
        return $this->hasOne(TranskripNilai::class, 'user_id', 'id');
    }

    /**
     * Get all of the SKPI for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function skpi()
    {
        return $this->hasOne(SKPI::class, 'user_id', 'id');
    }

    /**
     * Get all of the SIK for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function sik()
    {
        return $this->hasMany(SIK::class, 'ormawa_id', 'id');
    }

    /**
     * Get all of the SIK for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function ormawa()
    {
        return $this->hasMany(SIK::class, 'ketua_id', 'id');
    }

    /**
     * Get all of the SIK for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function pembina()
    {
        return $this->belongsTo(PembinaOrmawa::class, 'pembina_id', 'id');
    }

    /**
     * Get all of the perpanjanganStudi for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function perpanjanganStudi()
    {
        return $this->hasMany(PerpanjanganStudi::class, 'user_id', 'id');
    }

    /**
     * Get all of the diluarJadwal for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function diluarJadwal()
    {
        return $this->hasMany(DiluarJadwal::class, 'user_id', 'id');
    }

    /**
     * Get all of the penundaan for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function penundaan()
    {
        return $this->hasMany(Penundaan::class, 'user_id', 'id');
    }

    /**
     * Get all of the selangCuti for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function selangCuti()
    {
        return $this->hasMany(SelangCuti::class, 'user_id', 'id');
    }

    /**
     * Get all of the undurDiri for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function undurDiri()
    {
        return $this->hasMany(UndurDiri::class, 'user_id', 'id');
    }

    /**
     * Get all of the suratTugas for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function suratTugas()
    {
        return $this->hasMany(SuratTugas::class, 'user_id', 'id');
    }
}
