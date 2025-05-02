<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SuratTugasSeeder extends Seeder
{
    public function run()
    {
        DB::table('surat_tugas')->insert([
            'user_id' => 1,
            'status_id' => 2,
            'catatan' => 'Segera diproses',
            'file' => 'surat_tugas_1.pdf',
            'surat_hasil' => 'hasil_surat_tugas_1.pdf',
            'no_surat' => '456/ST/IV/2025',
            'tanggal_proses' => Carbon::now()->format('Y-m-d'),
            'nama_kegiatan' => 'Workshop Data Science',
            'mulai_kegiatan' => Carbon::now()->format('Y-m-d'),
            'selesai_kegiatan' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'penyelenggara' => 'Universitas Teknologi',
            'tempat' => 'Aula Gedung B',
            'delegasi' => 'Tim Data Mahasiswa',
            'jumlah_peserta' => 10,
            'dospem' => 'Dr. Andi Wijaya',
            'nip_dospem' => '197805112005011002',
            'nidn_dospem' => '0023117801',
            'unit_dospem' => 'Program Studi Informatika',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
