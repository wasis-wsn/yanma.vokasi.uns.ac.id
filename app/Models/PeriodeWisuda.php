<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeWisuda extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun',
        'bulan',
        'nama_bulan',
        'tanggal_wisuda',
        'is_active'
    ];

    protected $casts = [
        'tanggal_wisuda' => 'date',
        'is_active' => 'boolean'
    ];

    public static function getActivePeriode($tahun = null)
    {
        $tahun = $tahun ?? date('Y');
        return self::where('tahun', $tahun)
            ->where('is_active', true)
            ->first();
    }

    public static function initializeYear($tahun)
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            self::firstOrCreate([
                'tahun' => $tahun,
                'bulan' => $bulan
            ], [
                'nama_bulan' => $bulanNames[$bulan],
                'is_active' => false
            ]);
        }
    }
}
