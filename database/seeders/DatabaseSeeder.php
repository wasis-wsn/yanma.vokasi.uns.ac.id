<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            ContactSeeder::class,
            KategoriLayananSeeder::class,
            KeringananUKTSeeder::class,
            LayananSeeder::class,
            ProdiSeeder::class,
            PembinaOrmawaSeeder::class,
            RoleSeeder::class,
            LayananRoleSeeder::class,
            StatusHeregistrasiSeeder::class,
            StatusKemahasiswaanSeeder::class,
            StatusLegalisirSeeder::class,
            StatusLPJSeeder::class,
            StatusPengesahanTASeeder::class,
            StatusTranskripSeeder::class,
            StatusWisudaSeeder::class,
            StatusSKLSeeder::class,
            SemesterSeeder::class,
            UserSeeder::class,
        ]);
    }
}
