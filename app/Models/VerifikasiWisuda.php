<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifikasiWisuda extends Model
{
    use HasFactory;

    protected $table = 'verifikasi_wisuda';
    protected $guarded = ['id'];

    /**
     * Get the status that owns the SuratKeterangan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(StatusWisuda::class, 'status_id', 'id');
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
