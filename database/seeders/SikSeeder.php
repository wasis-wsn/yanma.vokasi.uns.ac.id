<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SikSeeder extends Seeder
{
    public function run()
    {
        DB::table('sik')->insert([
            'ormawa_id' => 1,
            'ketua_id' => 5,
            'status_id' => 2,
            'catatan' => 'Kegiatan berjalan dengan baik',
            'file' => 'proposal_kegiatan.pdf',
            'surat_hasil' => 'hasil_kegiatan.pdf',
            'no_surat' => '789/SIK/IV/2025',
            'tanggal_proses' => Carbon::now()->format('Y-m-d'),
            'nama_kegiatan' => 'Pelatihan Kepemimpinan Mahasiswa',
            'no_surat_ormawa' => '001/ORMAWA/X/2025',
            'tanggal_surat' => Carbon::now()->subDays(7)->format('Y-m-d'),
            'is_dana' => 1,
            'tanggal_lpj' => Carbon::now()->addDays(10)->format('Y-m-d'),
            'mulai_kegiatan' => Carbon::now()->format('Y-m-d'),
            'selesai_kegiatan' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'tempat' => 'Ruang Seminar Lantai 2',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
