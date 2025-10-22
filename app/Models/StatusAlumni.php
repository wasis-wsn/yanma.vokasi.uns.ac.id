<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusAlumni extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'status_alumni';

    protected $fillable = [
        'name',
        'color',
        'gate',
    ];

    protected $casts = [
        'gate' => 'integer',
    ];
}
