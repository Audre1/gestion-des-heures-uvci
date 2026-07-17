<?php

namespace App\Services;

use App\Models\ActivitePedagogique;
use App\Models\AnneeAcademique;
use App\Models\Departement;
use App\Models\Enseignant;
use App\Models\EtatPaiement;
use App\Models\ParametreCalcul;

class RapportService
{
    /**
     * Génère la fiche individuelle d'un enseignant
     */
    public function ficheIndividuelleEnseignant(int $enseignantId, ?int $anneeId = null): array
    {
        $enseignant = Enseignant::with(['utilisateur', 'grade', 'departement'])
            ->findOrFail($enseignantId);

        $annee = $anneeId ? AnneeAcademique::findOrFail($anneeId) : AnneeAcademique::where('statut', 'en_cours')->first();

        if (!$annee) {
            throw new \Exception('Aucune année académique trouvée');
        }

        // Récupérer les activités de l'enseignant pour l'année
        $activites = ActivitePedagogique::whereHas('affectationCours.enseignant', function ($query) use ($enseignantId) {
            $query->where('id', $enseignantId);
        })
            ->whereHas('affectationCours.cours', function ($query) use ($annee) {
                $query->where('id_annee', $annee->id);
            })
            ->with(['affectationCours.cours', 'niveauComplexite', 'ressourcePedagogique'])
            ->get();

        // Calculer les totaux
        $totalVHT = $activites->sum('volume_horaire');
        $totalSequences = $activites->sum('nb_sequences');
        $parametres = ParametreCalcul::where('annee_id', $annee->id)->first();

        // Service statutaire uniquement pour les permanents
        $serviceStatutaire = ($enseignant->statut === 'Permanent')
            ? ($parametres ? $parametres->service_statutaire : 192)
            : 0;

        // Heures complémentaires selon le statut
        $heuresComplementaires = ($enseignant->statut === 'Permanent')
            ? max(0, $totalVHT - $serviceStatutaire)
            : $totalVHT; // Pour les vacataires, tout est complémentaire

        // Récupérer le taux horaire
        $tauxHoraire = $enseignant->getTauxHoraire($annee->id);
        $montantEstime = $heuresComplementaires * $tauxHoraire;

        return [
            'enseignant' => $enseignant,
            'annee' => $annee,
            'activites' => $activites,
            'total_vht' => $totalVHT,
            'total_sequences' => $totalSequences,
            'service_statutaire' => $serviceStatutaire,
            'heures_complementaires' => $heuresComplementaires,
            'taux_horaire' => $tauxHoraire,
            'montant_estime' => $montantEstime,
        ];
    }

    /**
     * Génère l'état global des heures
     */
    public function etatGlobalHeures(?int $anneeId = null): array
    {
        $annee = $anneeId ? AnneeAcademique::findOrFail($anneeId) : AnneeAcademique::where('statut', 'en_cours')->first();

        if (!$annee) {
            throw new \Exception('Aucune année académique trouvée');
        }

        $enseignants = Enseignant::with(['utilisateur', 'grade', 'departement'])
            ->whereHas('affectationsCours.cours', function ($query) use ($annee) {
                $query->where('id_annee', $annee->id);
            })
            ->get();

        $data = [];
        $totalVHT = 0;
        $totalHeuresComplementaires = 0;
        $parametres = ParametreCalcul::where('annee_id', $annee->id)->first();
        $serviceStatutaireGlobal = $parametres ? $parametres->service_statutaire : 192;

        foreach ($enseignants as $enseignant) {
            $activites = ActivitePedagogique::whereHas('affectationCours.enseignant', function ($query) use ($enseignant) {
                $query->where('id', $enseignant->id);
            })
                ->whereHas('affectationCours.cours', function ($query) use ($annee) {
                    $query->where('id_annee', $annee->id);
                })
                ->get();

            $vht = $activites->sum('volume_horaire');

            // Service statutaire et heures complémentaires selon le statut
            $serviceStatutaire = ($enseignant->statut === 'Permanent')
                ? $serviceStatutaireGlobal
                : 0;

            $heuresComplementaires = ($enseignant->statut === 'Permanent')
                ? max(0, $vht - $serviceStatutaire)
                : $vht; // Pour les vacataires, tout est complémentaire

            $totalVHT += $vht;
            $totalHeuresComplementaires += $heuresComplementaires;

            $data[] = [
                'enseignant' => $enseignant,
                'vht' => $vht,
                'service_statutaire' => $serviceStatutaire,
                'heures_complementaires' => $heuresComplementaires,
                'statut' => $enseignant->statut,
            ];
        }

        return [
            'annee' => $annee,
            'enseignants' => $data,
            'total_vht' => $totalVHT,
            'total_heures_complementaires' => $totalHeuresComplementaires,
        ];
    }

    /**
     * Génère les statistiques pédagogiques
     */
    public function statistiquesPedagogiques(?int $anneeId = null): array
    {
        $annee = $anneeId ? AnneeAcademique::findOrFail($anneeId) : AnneeAcademique::where('statut', 'en_cours')->first();

        if (!$annee) {
            throw new \Exception('Aucune année académique trouvée');
        }

        $activites = ActivitePedagogique::whereHas('affectationCours.cours', function ($query) use ($annee) {
            $query->where('id_annee', $annee->id);
        })
            ->with(['affectationCours.enseignant.departement', 'niveauComplexite'])
            ->get();

        // Répartition par type
        $parType = [
            'creation' => $activites->where('type_activite', 'creation')->count(),
            'maj' => $activites->where('type_activite', 'maj')->count(),
        ];

        // Répartition par niveau
        $parNiveau = [];
        foreach ($activites->groupBy('niveauComplexite.libelle') as $niveau => $acts) {
            $parNiveau[$niveau] = $acts->count();
        }

        // Répartition par département
        $parDepartement = [];
        foreach ($activites->groupBy('affectationCours.enseignant.departement.nom_departement') as $dept => $acts) {
            $parDepartement[$dept] = $acts->count();
        }

        // Statistiques globales
        $totalActivites = $activites->count();
        $totalVHT = $activites->sum('volume_horaire');
        $totalSequences = $activites->sum('nb_sequences');

        return [
            'annee' => $annee,
            'total_activites' => $totalActivites,
            'total_vht' => $totalVHT,
            'total_sequences' => $totalSequences,
            'par_type' => $parType,
            'par_niveau' => $parNiveau,
            'par_departement' => $parDepartement,
        ];
    }

    /**
     * Génère l'état des heures complémentaires
     */
    public function etatHeuresComplementaires(?int $anneeId = null): array
    {
        $annee = $anneeId ? AnneeAcademique::findOrFail($anneeId) : AnneeAcademique::where('statut', 'en_cours')->first();

        if (!$annee) {
            throw new \Exception('Aucune année académique trouvée');
        }

        $enseignants = Enseignant::with(['utilisateur', 'grade', 'departement'])
            ->whereHas('affectationsCours.cours', function ($query) use ($annee) {
                $query->where('id_annee', $annee->id);
            })
            ->get();

        $data = [];
        $totalHeuresComplementaires = 0;
        $totalMontant = 0;
        $parametres = ParametreCalcul::where('annee_id', $annee->id)->first();
        $serviceStatutaireGlobal = $parametres ? $parametres->service_statutaire : 192;

        foreach ($enseignants as $enseignant) {
            $activites = ActivitePedagogique::whereHas('affectationCours.enseignant', function ($query) use ($enseignant) {
                $query->where('id', $enseignant->id);
            })
                ->whereHas('affectationCours.cours', function ($query) use ($annee) {
                    $query->where('id_annee', $annee->id);
                })
                ->get();

            $vht = $activites->sum('volume_horaire');

            // Service statutaire et heures complémentaires selon le statut
            $serviceStatutaire = ($enseignant->statut === 'Permanent')
                ? $serviceStatutaireGlobal
                : 0;

            $heuresComplementaires = ($enseignant->statut === 'Permanent')
                ? max(0, $vht - $serviceStatutaire)
                : $vht; // Pour les vacataires, tout est complémentaire

            if ($heuresComplementaires > 0) {
                $tauxHoraire = $enseignant->getTauxHoraire($annee->id);
                $montant = $heuresComplementaires * $tauxHoraire;

                $totalHeuresComplementaires += $heuresComplementaires;
                $totalMontant += $montant;

                $data[] = [
                    'enseignant' => $enseignant,
                    'vht' => $vht,
                    'service_statutaire' => $serviceStatutaire,
                    'heures_complementaires' => $heuresComplementaires,
                    'taux_horaire' => $tauxHoraire,
                    'montant' => $montant,
                ];
            }
        }

        // Trier par montant décroissant
        usort($data, function ($a, $b) {
            return $b['montant'] <=> $a['montant'];
        });

        return [
            'annee' => $annee,
            'enseignants' => $data,
            'total_heures_complementaires' => $totalHeuresComplementaires,
            'total_montant' => $totalMontant,
        ];
    }

    /**
     * Génère l'état de paiement collectif
     */
    public function etatPaiementCollectif(?int $anneeId = null, ?string $periode = null): array
    {
        $annee = $anneeId ? AnneeAcademique::findOrFail($anneeId) : AnneeAcademique::where('statut', 'en_cours')->first();

        if (!$annee) {
            throw new \Exception('Aucune année académique trouvée');
        }

        $query = EtatPaiement::with(['enseignant.utilisateur', 'anneeAcademique'])
            ->where('id_annee', $annee->id);

        if ($periode) {
            $query->where('periode', $periode);
        }

        $etats = $query->get();

        $totalMontant = $etats->sum('montant_total');

        return [
            'annee' => $annee,
            'periode' => $periode,
            'etats' => $etats,
            'total_montant' => $totalMontant,
        ];
    }

    /**
     * Génère la charge par département
     */
    public function chargeParDepartement(?int $anneeId = null): array
    {
        $annee = $anneeId ? AnneeAcademique::findOrFail($anneeId) : AnneeAcademique::where('statut', 'en_cours')->first();

        if (!$annee) {
            throw new \Exception('Aucune année académique trouvée');
        }

        $departements = Departement::with(['enseignants.utilisateur', 'filieres'])
            ->whereHas('enseignants.affectationsCours.cours', function ($query) use ($annee) {
                $query->where('id_annee', $annee->id);
            })
            ->get();

        $data = [];
        $totalVHT = 0;

        foreach ($departements as $departement) {
            $enseignants = $departement->enseignants()
                ->whereHas('affectationsCours.cours', function ($query) use ($annee) {
                    $query->where('id_annee', $annee->id);
                })
                ->get();

            $deptVHT = 0;
            $enseignantsData = [];

            foreach ($enseignants as $enseignant) {
                $activites = ActivitePedagogique::whereHas('affectationCours.enseignant', function ($query) use ($enseignant) {
                    $query->where('id', $enseignant->id);
                })
                    ->whereHas('affectationCours.cours', function ($query) use ($annee) {
                        $query->where('id_annee', $annee->id);
                    })
                    ->get();

                $vht = $activites->sum('volume_horaire');
                $deptVHT += $vht;

                $enseignantsData[] = [
                    'enseignant' => $enseignant,
                    'vht' => $vht,
                ];
            }

            $totalVHT += $deptVHT;

            $data[] = [
                'departement' => $departement,
                'enseignants' => $enseignantsData,
                'vht' => $deptVHT,
                'nb_enseignants' => count($enseignantsData),
            ];
        }

        // Trier par VHT décroissant
        usort($data, function ($a, $b) {
            return $b['vht'] <=> $a['vht'];
        });

        return [
            'annee' => $annee,
            'departements' => $data,
            'total_vht' => $totalVHT,
        ];
    }
}
