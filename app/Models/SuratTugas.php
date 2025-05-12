<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratTugas extends Model
{
    use HasFactory;

    protected $table = 'surat_tugas';
    protected $guarded = ['id'];

    /**
     * Get the lpj associated with the SuratTugas
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lpj()
    {
        return $this->hasOne(Lpj::class, 'surat_tugas_id', 'id');
    }

    /**
     * Get the status that owns the SuratKeterangan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(StatusKemahasiswaan::class, 'status_id', 'id');
    }

    /**
     * Get the user that owns the SuratKeterangan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
