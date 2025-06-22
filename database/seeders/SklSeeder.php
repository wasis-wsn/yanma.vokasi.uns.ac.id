<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SklSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('skl')->insert([
            [
                'user_id' => 1,
                'status_id' => 1,
                'no_surat' => 'SKL-2025/001',
                'catatan' => 'Pengajuan SKL semester genap',
                'lembar_revisi' => 'lembar_revisi_1.pdf',
                'ss_ajuan_skl' => 'ss_ajuan_skl_1.png',
                'tanggal_proses' => Carbon::now()->subDays(2),
                'tanggal_ambil' => Carbon::now()->addDays(3),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 2,
                'status_id' => 2,
                'no_surat' => 'SKL-2025/002',
                'catatan' => 'Pengajuan ulang karena revisi',
                'lembar_revisi' => 'lembar_revisi_2.pdf',
                'ss_ajuan_skl' => 'ss_ajuan_skl_2.png',
                'tanggal_proses' => Carbon::now()->subDays(3),
                'tanggal_ambil' => Carbon::now()->addDays(2),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
