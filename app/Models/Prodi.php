<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prodi extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'ref_prodi';
    protected $fillable = ['name'];

    protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {
        if (auth()->check()) {
            $model->created_at = auth()->user()->id;
        } else {
            $model->created_at = 1; // or null if your column is nullable
        }
    });

    static::updating(function ($model) {
        if (auth()->check()) {
            $model->updated_at = auth()->user()->id;
        }
    });
}

    /**
     * Get all of the user for the Prodi
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user()
    {
        return $this->hasMany(User::class, 'prodi', 'id');
    }

    /**
     * Get all of the legalisir for the Prodi
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function legalisir()
    {
        return $this->hasMany(Legalisir::class, 'prodi_id', 'id');
    }

    /**
     * Get all of the akreditasi for the Prodi
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function akreditasi()
    {
        return $this->hasMany(Akreditasi::class, 'prodi_id', 'id');
    }

    /**
     * Get all of the pembina for the Prodi
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pembina()
    {
        return $this->hasMany(PembinaOrmawa::class, 'unit_id', 'id');
    }
}
