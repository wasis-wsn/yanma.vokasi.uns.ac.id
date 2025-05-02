<?php

namespace Database\Seeders;

use App\Models\KeringananUKT;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KeringananUKTSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KeringananUKT::insert([
            ['id' => '1', 'jenis' => 'Kurang Mampu', 'keterangan' => '', 'persyaratan' => ''],
            ['id' => '2', 'jenis' => 'Tugas Akhir', 'keterangan' => '', 'persyaratan' => ''],
        ]);
    }
}
