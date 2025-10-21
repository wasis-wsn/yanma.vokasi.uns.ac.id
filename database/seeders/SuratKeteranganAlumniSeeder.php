<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuratKeteranganAlumni;
use Carbon\Carbon;

class SuratKeteranganAlumniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SuratKeteranganAlumni::insert([
            [
                'nama' => 'Budi Santoso',
                'nim' => '1234567890',
                'program_studi' => 'Teknik Informatika',
                'nomor_ijazah' => 'IJZ-2025-0001',
                'tanggal_lulus' => Carbon::parse('2025-07-01')->toDateString(),
                'file' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Siti Aminah',
                'nim' => '0987654321',
                'program_studi' => 'Manajemen',
                'nomor_ijazah' => 'IJZ-2025-0002',
                'tanggal_lulus' => Carbon::parse('2024-12-15')->toDateString(),
                'file' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
