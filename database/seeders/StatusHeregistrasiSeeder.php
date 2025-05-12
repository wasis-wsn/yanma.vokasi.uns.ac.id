<?php

namespace Database\Seeders;

use App\Models\StatusHeregistrasi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusHeregistrasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StatusHeregistrasi::insert([
            ['id' =>  '1', 'name' => 'Belum Diproses', 'color' => 'btn-light', 'gate' => '2'],
            ['id' =>  '2', 'name' => 'Revisi', 'color' => 'btn-warning', 'gate' => '2'],
            ['id' =>  '3', 'name' => 'Diproses', 'color' => 'btn-primary', 'gate' => '2'],
            ['id' =>  '4', 'name' => 'Diajukan Persetujuan ke WD 1', 'color' => 'btn-info', 'gate' => '2'],
            ['id' =>  '5', 'name' => 'Tidak Diproses', 'color' => 'btn-danger', 'gate' => '2'],
            ['id' =>  '6', 'name' => 'Selesai', 'color' => 'btn-success', 'gate' => '2'],
            ['id' =>  '7', 'name' => 'Sudah Diambil', 'color' => 'btn-success', 'gate' => '4'],
        ]);
    }
}
