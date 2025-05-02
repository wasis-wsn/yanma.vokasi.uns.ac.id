<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lpj extends Model
{
    use HasFactory;

    protected $table = 'lpjs';
    protected $guarded = ['id'];

    /**
     * Get the sik that owns the Lpj
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sik()
    {
        return $this->belongsTo(SIK::class, 'sik_id', 'id');
    }

    /**
     * Get the suratTugas that owns the Lpj
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function suratTugas()
    {
        return $this->belongsTo(SuratTugas::class, 'surat_tugas_id', 'id');
    }

    /**
     * Get the status that owns the Lpj
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(StatusLPJ::class, 'status_id', 'id');
    }
}
