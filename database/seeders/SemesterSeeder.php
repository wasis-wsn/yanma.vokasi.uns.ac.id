<?php

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Semester::insert([
            ['id' => '1', 'semester' => 'Gasal'],
            ['id' => '2', 'semester' => 'Genap'],
        ]);
    }
}
