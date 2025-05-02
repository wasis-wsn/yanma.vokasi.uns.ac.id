<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusLPJ extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'status_lpjs';
    protected $fillable = ['name'];

    /**
     * Get all of the lpj for the StatusLPJ
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lpj()
    {
        return $this->hasMany(Lpj::class, 'status_id', 'id');
    }
}
