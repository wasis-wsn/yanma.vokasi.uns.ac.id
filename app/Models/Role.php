<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'roles';
    protected $fillable = ['name'];

    /**
     * Get all of the user for the Role
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role');
    }

    public function layanans()
    {
        return $this->belongsToMany(Layanan::class, 'layanans_roles', 'roles_id', 'layanans_id');
    }
}
