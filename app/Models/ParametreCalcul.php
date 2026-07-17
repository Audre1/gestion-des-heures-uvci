<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParametreCalcul extends Model
{
    protected $table = 'parametres_calcul';

    protected $fillable = [
        'annee_id',
        'heures_par_credit',
        'sequences_par_credit',
        'service_statutaire',
        'reduction_mise_a_jour',
        'sauvegarde_auto_delai',
        'sauvegarde_auto_rotation',
    ];

    protected $casts = [
        'heures_par_credit'     => 'integer',
        'sequences_par_credit'  => 'integer',
        'service_statutaire'    => 'integer',
        'reduction_mise_a_jour' => 'integer',
        'sauvegarde_auto_delai' => 'integer',
        'sauvegarde_auto_rotation' => 'integer',
    ];

    // ─── Relation ───────────────────────────────────────────

    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class, 'annee_id');
    }

    // ─── Méthodes privées ───────────────────────────────────

    /**
     * Calcule le ratio séquences/heure depuis les paramètres
     */
    private function getRatio(): float
    {
        return $this->sequences_par_credit / $this->heures_par_credit;
    }

    // ─── Accesseurs utiles ──────────────────────────────────

    /**
     * Retourne le coefficient selon le type d'activité et le niveau
     * Les coefficients MAJ sont calculés dynamiquement depuis la réduction
     *
     * @param string $type   'creation' ou 'maj'
     * @param int    $niveau  Numéro du niveau (1-based index)
     */
    public function getCoefficient(string $type, int $niveau): float
    {
        if (!in_array($type, ['creation', 'maj'])) {
            return 0.0;
        }

        // Récupérer TOUS les niveaux depuis la base, ordonnés par ID
        $niveaux = NiveauComplexite::orderBy('id', 'asc')->get();

        // Chercher le niveau par son index (niveau - 1 car tableau 0-based)
        $niveauComplexite = $niveaux->skip($niveau - 1)->first();

        // Si aucun niveau trouvé, retourner 0
        if (!$niveauComplexite) {
            return 0.0;
        }

        $coeffCreation = (float) $niveauComplexite->coefficient;

        if ($type === 'creation') {
            return $coeffCreation;
        }

        // Coefficient MAJ = coeff_creation × (1 - reduction/100)
        return round($coeffCreation * (1 - $this->reduction_mise_a_jour / 100), 3);
    }

    /**
     * Calcule le nombre de séquences à partir des heures
     */
    public function calculerSequencesDepuisHeures(int $nbHeures): int
    {
        return (int) ($nbHeures * $this->getRatio());
    }

    /**
     * Calcule le nombre de séquences à partir des crédits
     */
    public function calculerSequencesDepuisCredits(int $nbCredits): int
    {
        return $nbCredits * $this->sequences_par_credit;
    }

    /**
     * Calcule le Volume Horaire Total (VHT)
     *
     * @param int    $nbHeuresCours  Nombre d'heures du cours
     * @param string $type           'creation' ou 'maj'
     * @param int    $niveau          Numéro du niveau (1-based index)
     */
    public function calculerVHT(int $nbHeuresCours, string $type, int $niveau): float
    {
        $nbSequences = $this->calculerSequencesDepuisHeures($nbHeuresCours);
        $coefficient = $this->getCoefficient($type, $niveau);

        return round($nbSequences * $coefficient, 2);
    }

    /**
     * Calcule les heures complémentaires d'un enseignant
     *
     * @param float  $vhtTotal  Volume horaire total de l'enseignant
     * @param string $statut    'Permanent' ou 'Vacataire'
     */
    public function calculerHeuresComplementaires(float $vhtTotal, string $statut): float
    {
        if ($statut === 'Vacataire') {
            return $vhtTotal;
        }

        return max(0, $vhtTotal - $this->service_statutaire);
    }

    /**
     * Retourne la grille complète des coefficients pour l'affichage
     */
    public function getGrille(): array
    {
        $colonnes = [10, 20, 30];
        $grille   = [];

        // Récupérer TOUS les niveaux depuis la base, ordonnés par ID
        $niveaux = NiveauComplexite::orderBy('id', 'asc')->get();

        foreach (['creation', 'maj'] as $type) {
            foreach ($niveaux as $index => $niveau) {
                $niveauNum = $index + 1; // Convertir en 1-based index
                $coeff  = $this->getCoefficient($type, $niveauNum);
                $ligne  = [
                    'type'    => $type,
                    'niveau'  => $niveauNum,
                    'libelle' => $niveau->libelle, // Ajouter le libellé réel
                    'coeff'   => $coeff,
                    'valeurs' => [],
                ];

                foreach ($colonnes as $heures) {
                    $ligne['valeurs'][] = [
                        'heures'    => $heures,
                        'sequences' => $this->calculerSequencesDepuisHeures($heures),
                        'vht'       => $this->calculerVHT($heures, $type, $niveauNum),
                    ];
                }

                $grille[] = $ligne;
            }
        }

        return $grille;
    }

    // ─── Scopes ─────────────────────────────────────────────

    /**
     * Récupère les paramètres de l'année académique active
     */
    public function scopeAnneeActive($query)
    {
        return $query->whereHas('anneeAcademique', function ($q) {
            $q->where('statut', 'en_cours');
        });
    }
}
