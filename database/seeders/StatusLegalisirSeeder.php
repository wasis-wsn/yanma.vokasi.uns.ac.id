<?php

namespace Database\Seeders;

use App\Models\StatusLegalisir;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusLegalisirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StatusLegalisir::insert([
            ['id' =>  '1', 'name' => 'Diproses', 'color' => 'btn-primary', 'gate' => '4'],
            ['id' =>  '2', 'name' => 'Diajukan Pengesahan ke Dekan', 'color' => 'btn-info', 'gate' => '4'],
            ['id' =>  '3', 'name' => 'Selesai', 'color' => 'btn-success', 'gate' => '4'],
            ['id' =>  '4', 'name' => 'Sudah Diambil', 'color' => 'btn-success', 'gate' => '4'],
        ]);
    }
}
