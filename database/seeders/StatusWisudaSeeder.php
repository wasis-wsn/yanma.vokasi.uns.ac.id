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
            ['id' =>  '3', 'name' => 'Tidak Terverifikasi', 'color' => 'btn-danger', 'gate' => '2'],
            ['id' =>  '4', 'name' => 'Bersedia', 'color' => 'btn-primary', 'gate' => '2'],
            ['id' =>  '5', 'name' => 'Tidak Bersedia', 'color' => 'btn-warning', 'gate' => '2'],
            ['id' =>  '6', 'name' => 'Valid', 'color' => 'btn-info', 'gate' => '2'],
            ['id' =>  '7', 'name' => 'Diproses', 'color' => 'btn-secondary', 'gate' => '2'],
        ]);
    }
}
