<?php

namespace Database\Seeders;

use App\Models\Pemilihan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PemilihanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pemilihans = [
            [
                'name' => 'Pemilihan Presbem 2024',
                'slug' => Str::slug('Pemilihan Presbem 2024'),
                'jenis' => 'presbem',
                'deskripsi' => 'Pemilihan Presiden BEM Sekolah Vokasi periode 2024.',
                'mulai_at' => now()->subWeek(),
                'selesai_at' => now()->addWeeks(2),
                'is_active' => true,
                'candidates' => [
                    [
                        'nomor_urut' => 1,
                        'name' => 'Tim Visioner',
                        'visi' => 'Menjadikan BEM sebagai rumah perubahan.',
                        'misi' => 'Kolaborasi lintas ormawa dan penguatan layanan mahasiswa.',
                        'deskripsi' => 'Pasangan dengan fokus digitalisasi layanan ormawa.',
                    ],
                    [
                        'nomor_urut' => 2,
                        'name' => 'Tim Aksara',
                        'visi' => 'BEM yang adaptif dan komunikatif.',
                        'misi' => 'Menghadirkan program pemberdayaan mahasiswa yang merata.',
                        'deskripsi' => 'Menjanjikan program inkubasi dan dukungan lomba.',
                    ],
                ],
            ],
            [
                'name' => 'Pemilihan Caleg DEMA 2024',
                'slug' => Str::slug('Pemilihan Caleg DEMA 2024'),
                'jenis' => 'caleg',
                'deskripsi' => 'Pemilihan legislatif mahasiswa DEMA Sekolah Vokasi 2024.',
                'mulai_at' => now()->subDays(2),
                'selesai_at' => now()->addWeeks(3),
                'is_active' => true,
                'candidates' => [
                    [
                        'nomor_urut' => 1,
                        'name' => 'Anindya Cahya',
                        'visi' => 'Legislatif yang transparan.',
                        'misi' => 'Mengawal aspirasi mahasiswa secara periodik.',
                        'deskripsi' => 'Aktif pada komisi hubungan lembaga.',
                    ],
                    [
                        'nomor_urut' => 2,
                        'name' => 'Rafi Pratama',
                        'visi' => 'Legislatif progresif dan inklusif.',
                        'misi' => 'Mendorong regulasi beasiswa internal.',
                        'deskripsi' => 'Fokus pada penguatan akses finansial mahasiswa.',
                    ],
                    [
                        'nomor_urut' => 3,
                        'name' => 'Syifa Maulida',
                        'visi' => 'Legislatif siap kawal ormawa.',
                        'misi' => 'Menyiapkan kanal aduan dan pelaporan real-time.',
                        'deskripsi' => 'Berpengalaman memimpin BEM prodi.',
                    ],
                ],
            ],
        ];

        foreach ($pemilihans as $pemilihanData) {
            $candidates = $pemilihanData['candidates'];
            unset($pemilihanData['candidates']);

            $pemilihan = Pemilihan::updateOrCreate(
                ['slug' => $pemilihanData['slug']],
                $pemilihanData
            );

            foreach ($candidates as $candidateData) {
                $pemilihan->candidates()->updateOrCreate(
                    [
                        'pemilihan_id' => $pemilihan->id,
                        'nomor_urut' => $candidateData['nomor_urut'],
                    ],
                    $candidateData
                );
            }
        }
    }
}
