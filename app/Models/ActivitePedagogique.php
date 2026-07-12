<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivitePedagogique extends Model
{
    use SoftDeletes;

    protected $table = 'activites_pedagogiques';
    protected $primaryKey = 'id';

    protected $fillable = [
        'type_activite',
        'date_activite',
        'statut',
        'coefficient',
        'nb_sequences',
        'volume_horaire',
        'id_affectation',
        'id_ressource',
        'id_niveau',
    ];

    protected function casts(): array
    {
        return [
            'date_activite'  => 'date',
            'coefficient'    => 'decimal:3',
            'nb_sequences'   => 'integer',
            'volume_horaire' => 'decimal:2',
        ];
    }

    // ─── Relations ──────────────────────────────────────────

    public function affectationCours(): BelongsTo
    {
        return $this->belongsTo(AffectationCours::class, 'id_affectation', 'id');
    }

    public function ressourcePedagogique(): BelongsTo
    {
        return $this->belongsTo(RessourcePedagogique::class, 'id_ressource', 'id');
    }

    public function niveauComplexite(): BelongsTo
    {
        return $this->belongsTo(NiveauComplexite::class, 'id_niveau', 'id');
    }

    // ─── Accesseurs ─────────────────────────────────────────

    /**
     * Raccourci pour accéder à l'enseignant via l'affectation
     */
    public function getEnseignantAttribute()
    {
        return $this->affectationCours->enseignant;
    }

    /**
     * Raccourci pour accéder au cours via l'affectation
     */
    public function getCoursAttribute()
    {
        return $this->affectationCours->cours;
    }

    // ─── Méthodes métier ────────────────────────────────────

    /**
     * Calcule et remplit automatiquement coefficient, nb_sequences et volume_horaire
     * avant la création de l'activité
     */
    public static function boot(): void
    {
        parent::boot();

        static::creating(function (ActivitePedagogique $activite) {
            $activite->calculerEtRemplir();
        });
    }

    private function calculerEtRemplir(): void
    {
        $params = ParametreCalcul::anneeActive()->first();
        
        if (!$params) {
            throw new \Exception('Aucun paramètre de calcul actif trouvé. Veuillez configurer les paramètres pour l\'année académique en cours.');
        }

        // Charger l'affectation avec ses relations pour éviter les requêtes N+1
        $this->load('affectationCours.cours');
        $affectation = $this->affectationCours;
        
        if (!$affectation) {
            throw new \Exception('Affectation de cours introuvable.');
        }

        $cours = $affectation->cours;
        if (!$cours) {
            throw new \Exception('Cours associé à l\'affectation introuvable.');
        }

        $type = $this->type_activite === 'creation' ? 'creation' : 'maj';
        
        // Récupérer le niveau de complexité pour vérifier son existence
        $niveauComplexite = NiveauComplexite::where('id', $this->id_niveau)->first();
        if (!$niveauComplexite) {
            throw new \Exception('Niveau de complexité introuvable.');
        }

        // Calculer les séquences depuis les heures du cours
        $this->nb_sequences = $params->calculerSequencesDepuisHeures($cours->nombre_heures);
        
        // Récupérer le coefficient en utilisant l'index du niveau (1-based)
        $niveauIndex = NiveauComplexite::orderBy('id', 'asc')->get()->search(function ($item) use ($niveauComplexite) {
            return $item->id === $niveauComplexite->id;
        });
        
        if ($niveauIndex === false) {
            throw new \Exception('Impossible de déterminer l\'index du niveau de complexité.');
        }
        
        $niveauNum = $niveauIndex + 1; // Convertir en 1-based index
        $this->coefficient = $params->getCoefficient($type, $niveauNum);
        
        // Calculer le VHT
        $this->volume_horaire = $params->calculerVHT($cours->nombre_heures, $type, $niveauNum);
    }

    /**
     * Vérifie si l'activité est validée
     */
    public function estValidee(): bool
    {
        return $this->statut === 'validee';
    }
}
