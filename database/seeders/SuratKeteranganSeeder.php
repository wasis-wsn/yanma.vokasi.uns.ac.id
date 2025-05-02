<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SuratKeteranganSeeder extends Seeder
{
    public function run()
    {
        DB::table('surat_keterangan')->truncate(); // Hapus data sebelumnya jika ada
        DB::table('surat_keterangan')->insert([
            [
                'user_id' => 1,
                'status_id' => 1,
                'tahun_akademik_id' => 1,
                'semester_id' => 1,
                'keperluan' => 'Pengajuan Beasiswa',
                'file' => 'beasiswa.pdf',
                'surat_hasil' => 'surat_beasiswa.pdf',
                'catatan' => 'Lengkap',
                'no_surat' => '001/SK/2025',
                'tanggal_proses' => Carbon::parse('2025-04-01'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 2,
                'status_id' => 2,
                'tahun_akademik_id' => 1,
                'semester_id' => 2,
                'keperluan' => 'Surat Aktif Kuliah',
                'file' => 'aktif_kuliah.pdf',
                'surat_hasil' => 'hasil_aktif_kuliah.pdf',
                'catatan' => 'Butuh segera',
                'no_surat' => '002/SK/2025',
                'tanggal_proses' => Carbon::parse('2025-04-05'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
