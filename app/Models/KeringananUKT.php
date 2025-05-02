<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KeringananUKT extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'keringanan_ukts';
    protected $guarded = ['id', 'jenis'];
}
