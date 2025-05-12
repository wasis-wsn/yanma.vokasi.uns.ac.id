<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusWisuda extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'status_wisudas';
    protected $fillable = ['name'];

    /**
     * Get all of the verifikasiWisuda for the StatusWisuda
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function verifikasiWisuda()
    {
        return $this->hasMany(VerifikasiWisuda::class, 'status_id', 'id');
    }
}
