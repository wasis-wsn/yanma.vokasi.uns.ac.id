<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    use HasFactory;

    protected $table = 'berita'; // Nama tabel di database

    protected $fillable = [
        'judul',
        'gambar',
        'deskripsi',
        'PDF',
        'tanggal'
    ];

    public $timestamps = false; // Tidak menggunakan created_at & updated_at
}
