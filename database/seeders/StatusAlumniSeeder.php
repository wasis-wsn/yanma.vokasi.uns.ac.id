<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusAlumniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['id' => 1, 'name' => 'Belum Diproses', 'color' => 'btn-light', 'gate' => 2],
            ['id' => 2, 'name' => 'Ajuan Dibatalkan Alumni', 'color' => 'btn-danger', 'gate' => 2],
            ['id' => 3, 'name' => 'Revisi', 'color' => 'btn-warning', 'gate' => 2],
            ['id' => 4, 'name' => 'Ajuan Revisi', 'color' => 'btn-light', 'gate' => 2],
            ['id' => 5, 'name' => 'Diproses', 'color' => 'btn-primary', 'gate' => 2],
            ['id' => 6, 'name' => 'Diajukan Pimpinan', 'color' => 'btn-info', 'gate' => 2],
            ['id' => 7, 'name' => 'Tidak Diproses', 'color' => 'btn-danger', 'gate' => 2],
            ['id' => 9, 'name' => 'Selesai', 'color' => 'btn-success', 'gate' => 2],
        ];

        foreach ($statuses as $status) {
            DB::table('status_alumni')->insert([
                'id' => $status['id'],
                'name' => $status['name'],
                'color' => $status['color'],
                'gate' => $status['gate'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
