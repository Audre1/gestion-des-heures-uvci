<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Models\Enseignant;
use App\Models\EtatPaiement;
use App\Http\Requests\GenerateEtatPaiementRequest;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    public function index(Request $request)
    {
        $anneeId = $request->get('annee_id');
        $statut = $request->get('statut');
        $anneeActive = AnneeAcademique::where('statut', 'active')->first();

        if (!$anneeId && $anneeActive) {
            $anneeId = $anneeActive->id;
        }

        // Récupérer les états de paiement avec filtres
        $query = EtatPaiement::with(['enseignant.utilisateur', 'enseignant.grade', 'anneeAcademique']);

        if ($anneeId) {
            $query->where('id_annee', $anneeId);
        }

        if ($statut) {
            $query->where('statut', $statut);
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
        logActivite('création', 'Génération d\'état de paiement pour enseignant, période: ' . $request->periode);

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

        logActivite('calcul', 'Calcul montant paiement - Enseignant: ' . $enseignant->utilisateur->nom . ' ' . $enseignant->utilisateur->prenom . ', VHT: ' . $vhtTotal . 'h, Montant: ' . $montant . ' FCFA');

        // Générer un numéro de paiement unique (PAY-YYMM-XXX)
        $numeroPaiement = 'PAY-' . now()->format('ym') . '-' . str_pad(EtatPaiement::count() + 1, 3, '0', STR_PAD_LEFT);

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

        logActivite('création', 'État de paiement créé - Numéro: ' . $numeroPaiement . ', Montant: ' . $montant . ' FCFA', $etatPaiement);

        return redirect()->route('paiements.index')
            ->with('success', 'État de paiement généré avec succès.');
    }

    /**
     * Valider un état de paiement
     */
    public function valider(int $id)
    {
        $etatPaiement = EtatPaiement::findOrFail($id);

        logActivite('modification', 'Validation état de paiement, statut: ' . $etatPaiement->statut . ' -> valide', $etatPaiement);

        $etatPaiement->update(['statut' => 'valide']);

        logActivite('modification', 'État de paiement validé - Montant: ' . $etatPaiement->montant_total . ' FCFA', $etatPaiement);

        return redirect()->route('paiements.index')
            ->with('success', 'État de paiement validé avec succès.');
    }

    /**
     * Marquer un état de paiement comme payé
     */
    public function marquerPaye(int $id)
    {
        $etatPaiement = EtatPaiement::findOrFail($id);

        logActivite('modification', 'Marquage comme payé état paiement, Montant: ' . $etatPaiement->montant_total . ' FCFA', $etatPaiement);

        $etatPaiement->update(['statut' => 'paye']);

        logActivite('modification', 'État de paiement marqué payé', $etatPaiement);

        return redirect()->route('paiements.index')
            ->with('success', 'État de paiement marqué comme payé.');
    }

    /**
     * Rejeter un état de paiement
     */
    public function rejeter(int $id)
    {
        $etatPaiement = EtatPaiement::findOrFail($id);

        logActivite('modification', 'Rejet état paiement, statut: ' . $etatPaiement->statut . ' -> rejete', $etatPaiement);

        $etatPaiement->update(['statut' => 'rejete']);

        logActivite('modification', 'État de paiement rejeté', $etatPaiement);

        return redirect()->route('paiements.index')
            ->with('success', 'État de paiement rejeté.');
    }

    /**
     * Supprimer un état de paiement
     */
    public function destroy(int $id)
    {
        $etatPaiement = EtatPaiement::findOrFail($id);

        logActivite('suppression', 'Suppression état paiement, statut: ' . $etatPaiement->statut . ', Montant: ' . $etatPaiement->montant_total . ' FCFA', $etatPaiement);

        $etatPaiement->delete();

        logActivite('suppression', 'État de paiement supprimé');

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

        $services = [
            'Professeur' => 192,
            'Maître de Conférences' => 192,
            'Maître-Assistant' => 192,
            'Assistant' => 192,
            'Chargé de cours' => 192,
        ];

        return $services[$grade] ?? 192;
    }
}
