<?php

namespace Database\Seeders;

use App\Models\PembinaOrmawa;
use Illuminate\Database\Seeder;

class PembinaOrmawaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PembinaOrmawa::insert([
            ['name' => '-', 'unit_id' => '1'],
        ]);
    }
}
