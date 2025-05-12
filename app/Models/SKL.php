<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SKL extends Model
{
    use HasFactory;

    protected $table = 'skl';
    protected $guarded = ['id'];

    /**
     * Get the status that owns the SKL
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(StatusSKL::class, 'status_id', 'id');
    }

    /**
     * Get the user that owns the SKL
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
