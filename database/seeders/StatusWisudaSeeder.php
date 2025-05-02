<?php

namespace Database\Seeders;

use App\Models\StatusWisuda;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusWisudaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StatusWisuda::insert([
            ['id' =>  '1', 'name' => 'Belum Diproses', 'color' => 'btn-secondary', 'gate' => '2'],
            ['id' =>  '2', 'name' => 'Sudah Terverifikasi', 'color' => 'btn-success', 'gate' => '2'],
        ]);
    }
}
