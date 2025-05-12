<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranskripNilai extends Model
{
    use HasFactory;

    protected $table = 'transkrip_nilais';
    protected $guarded = ['id'];

    /**
     * Get the status that owns the TranskripNilai
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(StatusTranskrip::class, 'status_id', 'id');
    }

    /**
     * Get the user that owns the TranskripNilai
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
