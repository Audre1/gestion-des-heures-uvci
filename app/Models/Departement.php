<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departement extends Model
{
    use SoftDeletes;

    protected $table = 'departements';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nom_departement',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function enseignants(): HasMany
    {
        return $this->hasMany(Enseignant::class, 'id_departement', 'id');
    }

    public function filieres(): HasMany
    {
        return $this->hasMany(Filiere::class, 'id_departement', 'id');
    }
}
