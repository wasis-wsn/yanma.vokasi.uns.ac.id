<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Semester extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'semesters';
    protected $guarded = ['id'];
    
    public function suratKeterangan()
    {
        return $this->hasMany(SuratKeterangan::class, 'semester_id', 'id');
    }

    public function skmk()
    {
        return $this->hasMany(SKMK::class, 'semester_id', 'id');
    }
}
