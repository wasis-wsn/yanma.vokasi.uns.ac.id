<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LpjsSeeder extends Seeder
{
    public function run()
    {
        DB::table('lpjs')->insert([
            [
                'sik_id' => 1,
                'surat_tugas_id' => 1,
                'status_id' => 2, // Misalnya "Sudah Upload"
                'catatan' => 'Dokumen lengkap dan sesuai',
                'file' => 'lpj_kegiatan.pdf',
                'tanggal_proses' => Carbon::now()->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sik_id' => 1,
                'surat_tugas_id' => 1,
                'status_id' => 3, // Misalnya "Revisi"
                'catatan' => 'Perlu revisi bagian keuangan',
                'file' => 'lpj_revisi.pdf',
                'tanggal_proses' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
