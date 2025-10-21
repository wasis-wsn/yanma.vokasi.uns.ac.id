<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratRekomendasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat_rekomendasi';

    protected $fillable = [
        'nama',
        'nim',
        'program_studi',
        'nomor_ijazah',
        'tanggal_lulus',
        'file',
    ];

    protected $dates = [
        'tanggal_lulus',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
