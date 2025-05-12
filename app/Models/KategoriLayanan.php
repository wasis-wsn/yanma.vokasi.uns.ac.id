<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriLayanan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'kategori_layanans';
    protected $guarded = ['id'];

    /**
     * Get all of the layanan for the KategoriLayanan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function layanan()
    {
        return $this->hasMany(Layanan::class, 'kategori_layanan_id', 'id');
    }
}
