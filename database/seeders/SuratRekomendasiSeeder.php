<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuratRekomendasi;
use Carbon\Carbon;

class SuratRekomendasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SuratRekomendasi::insert([
            [
                'nama' => 'Ahmad Fauzi',
                'nim' => '1122334455',
                'program_studi' => 'Teknik Elektro',
                'nomor_ijazah' => 'IJZ-2025-0101',
                'tanggal_lulus' => Carbon::parse('2025-06-30')->toDateString(),
                'file' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Rina Permata',
                'nim' => '5566778899',
                'program_studi' => 'Akuntansi',
                'nomor_ijazah' => 'IJZ-2024-0202',
                'tanggal_lulus' => Carbon::parse('2024-11-20')->toDateString(),
                'file' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
