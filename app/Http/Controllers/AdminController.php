<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnneeAcademiqueRequest;
use App\Http\Requests\StoreNiveauComplexiteRequest;
use App\Http\Requests\StoreTauxHoraireRequest;
use App\Http\Requests\StoreUtilisateurRequest;
use App\Http\Requests\UpdateAnneeAcademiqueRequest;
use App\Http\Requests\UpdateNiveauComplexiteRequest;
use App\Http\Requests\UpdateParametreCalculRequest;
use App\Http\Requests\UpdateTauxHoraireRequest;
use App\Http\Requests\UpdateUtilisateurRequest;
use App\Models\AnneeAcademique;
use App\Models\Grade;
use App\Models\NiveauComplexite;
use App\Models\ParametreCalcul;
use App\Models\JournalActivite;
use App\Models\Role;
use App\Models\TauxHoraire;
use App\Models\Utilisateur;
use App\Services\BackupService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // === Utilisateur ===
    public function utilisateurs()
    {
        $utilisateurs = Utilisateur::with(['role', 'createdBy'])->get();
        $roles = Role::where('code', '!=', 'enseignant')->get();

        return view('admin.utilisateurs', [
            'utilisateurs' => $utilisateurs,
            'roles' => $roles,
        ]);
    }

    // === Store utilisateur ===
    public function store(StoreUtilisateurRequest $request)
    {
        // Récupérer uniquement le premier prénom
        $premierPrenom = explode(' ', trim($request->prenom))[0];

        // Générer le login : premier_prenom.nom
        $login = strtolower($premierPrenom . '.' . $request->nom);

        // Remplacer les accents et caractères spéciaux
        $login = Str::slug($login, '.');

        // Vérifier si le login existe déjà et ajouter un suffixe
        $counter = 1;
        $originalLogin = $login;

        while (Utilisateur::where('login', $login)->exists()) {
            $login = $originalLogin . $counter;
            $counter++;
        }

        $utilisateur = Utilisateur::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'login' => $login,
            'mot_de_passe' => Hash::make($request->mot_de_passe),
            'id_role' => $request->id_role,
            'statut_compte' => $request->statut_compte,
            'created_by' => auth()->id(),
        ]);

        if (function_exists('logActivite')) {
            logActivite('création', 'Création de l\'utilisateur ' . $utilisateur->login, $utilisateur);
        }

        return redirect()
            ->route('utilisateurs.index')
            ->with('success', 'Utilisateur créé avec succès. Login : ' . $login);
    }

    // === Update Utulisateur ===
    public function update(UpdateUtilisateurRequest $request, int $id)
    {
        $utilisateur = Utilisateur::findOrFail($id);

        $data = [
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'id_role' => $request->id_role,
            'statut_compte' => $request->statut_compte,
        ];

        // Mettre à jour le mot de passe uniquement si fourni
        if ($request->filled('mot_de_passe')) {
            $data['mot_de_passe'] = Hash::make($request->mot_de_passe);
        }

        $utilisateur->update($data);

        if (function_exists('logActivite')) {
            logActivite('modification', 'Modification de l\'utilisateur ' . $utilisateur->login, $utilisateur);
        }

        return redirect()
            ->route('utilisateurs.index')
            ->with('success', 'Utilisateur modifié avec succès.');
    }

    // === Delete Utilisateur ===
    public function destroy(int $id)
    {
        $utilisateur = Utilisateur::findOrFail($id);
        $utilisateur->delete();

        if (function_exists('logActivite')) {
            logActivite('suppression', 'Suppression de l\'utilisateur ' . $utilisateur->login, $utilisateur);
        }

        return redirect()
            ->route('utilisateurs.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    // === Année académique ===
    public function annees()
    {
        $annees = AnneeAcademique::orderByRaw("CASE WHEN statut = 'en_cours' THEN 0 ELSE 1 END")
            ->orderBy('date_debut', 'desc')
            ->get();
        return view('admin.annees', compact('annees'));
    }

    // === Store année académique ===
    public function storeAnnee(StoreAnneeAcademiqueRequest $request)
    {
        $annee = AnneeAcademique::create([
            'libelle' => $request->libelle,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'statut' => 'a_venir',
        ]);

        if (function_exists('logActivite')) {
            logActivite('création', 'Création de l\'année académique ' . $annee->libelle, $annee);
        }

        return redirect()
            ->route('annees.index')
            ->with('success', 'Année académique créée avec succès.');
    }

    // === Update année académique ===
    public function updateAnnee(UpdateAnneeAcademiqueRequest $request, int $id)
    {
        $annee = AnneeAcademique::findOrFail($id);

        // Si on passe l'année en cours, désactiver toutes les autres
        if ($request->statut === 'en_cours') {
            AnneeAcademique::where('statut', 'en_cours')->where('id', '!=', $id)->update(['statut' => 'cloturee']);
        }

        $annee->update([
            'libelle' => $request->libelle,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'statut' => $request->statut,
        ]);

        if (function_exists('logActivite')) {
            logActivite('modification', 'Modification de l\'année académique ' . $annee->libelle, $annee);
        }

        return redirect()
            ->route('annees.index')
            ->with('success', 'Année académique modifiée avec succès.');
    }

    // === Delete année académique ===
    public function destroyAnnee(int $id)
    {
        $annee = AnneeAcademique::findOrFail($id);

        // Empêcher la suppression de l'année en cours
        if ($annee->statut === 'en_cours') {
            return redirect()
                ->route('annees.index')
                ->with('error', 'Impossible de supprimer l\'année académique en cours.');
        }

        $annee->delete();

        if (function_exists('logActivite')) {
            logActivite('suppression', 'Suppression de l\'année académique ' . $annee->libelle, $annee);
        }

        return redirect()
            ->route('annees.index')
            ->with('success', 'Année académique supprimée avec succès.');
    }

    public function activateAnnee(int $id)
    {
        $annee = AnneeAcademique::findOrFail($id);

        // Désactiver toutes les autres années
        AnneeAcademique::where('statut', 'en_cours')->update(['statut' => 'cloturee']);

        // Activer l'année sélectionnée
        $annee->update(['statut' => 'en_cours']);

        if (function_exists('logActivite')) {
            logActivite('activation', 'Activation de l\'année académique ' . $annee->libelle, $annee);
        }

        return redirect()
            ->route('annees.index')
            ->with('success', 'Année académique activée avec succès.');
    }

    // === Niveau complexite ===
    public function niveauxComplexite()
    {
        $niveaux = NiveauComplexite::withCount('activitesPedagogiques')->get();
        return view('admin.niveaux', compact('niveaux'));
    }

    // === Store niveau complexite ===
    public function storeNiveauComplexite(StoreNiveauComplexiteRequest $request)
    {
        // Vérifier si un niveau existe déjà avec ce libellé
        $existing = NiveauComplexite::withTrashed()
            ->where('libelle', $request->libelle)
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                // Restaurer le niveau soft-deleted
                $existing->restore();
                $existing->update([
                    'coefficient' => $request->coefficient,
                    'description' => $request->description,
                ]);

                if (function_exists('logActivite')) {
                    logActivite('création', "Restauration du niveau de complexité : {$request->libelle}", $existing);
                }

                return redirect()->route('niveaux.index')->with('success', 'Niveau de complexité restauré avec succès.');
            } else {
                return redirect()->back()->with('error', 'Ce libellé existe déjà.');
            }
        }

        // Créer un nouveau niveau de complexité
        $niveau = NiveauComplexite::create([
            'libelle' => $request->libelle,
            'coefficient' => $request->coefficient,
            'description' => $request->description,
        ]);

        if (function_exists('logActivite')) {
            logActivite('création', "Création du niveau de complexité : {$request->libelle}", $niveau);
        }

        return redirect()->route('niveaux.index')->with('success', 'Niveau de complexité créé avec succès.');
    }

    // === Update niveau complexite ===
    public function updateNiveauComplexite(UpdateNiveauComplexiteRequest $request, int $id)
    {
        $niveau = NiveauComplexite::findOrFail($id);

        $niveau->update([
            'libelle' => $request->libelle,
            'coefficient' => $request->coefficient,
            'description' => $request->description,
        ]);

        if (function_exists('logActivite')) {
            logActivite('modification', "Modification du niveau de complexité : {$request->libelle}", $niveau);
        }

        return redirect()->route('niveaux.index')->with('success', 'Niveau de complexité modifié avec succès.');
    }

    // === Destroy niveau complexite ===
    public function destroyNiveauComplexite(int $id)
    {
        $niveau = NiveauComplexite::withCount('activitesPedagogiques')->findOrFail($id);

        // Vérifier s'il y a des activités pédagogiques associées
        if ($niveau->activites_pedagogiques_count > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer ce niveau car il a des activités pédagogiques associées.');
        }

        $niveau->delete();

        if (function_exists('logActivite')) {
            logActivite('suppression', "Suppression du niveau de complexité : {$niveau->libelle}", $niveau);
        }

        return redirect()->route('niveaux.index')->with('success', 'Niveau de complexité supprimé avec succès.');
    }

    // === Paramètres ===
    public function parametres()
    {
        $parametres = ParametreCalcul::anneeActive()->first();

        if (!$parametres) {
            $anneeActive = AnneeAcademique::where('statut', 'en_cours')->first();

            if (!$anneeActive) {
                return redirect()->route('annees.index')
                    ->with('error', 'Aucune année académique active. Veuillez activer une année académique.');
            }

            $parametres = ParametreCalcul::create([
                'annee_id'              => $anneeActive->id,
                'heures_par_credit'     => 10,
                'sequences_par_credit'  => 40,
                'service_statutaire'    => 192,
                'reduction_mise_a_jour' => 50,
            ]);

            if (function_exists('logActivite')) {
                logActivite('création', 'Création automatique des paramètres de calcul pour l\'année ' . $anneeActive->libelle, $parametres);
            }
        }

        return view('admin.parametres', compact('parametres'));
    }

    // === Update paramètres ===
    public function updateParametres(UpdateParametreCalculRequest $request)
    {
        $parametres = ParametreCalcul::anneeActive()->first();

        if (!$parametres) {
            return redirect()->route('annees.index')
                ->with('error', 'Aucune année académique active. Veuillez activer une année académique.');
        }

        $parametres->update($request->validated());

        if (function_exists('logActivite')) {
            logActivite('modification', 'Mise à jour des paramètres de calcul', $parametres);
        }

        return redirect()
            ->route('parametres.index')
            ->with('success', 'Paramètres de calcul mis à jour avec succès.');
    }

    // === Taux horaire ===
    public function taux()
    {
        $taux = TauxHoraire::with(['grade', 'anneeAcademique'])
            ->whereHas('grade')
            ->whereHas('anneeAcademique')
            ->orderBy('date_application', 'desc')
            ->get();

        $grades = Grade::orderBy('libelle', 'asc')->get();
        $annees = AnneeAcademique::orderBy('date_debut', 'desc')->get();

        return view('admin.taux', [
            'taux' => $taux,
            'grades' => $grades,
            'annees' => $annees,
        ]);
    }

    // === Store taux horaire ===
    public function storeTaux(StoreTauxHoraireRequest $request)
    {
        $taux = TauxHoraire::create($request->validated());

        if (function_exists('logActivite')) {
            logActivite('création', 'Création du taux horaire pour ' . $taux->grade->libelle, $taux);
        }

        return redirect()
            ->route('taux.index')
            ->with('success', 'Taux horaire créé avec succès.');
    }

    // === Update taux horaire ===
    public function updateTaux(UpdateTauxHoraireRequest $request, int $id)
    {
        $taux = TauxHoraire::findOrFail($id);
        $taux->update($request->validated());

        if (function_exists('logActivite')) {
            logActivite('modification', 'Modification du taux horaire pour ' . $taux->grade->libelle, $taux);
        }

        return redirect()
            ->route('taux.index')
            ->with('success', 'Taux horaire modifié avec succès.');
    }

    // === Delete taux horaire ===
    public function destroyTaux(int $id)
    {
        $taux = TauxHoraire::findOrFail($id);
        $taux->delete();

        if (function_exists('logActivite')) {
            logActivite('suppression', 'Suppression du taux horaire pour ' . $taux->grade->libelle, $taux);
        }

        return redirect()
            ->route('taux.index')
            ->with('success', 'Taux horaire supprimé avec succès.');
    }

    // === Journal des activités ===
    public function journaux()
    {
        $journaux = JournalActivite::with('utilisateur')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.journaux', compact('journaux'));
    }

    // === Sauvegardes ===
    public function sauvegardes()
    {
        $backupService = new BackupService();
        $backups = $backupService->getBackups();
        $lastBackupDate = $backupService->getLastBackupDate();
        $totalSize = $backupService->getTotalSize();

        // Récupérer les paramètres de sauvegarde
        $parametre = ParametreCalcul::anneeActive()->first();
        $backupSettings = [
            'delai' => $parametre ? $parametre->sauvegarde_auto_delai : 24,
            'rotation' => $parametre ? $parametre->sauvegarde_auto_rotation : 7,
        ];

        return view('admin.sauvegardes', [
            'backups' => $backups,
            'lastBackupDate' => $lastBackupDate,
            'totalSize' => $totalSize,
            'backupSettings' => $backupSettings,
        ]);
    }

    // === Create backup ===
    public function createBackup()
    {
        try {
            $backupService = new BackupService();
            $filename = $backupService->backup();

            return redirect()
                ->route('sauvegardes.index')
                ->with('success', 'Sauvegarde créée avec succès : ' . $filename);
        } catch (\Exception $e) {
            return redirect()
                ->route('sauvegardes.index')
                ->with('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    // === Download backup ===
    public function downloadBackup(string $filename)
    {
        try {
            $backupService = new BackupService();
            $filepath = $backupService->downloadBackup($filename);

            if (!file_exists($filepath)) {
                return redirect()
                    ->route('sauvegardes.index')
                    ->with('error', 'Fichier de sauvegarde introuvable.');
            }

            return response()->download($filepath);
        } catch (\Exception $e) {
            return redirect()
                ->route('sauvegardes.index')
                ->with('error', 'Erreur lors du téléchargement : ' . $e->getMessage());
        }
    }

    // === Restore backup ===
    public function restoreBackup(string $filename)
    {
        try {
            $backupService = new BackupService();
            $backupService->restore($filename);

            return redirect()
                ->route('sauvegardes.index')
                ->with('success', 'Sauvegarde restaurée avec succès.');
        } catch (\Exception $e) {
            return redirect()
                ->route('sauvegardes.index')
                ->with('error', 'Erreur lors de la restauration : ' . $e->getMessage());
        }
    }

    // === Delete backup ===
    public function deleteBackup(string $filename)
    {
        try {
            $backupService = new BackupService();
            $backupService->deleteBackup($filename);

            return redirect()
                ->route('sauvegardes.index')
                ->with('success', 'Sauvegarde supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()
                ->route('sauvegardes.index')
                ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    // === Update backup settings ===
    public function updateBackupSettings(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'sauvegarde_auto_delai' => 'required|integer|min:1|max:168',
            'sauvegarde_auto_rotation' => 'required|integer|min:1|max:30',
        ]);

        try {
            $parametre = ParametreCalcul::anneeActive()->first();

            if (!$parametre) {
                return redirect()
                    ->route('sauvegardes.index')
                    ->with('error', 'Aucune année académique active. Veuillez activer une année académique.');
            }

            $parametre->update([
                'sauvegarde_auto_delai' => $validated['sauvegarde_auto_delai'],
                'sauvegarde_auto_rotation' => $validated['sauvegarde_auto_rotation'],
            ]);

            if (function_exists('logActivite')) {
                logActivite('modification', 'Mise à jour des paramètres de sauvegarde automatique', $parametre);
            }

            return redirect()
                ->route('sauvegardes.index')
                ->with('success', 'Paramètres de sauvegarde mis à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()
                ->route('sauvegardes.index')
                ->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }
}
