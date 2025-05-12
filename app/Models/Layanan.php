<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Layanan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'layanans';
    protected $guarded = ['id'];

    /**
     * Get all of the template for the Layanan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function template()
    {
        return $this->hasMany(Template::class, 'layanan_id', 'id');
    }

    /**
     * Get the kategoriLayanan that owns the Layanan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kategoriLayanan()
    {
        return $this->belongsTo(KategoriLayanan::class, 'kategori_layanan_id', 'id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'layanans_roles', 'layanans_id', 'roles_id');
    }
}
