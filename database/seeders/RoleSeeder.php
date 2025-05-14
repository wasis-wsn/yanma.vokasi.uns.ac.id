<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            ['id' => 1, 'name' => 'Mahasiswa', 'gate_name' => 'mahasiswa'],
            ['id' => 2, 'name' => 'Staff Akademik', 'gate_name' => 'staff'],
            ['id' => 3, 'name' => 'Dekanat', 'gate_name' => 'dekanat'],
            ['id' => 4, 'name' => 'Front Office', 'gate_name' => 'fo'],
            ['id' => 5, 'name' => 'Ormawa', 'gate_name' => 'ormawa'],
            ['id' => 6, 'name' => 'Sub Koor Akademik', 'gate_name' => 'subkoor'],
            ['id' => 7, 'name' => 'Admin Prodi', 'gate_name' => 'adminprodi'],
        ]);
    }
}
