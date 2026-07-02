<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cours extends Model
{
    use SoftDeletes;

    protected $table = 'cours';
    protected $primaryKey = 'id';

    protected $fillable = [
        'code_cours',
        'intitule',
        'nombre_heures',
        'nombre_credits',
        'semestre',
        'niveau',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function filieres(): BelongsToMany
    {
        return $this->belongsToMany(
            Filiere::class,
            'filiere_cours',
            'id_cours',
            'id_filiere'
        )->withTimestamps();
    }

    public function sequencesPedagogiques(): HasMany
    {
        return $this->hasMany(SequencePedagogique::class, 'id_cours', 'id');
    }

    public function affectationsCours(): HasMany
    {
        return $this->hasMany(AffectationCours::class, 'id_cours', 'id');
    }
}
