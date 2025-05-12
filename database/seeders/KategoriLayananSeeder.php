<?php

namespace Database\Seeders;

use App\Models\KategoriLayanan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriLayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KategoriLayanan::insert([
            ['id' => '1', 'name' => 'Akademik', 'icon' => '<i class="fa-solid fa-tachograph-digital"></i>'],
            ['id' => '2', 'name' => 'Kemahasiswaan', 'icon' => '<i class="icon fa fa-file"></i>'],
            ['id' => '3', 'name' => 'Alumni', 'icon' => '<i class="fa-solid fa-graduation-cap"></i>'],
        ]);
    }
}
