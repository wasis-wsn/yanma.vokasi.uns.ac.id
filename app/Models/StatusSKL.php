<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusSKL extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'status_skl';
    protected $guarded = ['id'];

    /**
     * Get all of the skl for the StatusHeregistrasi
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skl()
    {
        return $this->hasMany(SKL::class, 'status_id', 'id');
    }
}
