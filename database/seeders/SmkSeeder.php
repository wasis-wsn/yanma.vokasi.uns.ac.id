<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SmkSeeder extends Seeder
{
    public function run()
    {
        DB::table('skmk')->insert([
            'user_id' => 1,
            'status_id' => 1,
            'tahun_akademik_id' => 1,
            'semester_id' => 1,
            'semester_romawi' => 'I',
            'nama_ortu' => 'Budi Santoso',
            'nip_ortu' => '198112312005011001',
            'pangkat_ortu' => 'Pembina',
            'instansi_ortu' => 'Kementerian Pendidikan',
            'alamat_instansi' => 'Jl. Sudirman No. 12, Jakarta',
            'catatan' => 'Semua dokumen lengkap',
            'file' => 'file_skmk.pdf',
            'surat_hasil' => 'surat_hasil_skmk.pdf',
            'no_surat' => '123/SKMK/IV/2025',
            'tanggal_proses' => Carbon::now()->format('Y-m-d'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
