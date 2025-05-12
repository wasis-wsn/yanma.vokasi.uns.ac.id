<?php

namespace Database\Seeders;

use App\Models\StatusKemahasiswaan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusKemahasiswaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StatusKemahasiswaan::insert([
            ['id' =>  '1', 'name' => 'Belum Diproses', 'color' => 'btn-light', 'gate' => '2'],
            ['id' =>  '2', 'name' => 'Ajuan Dibatalkan Mahasiswa', 'color' => 'btn-danger', 'gate' => null],
            ['id' =>  '3', 'name' => 'Revisi', 'color' => 'btn-warning', 'gate' => '2'],
            ['id' =>  '4', 'name' => 'Ajuan Revisi', 'color' => 'btn-light', 'gate' => '2'],
            ['id' =>  '5', 'name' => 'Diproses', 'color' => 'btn-primary', 'gate' => '2'],
            ['id' =>  '6', 'name' => 'Diajukan WD 1', 'color' => 'btn-info', 'gate' => '2'],
            ['id' =>  '7', 'name' => 'Tidak Diproses', 'color' => 'btn-danger', 'gate' => '2'],
            ['id' =>  '8', 'name' => 'Tidak Disetujui WD 1', 'color' => 'btn-danger', 'gate' => '2'],
            ['id' =>  '9', 'name' => 'Selesai', 'color' => 'btn-success', 'gate' => '2'],
        ]);
    }
}
