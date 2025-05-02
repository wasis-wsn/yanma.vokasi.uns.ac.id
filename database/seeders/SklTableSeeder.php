<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SklTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        
        $data = [
            [
                'user_id' => 1,
                'status_id' => 1,
                'no_surat' => 'SKL/2023/001',
                'lembar_revisi' => null,
                'ss_ajuan_skl' => 'path/to/screenshot1.png',
                'tanggal_proses' => $now->subDays(5),
                'tanggal_ambil' => $now->addDays(2),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 2,
                'status_id' => 2,
                'no_surat' => 'SKL/2023/002',
                'lembar_revisi' => 'path/to/revisi1.pdf',
                'ss_ajuan_skl' => 'path/to/screenshot2.png',
                'tanggal_proses' => $now->subDays(3),
                'tanggal_ambil' => $now->addDays(1),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 3,
                'status_id' => 3,
                'no_surat' => 'SKL/2023/003',
                'lembar_revisi' => null,
                'ss_ajuan_skl' => 'path/to/screenshot3.png',
                'tanggal_proses' => $now->subDays(2),
                'tanggal_ambil' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('skl')->insert($data);
    }
}