<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status::insert([
            ['id' =>  '1', 'name' => 'Belum Diproses', 'color' => 'btn-light'],
            ['id' =>  '2', 'name' => 'Diproses', 'color' => 'btn-warning'],
            ['id' =>  '3', 'name' => 'Revisi', 'color' => 'btn-warning'],
            ['id' =>  '4', 'name' => 'Sudah Revisi', 'color' => 'btn-warning'],
            ['id' =>  '5', 'name' => 'Selesai', 'color' => 'btn-success'],
            ['id' =>  '6', 'name' => 'Ditolak', 'color' => 'btn-danger'],
            ['id' =>  '7', 'name' => 'Sudah Diambil', 'color' => 'btn-success'],
            ['id' =>  '8', 'name' => 'Belum Terverifikasi', 'color' => 'btn-warning'],
            ['id' =>  '9', 'name' => 'Sudah Terverifikasi', 'color' => 'btn-success'],
            ['id' =>  '10', 'name' => 'Diajukan persetujuan ke WD 1', 'color' => 'btn-secondary'],
            ['id' =>  '11', 'name' => 'Diajukan pengesahan ke Dekan', 'color' => 'btn-info'],
        ]);
    }
}
