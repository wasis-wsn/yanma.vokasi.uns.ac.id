<?php

use App\Models\KategoriLayanan;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

function encodeId($id)
{
    return Crypt::encryptString($id);
}

function decodeId($id)
{
    return Crypt::decryptString($id);
}

function editDekan()
{
    $idDekan = User::where('role', '3')->first()->id;
    return route('user.edit', encodeId($idDekan));
}

function getLayanan()
{
    $kategoris = KategoriLayanan::with(['layanan' => function ($query) {
        $query->where('is_active', '1')->with('roles:roles.id,gate_name')->orderBy('urutan');
    }])->get();
    
    // Mengubah data gate_name menjadi array untuk setiap layanan
    foreach ($kategoris as $kategori) {
        foreach ($kategori->layanan as $layanan) {
            $layanan->gate = $layanan->roles->pluck('gate_name')->toArray();
        }
    }
    return $kategoris;
}