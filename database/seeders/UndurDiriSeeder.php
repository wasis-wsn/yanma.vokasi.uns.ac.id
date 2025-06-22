<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class UndurDiriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('undur_diris')->insert([
            [
                'user_id' => 1,
                'status_id' => 1,
                'semester_id' => 2,
                'tahun_akademik_id' => 3,
                'catatan' => 'Mengundurkan diri karena alasan pribadi',
                'no_surat' => 'UND-2025/001',
                'file' => 'undur_diri_1.pdf',
                'tanggal_proses' => Carbon::now()->subDays(2),
                'tanggal_ambil' => Carbon::now()->addDays(5),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 2,
                'status_id' => 2,
                'semester_id' => 1,
                'tahun_akademik_id' => 2,
                'catatan' => 'Pindah ke perguruan tinggi lain',
                'no_surat' => 'UND-2025/002',
                'file' => 'undur_diri_2.pdf',
                'tanggal_proses' => Carbon::now()->subDays(5),
                'tanggal_ambil' => Carbon::now()->addDays(2),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
