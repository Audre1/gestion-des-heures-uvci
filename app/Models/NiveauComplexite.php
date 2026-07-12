<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class NiveauComplexite extends Model
{
    use SoftDeletes;

    protected $table = 'niveaux_complexite';
    protected $primaryKey = 'id';

    protected $fillable = [
        'libelle',
        'coefficient',
        'description',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function activitesPedagogiques(): HasMany
    {
        return $this->hasMany(ActivitePedagogique::class, 'id_niveau', 'id');
    }

    // ─── Méthodes métier ───────────────────────────────────────────────────────────

    /**
     * Vérifie si le niveau a des activités pédagogiques associées
     */
    public function hasActivites(): bool
    {
        return $this->activitesPedagogiques()->count() > 0;
    }

    /**
     * Compte le nombre d'activités pédagogiques associées
     */
    public function getActivitesCountAttribute(): int
    {
        return $this->activitesPedagogiques()->count();
    }
}
