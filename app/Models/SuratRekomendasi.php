<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratRekomendasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat_rekomendasi';

    protected $fillable = [
        'user_id',
        'nomor_ijazah',
        'tanggal_lulus',
        'file',
        'status_id',
        'no_surat',
        'catatan',
        'tanggal_proses',
        'surat_hasil',
    ];

    protected $dates = [
        'tanggal_lulus',
        'tanggal_proses',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi dengan Status
    public function status()
    {
        return $this->belongsTo(StatusKemahasiswaan::class, 'status_id');
    }
}
