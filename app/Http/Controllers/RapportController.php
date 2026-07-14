<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Models\Enseignant;
use App\Services\ExportService;
use App\Services\RapportService;
use Illuminate\Http\Request;

class RapportController extends Controller
{
    protected $rapportService;
    protected $exportService;

    public function __construct(RapportService $rapportService, ExportService $exportService)
    {
        $this->rapportService = $rapportService;
        $this->exportService = $exportService;
    }

    public function index()
    {
        if (function_exists('logActivite')) {
            logActivite('consultation', 'Consultation de la page des rapports');
        }

        return view('rapports.index');
    }

    /**
     * Formulaire pour la fiche individuelle enseignant
     */
    public function ficheIndividuelleForm()
    {
        $enseignants = Enseignant::with('utilisateur')->get();
        $annees = AnneeAcademique::all();

        return view('rapports.fiche-individuelle', compact('enseignants', 'annees'));
    }

    /**
     * Génère la fiche individuelle enseignant
     */
    public function ficheIndividuelleGenerate(Request $request)
    {
        $request->validate([
            'enseignant_id' => 'required|exists:enseignants,id',
            'annee_id' => 'nullable|exists:annees_academiques,id',
            'format' => 'required|in:pdf,excel',
        ], [
            'enseignant_id.required' => 'L\'enseignant est requis',
            'enseignant_id.exists' => 'L\'enseignant sélectionné est invalide',
            'annee_id.exists' => 'L\'année académique sélectionnée est invalide',
            'annee_id.nullable' => 'L\'année académique doit être valide si spécifiée',
            'format.required' => 'Le format de sortie est requis',
            'format.in' => 'Le format de sortie doit être pdf ou excel',
        ]);

        $data = $this->rapportService->ficheIndividuelleEnseignant(
            $request->enseignant_id,
            $request->annee_id
        );

        $enseignant = $data['enseignant'];
        $title = "Fiche individuelle - {$enseignant->utilisateur->nom} {$enseignant->utilisateur->prenom}";
        $filename = $this->sanitizeFilename($title) . '-' . date('Y-m-d');

        if ($request->format === 'pdf') {
            $headers = ['Cours', 'Type', 'Niveau', 'Séquences', 'VHT', 'Date', 'Statut'];
            $rows = $data['activites']->map(function ($activite) {
                return [
                    $activite->affectationCours->cours->code_cours,
                    $activite->type_activite === 'creation' ? 'Création' : 'Mise à jour',
                    $activite->niveauComplexite->libelle,
                    $activite->nb_sequences,
                    number_format($activite->volume_horaire, 2) . 'H',
                    $activite->date_activite->format('d/m/Y'),
                    $activite->statut === 'validee' ? 'Validée' : 'En attente',
                ];
            })->toArray();

            return $this->exportService->exportPDF($title, $headers, $rows, $filename . '.pdf');
        } else {
            $headers = ['Cours', 'Type', 'Niveau', 'Séquences', 'VHT', 'Date', 'Statut'];
            $rows = $data['activites']->map(function ($activite) {
                return [
                    $activite->affectationCours->cours->code_cours,
                    $activite->type_activite === 'creation' ? 'Création' : 'Mise à jour',
                    $activite->niveauComplexite->libelle,
                    $activite->nb_sequences,
                    $activite->volume_horaire,
                    $activite->date_activite->format('d/m/Y'),
                    $activite->statut === 'validee' ? 'Validée' : 'En attente',
                ];
            })->toArray();

            return $this->exportService->exportExcel($title, $headers, $rows, $filename . '.xlsx');
        }
    }

    /**
     * Nettoie une chaîne pour l'utiliser comme nom de fichier
     */
    private function sanitizeFilename($filename): string
    {
        // Remplacer les caractères non alphanumériques par des tirets
        $filename = preg_replace('/[^a-zA-Z0-9àâäéèêëïîôùûüÿçÀÂÄÉÈÊËÏÎÔÙÛÜŸÇ\s-]/', '', $filename);
        // Remplacer les espaces multiples par un tiret
        $filename = preg_replace('/\s+/', '-', $filename);
        // Convertir en minuscules
        $filename = strtolower($filename);
        // Supprimer les tirets multiples
        $filename = preg_replace('/-+/', '-', $filename);
        // Supprimer les tirets au début et à la fin
        $filename = trim($filename, '-');

        return $filename;
    }

    /**
     * Formulaire pour l'état global des heures
     */
    public function etatGlobalForm()
    {
        $annees = AnneeAcademique::all();

        return view('rapports.etat-global', compact('annees'));
    }

    /**
     * Génère l'état global des heures
     */
    public function etatGlobalGenerate(Request $request)
    {
        $request->validate([
            'annee_id' => 'nullable|exists:annees_academiques,id',
            'format' => 'required|in:pdf,excel',
        ], [
            'annee_id.nullable' => 'L\'année académique doit être valide si spécifiée',
            'format.required' => 'Le format de sortie est requis',
            'format.in' => 'Le format de sortie doit être pdf ou excel',
        ]);

        $data = $this->rapportService->etatGlobalHeures($request->annee_id);

        $title = "État Global des Heures - {$data['annee']->libelle}";
        $filename = $this->sanitizeFilename($title) . '-' . date('Y-m-d');

        $headers = ['Enseignant', 'Département', 'Grade', 'VHT', 'Service Statutaire', 'Heures Complémentaires', 'Statut'];
        $rows = collect($data['enseignants'])->map(function ($item) {
            return [
                $item['enseignant']->utilisateur->nom . ' ' . $item['enseignant']->utilisateur->prenom,
                $item['enseignant']->departement->nom_departement ?? 'N/A',
                $item['enseignant']->grade->libelle ?? 'N/A',
                number_format($item['vht'], 2) . 'H',
                number_format($item['service_statutaire'], 2) . 'H',
                number_format($item['heures_complementaires'], 2) . 'H',
                $item['statut'],
            ];
        })->toArray();

        if ($request->format === 'pdf') {
            return $this->exportService->exportPDF($title, $headers, $rows, $filename . '.pdf');
        } else {
            return $this->exportService->exportExcel($title, $headers, $rows, $filename . '.xlsx');
        }
    }

    /**
     * Formulaire pour les statistiques pédagogiques
     */
    public function statistiquesForm()
    {
        $annees = AnneeAcademique::all();

        return view('rapports.statistiques', compact('annees'));
    }

    /**
     * Génère les statistiques pédagogiques
     */
    public function statistiquesGenerate(Request $request)
    {
        $request->validate([
            'annee_id' => 'nullable|exists:annees_academiques,id',
            'format' => 'required|in:pdf,excel',
        ], [
            'annee_id.nullable' => 'L\'année académique doit être valide si spécifiée',
            'format.required' => 'Le format de sortie est requis',
            'format.in' => 'Le format de sortie doit être pdf ou excel',
        ]);

        $data = $this->rapportService->statistiquesPedagogiques($request->annee_id);

        $title = "Statistiques Pédagogiques - {$data['annee']->libelle}";
        $filename = $this->sanitizeFilename($title) . '-' . date('Y-m-d');

        $headers = ['Catégorie', 'Détail', 'Nombre'];
        $rows = [];

        // Par type
        foreach ($data['par_type'] as $type => $count) {
            $rows[] = ['Type', $type === 'creation' ? 'Création' : 'Mise à jour', $count];
        }

        // Par niveau
        foreach ($data['par_niveau'] as $niveau => $count) {
            $rows[] = ['Niveau', $niveau, $count];
        }

        // Par département
        foreach ($data['par_departement'] as $dept => $count) {
            $rows[] = ['Département', $dept, $count];
        }

        if ($request->format === 'pdf') {
            return $this->exportService->exportPDF($title, $headers, $rows, $filename . '.pdf');
        } else {
            return $this->exportService->exportExcel($title, $headers, $rows, $filename . '.xlsx');
        }
    }

    /**
     * Formulaire pour l'état des heures complémentaires
     */
    public function heuresComplementairesForm()
    {
        $annees = AnneeAcademique::all();

        return view('rapports.heures-complementaires', compact('annees'));
    }

    /**
     * Génère l'état des heures complémentaires
     */
    public function heuresComplementairesGenerate(Request $request)
    {
        $request->validate([
            'annee_id' => 'nullable|exists:annees_academiques,id',
            'format' => 'required|in:pdf,excel',
        ], [
            'annee_id.nullable' => 'L\'année académique doit être valide si spécifiée',
            'format.required' => 'Le format de sortie est requis',
            'format.in' => 'Le format de sortie doit être pdf ou excel',
        ]);

        $data = $this->rapportService->etatHeuresComplementaires($request->annee_id);

        $title = "Heures Complémentaires - {$data['annee']->libelle}";
        $filename = $this->sanitizeFilename($title) . '-' . date('Y-m-d');

        $headers = ['Enseignant', 'Département', 'Grade', 'VHT Total', 'Service Statutaire', 'Heures Complémentaires', 'Taux Horaire', 'Montant (FCFA)'];

        if ($request->format === 'pdf') {
            $rows = collect($data['enseignants'])->map(function ($item) {
                return [
                    $item['enseignant']->utilisateur->nom . ' ' . $item['enseignant']->utilisateur->prenom,
                    $item['enseignant']->departement->nom_departement ?? 'N/A',
                    $item['enseignant']->grade->libelle ?? 'N/A',
                    number_format($item['vht'], 2) . 'H',
                    number_format($item['service_statutaire'], 2) . 'H',
                    number_format($item['heures_complementaires'], 2) . 'H',
                    number_format($item['taux_horaire'], 0),
                    number_format($item['montant'], 0, ',', '.'),
                ];
            })->toArray();

            return $this->exportService->exportPDF($title, $headers, $rows, $filename . '.pdf');
        } else {
            $rows = collect($data['enseignants'])->map(function ($item) {
                return [
                    $item['enseignant']->utilisateur->nom . ' ' . $item['enseignant']->utilisateur->prenom,
                    $item['enseignant']->departement->nom_departement ?? 'N/A',
                    $item['enseignant']->grade->libelle ?? 'N/A',
                    $item['vht'],
                    $item['service_statutaire'],
                    $item['heures_complementaires'],
                    $item['taux_horaire'],
                    $item['montant'],
                ];
            })->toArray();

            return $this->exportService->exportExcel($title, $headers, $rows, $filename . '.xlsx');
        }
    }

    /**
     * Formulaire pour l'état de paiement collectif
     */
    public function paiementCollectifForm()
    {
        $annees = AnneeAcademique::all();

        return view('rapports.paiement-collectif', compact('annees'));
    }

    /**
     * Génère l'état de paiement collectif
     */
    public function paiementCollectifGenerate(Request $request)
    {
        $request->validate([
            'annee_id' => 'nullable|exists:annees_academiques,id',
            'periode' => 'nullable|string',
            'format' => 'required|in:pdf,excel',
        ], [
            'annee_id.nullable' => 'L\'année académique doit être valide si spécifiée',
            'periode.string' => 'La période doit être une chaîne de caractères',
            'format.required' => 'Le format de sortie est requis',
            'format.in' => 'Le format de sortie doit être pdf ou excel',
        ]);

        $data = $this->rapportService->etatPaiementCollectif($request->annee_id, $request->periode);

        $title = "État de Paiement Collectif - {$data['annee']->libelle}";
        $filename = $this->sanitizeFilename($title) . '-' . date('Y-m-d');

        $headers = ['Numéro Paiement', 'Enseignant', 'Période', 'Montant Total (FCFA)', 'Date Génération', 'Statut'];

        if ($request->format === 'pdf') {
            $rows = $data['etats']->map(function ($etat) {
                return [
                    $etat->numero_paiement,
                    $etat->enseignant->utilisateur->nom . ' ' . $etat->enseignant->utilisateur->prenom,
                    $etat->periode,
                    number_format($etat->montant_total, 0, ',', '.'),
                    $etat->date_generation->format('d/m/Y'),
                    ucfirst(str_replace('_', ' ', $etat->statut))
                ];
            })->toArray();

            return $this->exportService->exportPDF($title, $headers, $rows, $filename . '.pdf');
        } else {
            $rows = $data['etats']->map(function ($etat) {
                return [
                    $etat->numero_paiement,
                    $etat->enseignant->utilisateur->nom . ' ' . $etat->enseignant->utilisateur->prenom,
                    $etat->periode,
                    $etat->montant_total,
                    $etat->date_generation->format('d/m/Y'),
                    ucfirst(str_replace('_', ' ', $etat->statut))
                ];
            })->toArray();

            return $this->exportService->exportExcel($title, $headers, $rows, $filename . '.xlsx');
        }
    }

    /**
     * Formulaire pour la charge par département
     */
    public function chargeDepartementForm()
    {
        $annees = AnneeAcademique::all();

        return view('rapports.charge-departement', compact('annees'));
    }

    /**
     * Génère la charge par département
     */
    public function chargeDepartementGenerate(Request $request)
    {
        $request->validate([
            'annee_id' => 'nullable|exists:annees_academiques,id',
            'format' => 'required|in:pdf,excel',
        ], [
            'annee_id.nullable' => 'L\'année académique doit être valide si spécifiée',
            'format.required' => 'Le format de sortie est requis',
            'format.in' => 'Le format de sortie doit être pdf ou excel',
        ]);

        $data = $this->rapportService->chargeParDepartement($request->annee_id);

        $title = "Charge par Département - {$data['annee']->libelle}";
        $filename = $this->sanitizeFilename($title) . '-' . date('Y-m-d');

        $headers = ['Département', 'Nombre Enseignants', 'VHT Total'];
        $rows = collect($data['departements'])->map(function ($item) {
            return [
                $item['departement']->nom_departement,
                $item['nb_enseignants'],
                number_format($item['vht'], 2) . 'H',
            ];
        })->toArray();

        if ($request->format === 'pdf') {
            return $this->exportService->exportPDF($title, $headers, $rows, $filename . '.pdf');
        } else {
            return $this->exportService->exportExcel($title, $headers, $rows, $filename . '.xlsx');
        }
    }
}
