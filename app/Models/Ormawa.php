<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ormawa extends Model
{
    use HasFactory;

    protected $table = 'ormawa';
    protected $guarded = ['id'];

    /**
     * Get all of the SIK for the Ormawa
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sik()
    {
        return $this->hasMany(SIK::class, 'ormawa_id', 'id');
    }
}
