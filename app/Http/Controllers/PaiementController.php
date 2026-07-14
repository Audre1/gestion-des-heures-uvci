<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Models\Enseignant;
use App\Models\EtatPaiement;
use App\Models\ParametreCalcul;
use App\Http\Requests\GenerateEtatPaiementRequest;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    public function index(Request $request)
    {
        $anneeId = $request->get('annee_id');
        $statut = $request->get('statut');
        $anneeActive = AnneeAcademique::where('statut', 'en_cours')->first();

        if (!$anneeId && $anneeActive) {
            $anneeId = $anneeActive->id;
        }

        // Récupérer les états de paiement avec filtres
        $query = EtatPaiement::with(['enseignant.utilisateur', 'enseignant.grade', 'anneeAcademique']);

        if ($anneeId) {
            $query->where('id_annee', $anneeId);
        }

        $etatsPaiement = $query->orderBy('date_generation', 'desc')->get();

        // Calculer les totaux
        $totalMontant = $etatsPaiement->sum('montant_total');
        $totalEnseignants = $etatsPaiement->pluck('id_enseignant')->unique()->count();

        // Récupérer les années académiques pour le filtre
        $annees = AnneeAcademique::orderBy('date_debut', 'desc')->get();

        // Statistiques par statut
        $statsParStatut = [
            'en_attente' => $etatsPaiement->where('statut', 'en_attente')->count(),
            'valide' => $etatsPaiement->where('statut', 'valide')->count(),
            'paye' => $etatsPaiement->where('statut', 'paye')->count(),
            'rejete' => $etatsPaiement->where('statut', 'rejete')->count(),
        ];

        return view('paiements.index', compact(
            'etatsPaiement',
            'annees',
            'anneeId',
            'anneeActive',
            'statut',
            'totalMontant',
            'totalEnseignants',
            'statsParStatut'
        ));
    }

    /**
     * Générer un état de paiement pour un enseignant
     */
    public function generate(GenerateEtatPaiementRequest $request)
    {
        if (function_exists('logActivite')) {
            logActivite('création', 'Génération d\'état de paiement pour enseignant, période: ' . $request->periode);
        }

        $enseignant = Enseignant::with(['utilisateur', 'grade', 'affectationsCours.activitesPedagogiques', 'affectationsCours.cours'])
            ->findOrFail($request->id_enseignant);

        // Calculer les volumes horaires
        $vhtTotal = 0;
        $nbCours = 0;

        foreach ($enseignant->affectationsCours as $affectation) {
            if ($affectation->id_annee == $request->id_annee) {
                foreach ($affectation->activitesPedagogiques as $activite) {
                    if ($activite->statut === 'validee') {
                        $vhtTotal += $activite->volume_horaire;
                        $nbCours++;
                    }
                }
            }
        }

        // Calculer le service statutaire et heures complémentaires
        $serviceStatutaire = $this->getServiceStatutaire($enseignant->grade->libelle ?? null, $enseignant->statut ?? null);
        $heuresComplementaires = ($enseignant->statut !== 'Vacataire') ? max(0, $vhtTotal - $serviceStatutaire) : 0;

        // Récupérer le taux horaire
        $tauxHoraire = $enseignant->getTauxHoraire($request->id_annee);

        // Calculer le montant
        $montant = 0;

        if ($enseignant->statut === 'Vacataire') {
            // Pour les vacataires, toutes les heures sont payées
            $montant = $vhtTotal * $tauxHoraire;
        } else {
            // Pour les permanents, seules les heures complémentaires sont payées
            $montant = $heuresComplementaires * $tauxHoraire;
        }

        if (function_exists('logActivite')) {
            logActivite('calcul', 'Calcul montant paiement - Enseignant: ' . $enseignant->utilisateur->nom . ' ' . $enseignant->utilisateur->prenom . ', VHT: ' . $vhtTotal . 'h, Montant: ' . $montant . ' FCFA');
        }

        // Générer un numéro de paiement unique (PAY-YYMM-XXX)
        // Compter les états de paiement du mois en cours pour éviter les doublons
        $countThisMonth = EtatPaiement::whereYear('date_generation', now()->year)
            ->whereMonth('date_generation', now()->month)
            ->count();
        $numeroPaiement = 'PAY-' . now()->format('ym') . '-' . str_pad($countThisMonth + 1, 3, '0', STR_PAD_LEFT);

        // Créer l'état de paiement
        $etatPaiement = EtatPaiement::create([
            'numero_paiement' => $numeroPaiement,
            'date_generation' => now(),
            'periode' => $request->periode,
            'montant_total' => $montant,
            'statut' => 'en_attente',
            'format_export' => 'pdf',
            'id_enseignant' => $enseignant->id,
            'id_annee' => $request->id_annee,
        ]);

        if (function_exists('logActivite')) {
            logActivite('création', 'État de paiement créé - Numéro: ' . $numeroPaiement . ', Montant: ' . $montant . ' FCFA', $etatPaiement);
        }

        return redirect()->route('paiements.index')
            ->with('success', 'État de paiement généré avec succès.');
    }

    /**
     * Valider un état de paiement
     */
    public function valider(int $id)
    {
        $etatPaiement = EtatPaiement::findOrFail($id);

        if (function_exists('logActivite')) {
            logActivite('modification', 'Validation état de paiement, statut: ' . $etatPaiement->statut . ' -> valide', $etatPaiement);
        }

        $etatPaiement->update(['statut' => 'valide']);

        if (function_exists('logActivite')) {
            logActivite('modification', 'État de paiement validé - Montant: ' . $etatPaiement->montant_total . ' FCFA', $etatPaiement);
        }

        return redirect()->route('paiements.index')
            ->with('success', 'État de paiement validé avec succès.');
    }

    /**
     * Marquer un état de paiement comme payé
     */
    public function marquerPaye(int $id)
    {
        $etatPaiement = EtatPaiement::findOrFail($id);

        if (function_exists('logActivite')) {
            logActivite('modification', 'Marquage comme payé état paiement, Montant: ' . $etatPaiement->montant_total . ' FCFA', $etatPaiement);
        }

        $etatPaiement->update(['statut' => 'paye']);

        if (function_exists('logActivite')) {
            logActivite('modification', 'État de paiement marqué payé', $etatPaiement);
        }

        return redirect()->route('paiements.index')
            ->with('success', 'État de paiement marqué comme payé.');
    }

    /**
     * Rejeter un état de paiement
     */
    public function rejeter(int $id)
    {
        $etatPaiement = EtatPaiement::findOrFail($id);

        if (function_exists('logActivite')) {
            logActivite('modification', 'Rejet état paiement, statut: ' . $etatPaiement->statut . ' -> rejete', $etatPaiement);
        }

        $etatPaiement->update(['statut' => 'rejete']);

        if (function_exists('logActivite')) {
            logActivite('modification', 'État de paiement rejeté', $etatPaiement);
        }

        return redirect()->route('paiements.index')
            ->with('success', 'État de paiement rejeté.');
    }

    /**
     * Supprimer un état de paiement
     */
    public function destroy(int $id)
    {
        $etatPaiement = EtatPaiement::findOrFail($id);

        if (function_exists('logActivite')) {
            logActivite('suppression', 'Suppression état paiement, statut: ' . $etatPaiement->statut . ', Montant: ' . $etatPaiement->montant_total . ' FCFA', $etatPaiement);
        }

        $etatPaiement->delete();

        if (function_exists('logActivite')) {
            logActivite('suppression', 'État de paiement supprimé');
        }

        return redirect()->route('paiements.index')
            ->with('success', 'État de paiement supprimé avec succès.');
    }

    /**
     * Helper: Service statutaire selon le grade
     */
    private function getServiceStatutaire(?string $grade, ?string $statut): int
    {
        if ($statut === 'Vacataire') {
            return 0;
        }

        // Récupérer le service statutaire depuis les paramètres de calcul de l'année active
        $parametres = ParametreCalcul::anneeActive()->first();

        if ($parametres) {
            return $parametres->service_statutaire;
        }

        // Valeur par défaut si aucun paramètre n'est trouvé
        return 192;
    }
}
