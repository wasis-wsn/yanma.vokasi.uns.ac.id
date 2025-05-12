<?php

namespace Database\Seeders;

use App\Models\StatusLPJ;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusLPJSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StatusLPJ::insert([
            ['id' =>  '1', 'name' => 'Belum Upload', 'color' => 'btn-secondary', 'gate' => null],
            ['id' =>  '2', 'name' => 'Sudah Upload', 'color' => 'btn-primary', 'gate' => null],
            ['id' =>  '3', 'name' => 'Revisi', 'color' => 'btn-warning', 'gate' => '2'],
            ['id' =>  '4', 'name' => 'Tervalidasi', 'color' => 'btn-success', 'gate' => '2'],
        ]);
    }
}
