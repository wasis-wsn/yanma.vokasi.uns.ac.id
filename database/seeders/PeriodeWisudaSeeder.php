<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PeriodeWisudaSeeder extends Seeder
{
    public function run(): void
    {
        $bulanIndo = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $data = [];

        foreach ($bulanIndo as $bulan => $namaBulan) {
            $data[] = [
                'tahun' => 2025,
                'bulan' => $bulan,
                'nama_bulan' => $namaBulan,
                'tanggal_wisuda' => Carbon::create(2025, $bulan, 7)->format('Y-m-d'),
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('periode_wisudas')->insert($data);
    }
}
