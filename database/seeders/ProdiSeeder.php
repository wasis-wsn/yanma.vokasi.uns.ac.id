<?php

namespace Database\Seeders;

use App\Models\Prodi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => '-'],
            ['name' => 'Universitas Sebelas Maret'],
            ['name' => 'Sarjana Terapan Keselamatan dan Kesehatan Kerja'],
            ['name' => 'Sarjana Terapan Demografi dan Pencatatan Sipil'],
            ['name' => 'Sarjana Terapan Keperawatan Anestesi'],
            ['name' => 'Diploma Tiga Teknik Informatika'],
            ['name' => 'Diploma Tiga Manajemen Administrasi'],
            ['name' => 'Diploma Tiga Teknik Kimia'],
            ['name' => 'Diploma Tiga Perpajakan'],
            ['name' => 'Diploma Tiga Bahasa Inggris'],
            ['name' => 'Diploma Tiga Teknik Sipil'],
            ['name' => 'Diploma Tiga Manajemen Perdagangan'],
            ['name' => 'Diploma Tiga Bahasa Mandarin'],
            ['name' => 'Diploma Tiga Manajemen Pemasaran'],
            ['name' => 'Diploma Tiga Desain Komunikasi Visual'],
            ['name' => 'Diploma Tiga Teknik Mesin'],
            ['name' => 'Diploma Tiga Manajemen Bisnis'],
            ['name' => 'Diploma Tiga Komunikasi Terapan'],
            ['name' => 'Diploma Tiga Budidaya Ternak'],
            ['name' => 'Diploma Tiga Keuangan Perbankan'],
            ['name' => 'Diploma Tiga Usaha Perjalanan Wisata'],
            ['name' => 'Diploma Tiga Teknologi Hasil Pertanian'],
            ['name' => 'Diploma Tiga Akuntansi'],
            ['name' => 'Diploma Tiga Agribisnis'],
            ['name' => 'Diploma Tiga Farmasi'],
            ['name' => 'Diploma Tiga Perpustakaan'],
            ['name' => 'Diploma Tiga Kebidanan'],
            ['name' => 'Diploma Tiga Teknik Informatika PSDKU'],
            ['name' => 'Diploma Tiga Akuntansi PSDKU'],
            ['name' => 'Diploma Tiga Teknologi Hasil Pertanian PSDKU'],
        ];

        foreach ($data as $i) {
            Prodi::create($i);
        }
    }
}
