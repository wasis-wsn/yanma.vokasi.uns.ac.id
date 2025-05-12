<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Contact::insert([
            ['id' => '1', 'name' => 'Tanya Akademik', 'link' => 'https://api.whatsapp.com/send/?phone=6281326895436&text&type=phone_number&app_absent=0'],
            ['id' => '2', 'name' => 'Tanya Kemahasiswaan', 'link' => 'https://api.whatsapp.com/send/?phone=6281215891279&text&type=phone_number&app_absent=0'],
        ]);
    }
}
