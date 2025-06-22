<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DiluarJadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('diluar_jadwals')->insert([
            [
                'user_id' => 1,
                'status_id' => 1,
                'catatan' => 'Permohonan cuti mendesak',
                'no_surat' => 'DJ-2025/001',
                'semester_romawi' => 'II',
                'semester_id' => 2,
                'tahun_akademik_id' => 3,
                'alasan' => 'Alasan kesehatan keluarga',
                'tanggal_bayar' => Carbon::now()->subDays(4)->format('Y-m-d'),
                'surat_permohonan' => 'permohonan_cuti_1.pdf',
                'bukti_bayar_ukt' => 'bukti_bayar_ukt_1.pdf',
                'izin_cuti' => 'izin_cuti_1.pdf',
                'tanggal_proses' => Carbon::now()->subDays(1),
                'tanggal_ambil' => Carbon::now()->addDays(3),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 2,
                'status_id' => 2,
                'catatan' => 'Keperluan keluarga',
                'no_surat' => 'DJ-2025/002',
                'semester_romawi' => 'IV',
                'semester_id' => 4,
                'tahun_akademik_id' => 4,
                'alasan' => 'Pindah tempat tinggal',
                'tanggal_bayar' => Carbon::now()->subDays(7)->format('Y-m-d'),
                'surat_permohonan' => 'permohonan_cuti_2.pdf',
                'bukti_bayar_ukt' => 'bukti_bayar_ukt_2.pdf',
                'izin_cuti' => 'izin_cuti_2.pdf',
                'tanggal_proses' => Carbon::now()->subDays(2),
                'tanggal_ambil' => Carbon::now()->addDays(4),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
