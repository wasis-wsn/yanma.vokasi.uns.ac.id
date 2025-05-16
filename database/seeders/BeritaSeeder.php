<?php

namespace Database\Seeders;

use App\Models\Berita;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BeritaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Berita::insert([
            [
                'judul' => 'Teknologi AI Meningkat Pesat',
                'gambar' => 'berita/ai.jpg', // Sesuaikan dengan gambar yang ada
                'deskripsi' => 'Artificial Intelligence terus berkembang dengan pesat di berbagai sektor...',
                'tanggal' => now()->subDays(3)->toDateString(),
            ],
            [
                'judul' => 'Ekonomi Digital di Indonesia',
                'gambar' => 'berita/ekonomi.jpg',
                'deskripsi' => 'Pertumbuhan ekonomi digital di Indonesia semakin pesat...',
                'tanggal' => now()->subDays(7)->toDateString(),
            ],
            [
                'judul' => 'Startup Lokal Mendunia',
                'gambar' => 'berita/startup.jpg',
                'deskripsi' => 'Banyak startup asal Indonesia yang kini bersaing di kancah global...',
                'tanggal' => now()->subDays(10)->toDateString(),
            ]
        ]);
    }
}
