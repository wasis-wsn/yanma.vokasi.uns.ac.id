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
     * Get the status that owns the VerifikasiWisuda
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(StatusWisuda::class, 'status_id', 'id');
    }

    /**
     * Get the user that owns the VerifikasiWisuda
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Reset students status for a specific graduation period
     *
     * @param string $periodeWisuda
     * @return int
     */
    public static function resetStatusForPeriode($periodeWisuda)
    {
        return self::where('periode_wisuda', $periodeWisuda)
            ->whereNotIn('status_id', ['1']) // Don't reset those already in "Belum Diproses"
            ->update([
                'status_id' => '1', // Belum Diproses
                'catatan' => 'Status direset karena perubahan periode wisuda pada ' . now()->format('d F Y H:i:s'),
                'tanggal_proses' => null
            ]);
    }

    /**
     * Get students by graduation period
     *
     * @param string $periodeWisuda
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByPeriode($periodeWisuda)
    {
        return self::where('periode_wisuda', $periodeWisuda)->get();
    }
}

