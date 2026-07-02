<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model
{
    use SoftDeletes;

    protected $table = 'grades';
    protected $primaryKey = 'id';

    protected $fillable = [
        'libelle',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function enseignants(): HasMany
    {
        return $this->hasMany(Enseignant::class, 'id_grade', 'id');
    }

    public function tauxHoraires(): HasMany
    {
        return $this->hasMany(TauxHoraire::class, 'id_grade', 'id');
    }
}
