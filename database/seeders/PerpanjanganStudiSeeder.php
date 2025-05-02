<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PerpanjanganStudiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('perpanjangan_studis')->insert([
            [
                'user_id' => 1,
                'status_id' => 1,
                'queue_number' => 101,
                'queue_status' => 'waiting',
                'catatan' => 'Dokumen lengkap',
                'no_surat' => '123/UNS/VIII/2025',
                'file' => 'perpanjangan_1.pdf',
                'semester_id' => 5,
                'tahun_akademik_id' => 3,
                'perpanjangan_ke' => 1,
                'tanggal_proses' => Carbon::now(),
                'tanggal_ambil' => Carbon::now()->addDays(3),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 2,
                'status_id' => 2,
                'queue_number' => 102,
                'queue_status' => 'processed',
                'catatan' => 'Sudah diverifikasi',
                'no_surat' => '124/UNS/VIII/2025',
                'file' => 'perpanjangan_2.pdf',
                'semester_id' => 6,
                'tahun_akademik_id' => 3,
                'perpanjangan_ke' => 2,
                'tanggal_proses' => Carbon::now()->subDays(1),
                'tanggal_ambil' => Carbon::now()->addDays(2),
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
