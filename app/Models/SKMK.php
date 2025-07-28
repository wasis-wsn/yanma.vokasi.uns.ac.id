<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SKMK extends Model
{
    use HasFactory;

    protected $table = 'skmk';
    protected $guarded = ['id'];

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

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id', 'id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id', 'id');
    }

    public function scopeWaitingQueue($query)
    {
        return $query->where('queue_status', 'waiting')
                    ->whereDate('created_at', today())
                    ->orderBy('queue_number', 'asc');
    }

    public function scopeProcessedQueue($query)
    {
        return $query->where('queue_status', 'processed')
                    ->whereDate('created_at', today())
                    ->orderBy('queue_number', 'asc');
    }
}
