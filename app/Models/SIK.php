<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SIK extends Model
{
    use HasFactory;

    protected $table = 'sik';
    protected $guarded = ['id'];

    /**
     * Get the lpj associated with the SIK
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lpj()
    {
        return $this->hasOne(Lpj::class, 'sik_id', 'id');
    }

    /**
     * Get the status that owns the SKL
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(StatusKemahasiswaan::class, 'status_id', 'id');
    }

    /**
     * Get the user that owns the SKL
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ormawa()
    {
        return $this->belongsTo(User::class, 'ormawa_id', 'id');
    }

    /**
     * Get the ormawa that owns the SKL
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ketua()
    {
        return $this->belongsTo(User::class, 'ketua_id', 'id');
    }

    /**
     * Get the PembinaOrmawa that owns the SKL
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pembinaOrmawa()
    {
        return $this->belongsTo(PembinaOrmawa::class, 'pembina_ormawa_id', 'id');
    }
}
