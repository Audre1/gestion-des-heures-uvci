<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Enseignant extends Model
{
    use SoftDeletes;

    protected $table = 'enseignants';
    protected $primaryKey = 'id';

    protected $fillable = [
        'matricule',
        'statut',
        'taux_horaire_perso',
        'date_recrutement',
        'id_grade',
        'id_departement',
        'id_utilisateur',
    ];

    protected function casts(): array
    {
        return [
            'date_recrutement' => 'date',
            'taux_horaire_perso' => 'decimal:2',
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
        return $this->belongsTo(User::class, 'id_utilisateur', 'id');
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
     * Accesseur : nom complet de l'enseignant (récupéré depuis la table users).
     */
    public function getNomCompletAttribute(): string
    {
        return "{$this->utilisateur->prenom} {$this->utilisateur->nom}";
    }

    /**
     * Récupère le taux horaire de l'enseignant
     * Si le taux personnel est défini, l'utilise
     * Sinon, utilise le taux du grade pour l'année académique active
     */
    public function getTauxHoraire(?int $anneeId = null): float
    {
        // Si l'enseignant a un taux personnel, l'utiliser
        if ($this->taux_horaire_perso !== null) {
            return (float) $this->taux_horaire_perso;
        }

        // Sinon, récupérer le taux du grade pour l'année académique
        if (!$anneeId) {
            $anneeActive = AnneeAcademique::where('statut', 'active')->first();
            $anneeId = $anneeActive ? $anneeActive->id : null;
        }

        if ($anneeId && $this->grade) {
            $tauxHoraire = TauxHoraire::where('id_grade', $this->id_grade)
                ->where('id_annee', $anneeId)
                ->first();

            if ($tauxHoraire) {
                return (float) $tauxHoraire->montant;
            }
        }

        return 0.0;
    }
}
