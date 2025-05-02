<?php

namespace Database\Seeders;

use App\Models\StatusPengesahanTA;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusPengesahanTASeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StatusPengesahanTA::insert([
            ['id' =>  '1', 'name' => 'Belum diproses', 'color' => 'btn-secondary', 'gate' => '4'],
            ['id' =>  '2', 'name' => 'Diproses', 'color' => 'btn-primary', 'gate' => '4'],
            ['id' =>  '3', 'name' => 'Diajukan ke Dekan SV', 'color' => 'btn-info', 'gate' => '4'],
            ['id' =>  '4', 'name' => 'Selesai', 'color' => 'btn-success', 'gate' => '4'],
            ['id' =>  '5', 'name' => 'Sudah Diambil', 'color' => 'btn-success', 'gate' => '4'],
        ]);
    }
}
