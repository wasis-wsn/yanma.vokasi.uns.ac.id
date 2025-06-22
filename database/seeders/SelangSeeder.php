<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SelangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $data = [
            [
                'user_id' => 1,
                'status_id' => 1,
                'catatan' => 'Catatan contoh untuk data pertama',
                'no_surat' => 'SURAT/001/2023',
                'semester_id' => 1,
                'tahun_akademik_id' => 1,
                'file' => 'file1.pdf',
                'alasan' => 'Alasan pengajuan pertama',
                'tanggal_proses' => Carbon::now()->subDays(5),
                'tanggal_ambil' => Carbon::now()->subDays(2),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 2,
                'status_id' => 2,
                'catatan' => 'Catatan contoh untuk data kedua',
                'no_surat' => 'SURAT/002/2023',
                'semester_id' => 2,
                'tahun_akademik_id' => 2,
                'file' => 'file2.pdf',
                'alasan' => 'Alasan pengajuan kedua yang lebih panjang',
                'tanggal_proses' => Carbon::now()->subDays(10),
                'tanggal_ambil' => Carbon::now()->subDays(1),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Tambahkan data lain sesuai kebutuhan
        ];

        DB::table('selang_cutis')->insert($data);
    }
}
