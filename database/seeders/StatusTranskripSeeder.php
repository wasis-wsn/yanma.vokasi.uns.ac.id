<?php

namespace Database\Seeders;

use App\Models\StatusTranskrip;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusTranskripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StatusTranskrip::insert([
            ['id' =>  '1', 'name' => 'Diproses', 'color' => 'btn-primary', 'gate' => '2'],
            ['id' =>  '2', 'name' => 'Diajuakan Persetujuan ke WD 1', 'color' => 'btn-info', 'gate' => '2'],
            ['id' =>  '3', 'name' => 'Diajukan Pengesahan ke Dekan', 'color' => 'btn-info', 'gate' => '2'],
            ['id' =>  '4', 'name' => 'Selesai', 'color' => 'btn-success', 'gate' => '2'],
            ['id' =>  '5', 'name' => 'Sudah Diambil', 'color' => 'btn-success', 'gate' => '4'],
        ]);
    }
}
