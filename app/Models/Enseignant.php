<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enseignant extends Model
{
    use SoftDeletes;

    protected $table = 'enseignants';
    protected $primaryKey = 'id';

    protected $fillable = [
        'matricule',
        'nom',
        'prenom',
        'email',
        'telephone',
        'statut',
        'date_recrutement',
        'id_grade',
        'id_departement',
        'id_utilisateur',
    ];

    protected function casts(): array
    {
        return [
            'date_recrutement' => 'date',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'id_grade', 'id');
    }

    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departement::class, 'id_departement', 'id');
    }

    /**
     * Lié à l'utilisateur via la table 'users'.
     */
    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur', 'id');
    }

    public function affectationsCours(): HasMany
    {
        return $this->hasMany(AffectationCours::class, 'id_enseignant', 'id');
    }

    public function etatsPaiement(): HasMany
    {
        return $this->hasMany(EtatPaiement::class, 'id_enseignant', 'id');
    }

    /**
     * Accesseur : nom complet de l'enseignant.
     */
    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }
}
