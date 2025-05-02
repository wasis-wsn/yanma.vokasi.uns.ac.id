<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusPengesahanTA extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'status_pengesahan_t_a_s';
    protected $fillable = ['name'];

    /**
     * Get all of the pengajuanTTDTA for the StatusPengesahanTA
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pengajuanTTDTA()
    {
        return $this->hasMany(PengajuanTTDTA::class, 'status_id', 'id');
    }
}
