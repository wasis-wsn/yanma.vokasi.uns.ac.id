<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanTTDTA extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_ttd_ta';
    protected $guarded = ['id'];

    /**
     * Get the status that owns the PengajuanTTDTA
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(StatusPengesahanTA::class, 'status_id', 'id');
    }

    /**
     * Get the user that owns the PengajuanTTDTA
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
