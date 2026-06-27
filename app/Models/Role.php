<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    protected $table = 'roles';
    protected $primaryKey = 'id';

    protected $fillable = [
        'libelle',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function utilisateurs(): HasMany
    {
        return $this->hasMany(Utilisateur::class, 'id_role', 'id');
    }
}
