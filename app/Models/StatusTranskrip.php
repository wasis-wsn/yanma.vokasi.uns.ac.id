<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusTranskrip extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'status_transkrips';
    protected $fillable = ['name'];

    /**
     * Get all of the transkripNilai for the StatusTranskrip
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transkripNilai()
    {
        return $this->hasMany(TranskripNilai::class, 'status_id', 'id');
    }

    /**
     * Get all of the skpi for the StatusTranskrip
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skpi()
    {
        return $this->hasMany(SKPI::class, 'status_id', 'id');
    }
}
