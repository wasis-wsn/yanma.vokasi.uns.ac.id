<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusLegalisir extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'status_legalisirs';
    protected $fillable = ['name'];

    /**
     * Get all of the legalisir for the StatusLegalisir
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function legalisir()
    {
        return $this->hasMany(Legalisir::class, 'status_id', 'id');
    }
}
