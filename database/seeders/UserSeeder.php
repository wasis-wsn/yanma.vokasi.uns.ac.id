<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Mahasiswa 1',
                'email' => 'mahasiswa1@student.uns.ac.id',
                'password' => Hash::make('mahasiswa321'),
                'nim' => 'V3412325',
                'no_wa' => '081234567890',
                'prodi' => '1',
                'role' => '1',
            ],
            [
                'name' => 'Mahasiswa 2',
                'email' => 'mahasiswa2@student.uns.ac.id',
                'password' => Hash::make('mahasiswa321'),
                'nim' => 'V3412326',
                'no_wa' => '081234567890',
                'prodi' => '1',
                'role' => '1',
            ],
            [
                'name' => 'Mahasiswa 3',
                'email' => 'mahasiswa3@student.uns.ac.id',
                'password' => Hash::make('mahasiswa321'),
                'nim' => 'V3412327',
                'no_wa' => '081234567890',
                'prodi' => '1',
                'role' => '1',
            ],
            [
                'name' => 'Staff Akademik',
                'email' => 'staffakademik@sv.uns.ac.id',
                'password' => Hash::make('akademikadmin321'),
                'prodi' => '1',
                'role' => '2',
            ],
            [
                'name' => 'Agus Dwi Priyanto, S.S., M.CALL',
                'email' => 'wdakademik@sv.uns.ac.id',
                'password' => Hash::make('akademikwd321'),
                'nim' => '197408182000121001',
                'pangkat' => 'Penata Tk .I/ IIId',
                'jabatan' => 'Plt. Wakil Dekan Akademik, Riset, dan Kemahasiswaan',
                'prodi' => '1',
                'role' => '3',
            ],
            [
                'name' => 'Front Office 1',
                'email' => 'fo1@fo.uns.ac.id',
                'password' => Hash::make('FrontOffice321'),
                'prodi' => '1',
                'role' => '4',
            ],
            [
                'name' => 'Front Office 2',
                'email' => 'fo2@fo.uns.ac.id',
                'password' => Hash::make('FrontOffice321'),
                'prodi' => '1',
                'role' => '4',
            ],
            [
                'name' => 'Ormawa Test',
                'email' => 'ormawa@unit.uns.ac.id',
                'password' => Hash::make('ormawa321'),
                'nim' => 'Test01',
                'pembina_id' => '1',
                'prodi' => '1',
                'role' => '5',
            ],
            [
                'name' => 'Subkoor Akademik',
                'email' => 'subkoor@staff.uns.ac.id',
                'password' => Hash::make('subkoor321'),
                'prodi' => '1',
                'role' => '6',
            ],
            [
                'name' => 'Subkoor Akademik',
                'email' => 'subkoor@staff.uns.ac.id',
                'password' => Hash::make('subkoor321'),
                'prodi' => '1',
                'role' => '6',
            ],
            [
                'name' => 'Admin Prodi',
                'email' => 'adminprodi@staff.uns.ac.id',
                'password' => Hash::make('adminprodi321'),
                'prodi' => '1',
                'role' => '7',
            ],
        ];

        foreach ($data as $i) {
            User::create($i);
        }
    }
}
