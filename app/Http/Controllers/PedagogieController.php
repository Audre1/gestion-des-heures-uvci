<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAffectationRequest;
use App\Http\Requests\StoreCoursRequest;
use App\Http\Requests\StoreDepartementRequest;
use App\Http\Requests\StoreEnseignantRequest;
use App\Http\Requests\StoreFiliereRequest;
use App\Http\Requests\StoreGradeRequest;
use App\Http\Requests\StoreRessourceRequest;
use App\Http\Requests\StoreSequenceRequest;
use App\Http\Requests\StoreTypeRessourceRequest;
use App\Http\Requests\UpdateAffectationRequest;
use App\Http\Requests\UpdateCoursRequest;
use App\Http\Requests\UpdateDepartementRequest;
use App\Http\Requests\UpdateEnseignantRequest;
use App\Http\Requests\UpdateFiliereRequest;
use App\Http\Requests\UpdateGradeRequest;
use App\Http\Requests\UpdateRessourceRequest;
use App\Http\Requests\UpdateSequenceRequest;
use App\Http\Requests\UpdateTypeRessourceRequest;
use App\Models\AffectationCours;
use App\Models\AnneeAcademique;
use App\Models\Cours;
use App\Models\Departement;
use App\Models\Enseignant;
use App\Models\Filiere;
use App\Models\Grade;
use App\Models\Role;
use App\Models\RessourcePedagogique;
use App\Models\SequencePedagogique;
use App\Models\TypeRessource;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PedagogieController extends Controller
{
    // === ENSEIGNANTS ===
    public function enseignants()
    {
        $enseignants = Enseignant::with(['utilisateur', 'utilisateur.createdBy', 'grade', 'departement'])
            ->withCount('affectationsCours', 'etatsPaiement')
            ->get();

        return view('pedagogie.enseignants', compact('enseignants'));
    }

    // === STORE ENSEIGNANT ===
    public function storeEnseignant(StoreEnseignantRequest $request)
    {
        // Vérifier si un enseignant avec ce matricule existe en soft-delete
        $existingEnseignant = Enseignant::withTrashed()->where('matricule', $request->matricule)->first();

        if ($existingEnseignant) {
            // Restaurer l'enseignant soft-deleted
            DB::transaction(function () use ($request, $existingEnseignant) {
                // Restaurer l'utilisateur
                $existingEnseignant->utilisateur->restore();

                // Mettre à jour l'utilisateur
                $existingEnseignant->utilisateur->update([
                    'nom' => $request->nom,
                    'prenom' => $request->prenom,
                    'email' => $request->email,
                    'telephone' => $request->telephone,
                    'mot_de_passe' => $request->mot_de_passe ? Hash::make($request->mot_de_passe) : $existingEnseignant->utilisateur->mot_de_passe,
                ]);

                // Mettre à jour l'enseignant
                $existingEnseignant->update([
                    'statut' => $request->statut,
                    'taux_horaire_perso' => $request->taux_horaire_perso,
                    'date_recrutement' => $request->date_recrutement,
                    'id_grade' => $request->id_grade,
                    'id_departement' => $request->id_departement,
                ]);

                $existingEnseignant->restore();

                if (function_exists('logActivite')) {
                    logActivite('création', 'Restauration de l\'enseignant ' . $request->nom . ' ' . $request->prenom, $existingEnseignant);
                }
            });

            return redirect()->route('enseignants.index')->with('success', 'Enseignant restauré avec succès.');
        }

        DB::transaction(function () use ($request) {
            // Récupérer le rôle enseignant
            $enseignantRole = Role::where('code', 'enseignant')->first();

            // Récupérer uniquement le premier prénom
            $premierPrenom = explode(' ', trim($request->prenom))[0];

            // Générer le login : premier_prenom.nom
            $login = strtolower($premierPrenom . '.' . $request->nom);

            // Remplacer les accents et caractères spéciaux
            $login = Str::slug($login, '.');

            // Vérifier si le login existe déjà et ajouter un suffixe si nécessaire
            $counter = 1;
            $originalLogin = $login;
            while (Utilisateur::where('login', $login)->exists()) {
                $login = $originalLogin . $counter;
                $counter++;
            }

            // Créer Utilisateur avec rôle enseignant
            $user = Utilisateur::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'login' => $login,
                'mot_de_passe' => Hash::make($request->mot_de_passe),
                'id_role' => $enseignantRole->id,
                'statut_compte' => 'actif',
                'created_by' => Auth::user()->id,
            ]);

            // Créer Enseignant
            $enseignant = Enseignant::create([
                'matricule' => $request->matricule,
                'statut' => $request->statut,
                'taux_horaire_perso' => $request->taux_horaire_perso,
                'date_recrutement' => $request->date_recrutement,
                'id_grade' => $request->id_grade,
                'id_departement' => $request->id_departement,
                'id_utilisateur' => $user->id,
            ]);

            // Logger la création de l'enseignant
            if (function_exists('logActivite')) {
                logActivite('création', 'Création de l\'enseignant ' . $request->nom . ' ' . $request->prenom, $enseignant);
            }
        });

        return redirect()->route('enseignants.index')->with('success', 'Enseignant créé avec succès.');
    }

    // === UPDATE ENSEIGNANT ===
    public function updateEnseignant(UpdateEnseignantRequest $request, int $id)
    {
        $enseignant = Enseignant::with('utilisateur')->findOrFail($id);

        DB::transaction(function () use ($request, $enseignant) {
            // Mettre à jour l'utilisateur
            $enseignant->utilisateur->update([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
            ]);

            // Mettre à jour le mot de passe si fourni
            if ($request->filled('mot_de_passe')) {
                $enseignant->utilisateur->update([
                    'mot_de_passe' => Hash::make($request->mot_de_passe),
                ]);
            }

            // Mettre à jour l'enseignant
            $enseignant->update([
                'matricule' => $request->matricule,
                'statut' => $request->statut,
                'taux_horaire_perso' => $request->taux_horaire_perso,
                'date_recrutement' => $request->date_recrutement,
                'id_grade' => $request->id_grade,
                'id_departement' => $request->id_departement,
            ]);

            // Logger la modification de l'enseignant
            if (function_exists('logActivite')) {
                logActivite('modification', 'Modification de l\'enseignant ' . $request->nom . ' ' . $request->prenom, $enseignant);
            }
        });

        return redirect()->route('enseignants.index')->with('success', 'Enseignant modifié avec succès.');
    }

    // === DELETE ENSEIGNANT ===
    public function destroyEnseignant(int $id)
    {
        $enseignant = Enseignant::withCount('affectationsCours', 'etatsPaiement')->with('utilisateur')->findOrFail($id);

        // Vérifier s'il y a des affectations ou des états de paiement associés
        if ($enseignant->affectations_cours_count > 0 || $enseignant->etats_paiement_count > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer cet enseignant car il a des affectations ou des paiements associés.');
        }

        $nom = $enseignant->utilisateur->nom . ' ' . $enseignant->utilisateur->prenom;

        DB::transaction(function () use ($enseignant) {
            // Logger la suppression avant de supprimer
            if (function_exists('logActivite')) {
                logActivite('suppression', 'Suppression de l\'enseignant ' . $enseignant->utilisateur->nom . ' ' . $enseignant->utilisateur->prenom, $enseignant);
            }

            // Soft-delete l'enseignant (cascade supprimera l'utilisateur si configuré)
            $enseignant->delete();
        });

        return redirect()->route('enseignants.index')->with('success', 'Enseignant supprimé avec succès.');
    }

    // === GRADES ===
    public function grades()
    {
        $grades = Grade::withCount('enseignants')->get();

        return view('pedagogie.grades', compact('grades'));
    }

    // === STORE GRADE ===
    public function storeGrade(StoreGradeRequest $request)
    {
        // Vérifier si un grade avec ce libellé existe en soft-delete
        $existingGrade = Grade::withTrashed()->where('libelle', $request->libelle)->first();

        if ($existingGrade) {
            // Restaurer le grade soft-deleted
            $existingGrade->restore();

            if (function_exists('logActivite')) {
                logActivite('création', 'Restauration du grade ' . $request->libelle, $existingGrade);
            }

            return redirect()->route('grades.index')->with('success', 'Grade restauré avec succès.');
        }

        // Créer un nouveau grade
        $grade = Grade::create([
            'libelle' => $request->libelle,
        ]);

        if (function_exists('logActivite')) {
            logActivite('création', 'Création du grade ' . $request->libelle, $grade);
        }

        return redirect()->route('grades.index')->with('success', 'Grade créé avec succès.');
    }

    // === UPDATE GRADE ===
    public function updateGrade(UpdateGradeRequest $request, int $id)
    {
        $grade = Grade::findOrFail($id);

        $grade->update([
            'libelle' => $request->libelle,
        ]);

        if (function_exists('logActivite')) {
            logActivite('modification', 'Modification du grade ' . $request->libelle, $grade);
        }

        return redirect()->route('grades.index')->with('success', 'Grade modifié avec succès.');
    }

    // === DELETE GRADE ===
    public function destroyGrade(int $id)
    {
        $grade = Grade::withCount('enseignants')->findOrFail($id);

        // Contrainte : ne pas supprimer si des enseignants sont associés
        if ($grade->enseignants_count > 0) {
            return redirect()->route('grades.index')->with('error', 'Impossible de supprimer ce grade car ' . $grade->enseignants_count . ' enseignant(s) y sont associé(s).');
        }

        $libelle = $grade->libelle;

        if (function_exists('logActivite')) {
            logActivite('suppression', 'Suppression du grade ' . $libelle, $grade);
        }

        $grade->delete();

        return redirect()->route('grades.index')->with('success', 'Grade supprimé avec succès.');
    }

    // === DEPARTEMENTS ===
    public function departements()
    {
        $departements = Departement::withCount('enseignants', 'filieres')->get();

        return view('pedagogie.departements', compact('departements'));
    }

    // === STORE DEPARTEMENT ===
    public function storeDepartement(StoreDepartementRequest $request)
    {
        // Vérifier si un département avec ce code existe en soft-delete
        $existingDepartement = Departement::withTrashed()->where('code_departement', $request->code_departement)->first();

        if ($existingDepartement) {
            // Restaurer le département soft/deleted
            $existingDepartement->update([
                'nom_departement' => $request->nom_departement,
            ]);
            $existingDepartement->restore();

            if (function_exists('logActivite')) {
                logActivite('création', 'Restauration du département ' . $request->code_departement, $existingDepartement);
            }

            return redirect()->route('departements.index')->with('success', 'Département restauré avec succès.');
        }

        // Créer un nouveau département
        $departement = Departement::create([
            'code_departement' => $request->code_departement,
            'nom_departement' => $request->nom_departement,
        ]);

        if (function_exists('logActivite')) {
            logActivite('création', 'Création du département ' . $request->code_departement . ' - ' . $request->nom_departement, $departement);
        }

        return redirect()->route('departements.index')->with('success', 'Département créé avec succès.');
    }

    // === UPDATE DEPARTEMENT ===
    public function updateDepartement(UpdateDepartementRequest $request, int $id)
    {
        $departement = Departement::findOrFail($id);

        $departement->update([
            'code_departement' => $request->code_departement,
            'nom_departement' => $request->nom_departement,
        ]);

        if (function_exists('logActivite')) {
            logActivite('modification', 'Modification du département ' . $request->code_departement . ' - ' . $request->nom_departement, $departement);
        }

        return redirect()->route('departements.index')->with('success', 'Département modifié avec succès.');
    }

    // === DELETE DEPARTEMENT ===
    public function destroyDepartement(int $id)
    {
        $departement = Departement::withCount('enseignants', 'filieres')->findOrFail($id);

        // Vérifier s'il y a des enseignants ou des filières associés
        if ($departement->enseignants_count > 0 || $departement->filieres_count > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer ce département car il a des enseignants ou des filières associés.');
        }

        $departement->delete();

        if (function_exists('logActivite')) {
            logActivite('suppression', 'Suppression du département ' . $departement->code_departement . ' - ' . $departement->nom_departement, $departement);
        }

        return redirect()->route('departements.index')->with('success', 'Département supprimé avec succès.');
    }

    // === FILIERES ===
    public function filieres()
    {
        $filieres = Filiere::with('departement')->withCount('cours')->get();

        return view('pedagogie.filieres', compact('filieres'));
    }

    // === STORE FILIERE ===
    public function storeFiliere(StoreFiliereRequest $request)
    {
        // Vérifier si une filière avec ce code existe en soft-delete
        $existingFiliere = Filiere::withTrashed()->where('code_filiere', $request->code_filiere)->first();

        if ($existingFiliere) {
            // Restaurer la filière soft-deleted
            $existingFiliere->update([
                'nom_filiere' => $request->nom_filiere,
                'id_departement' => $request->id_departement,
            ]);
            $existingFiliere->restore();

            if (function_exists('logActivite')) {
                logActivite('création', 'Restauration de la filière ' . $request->code_filiere, $existingFiliere);
            }

            return redirect()->route('filieres.index')->with('success', 'Filière restaurée avec succès.');
        }

        // Créer une nouvelle filière
        $filiere = Filiere::create([
            'code_filiere' => $request->code_filiere,
            'nom_filiere' => $request->nom_filiere,
            'id_departement' => $request->id_departement,
        ]);

        if (function_exists('logActivite')) {
            logActivite('création', 'Création de la filière ' . $request->code_filiere . ' - ' . $request->nom_filiere, $filiere);
        }

        return redirect()->route('filieres.index')->with('success', 'Filière créée avec succès.');
    }

    // === UPDATE FILIERE ===
    public function updateFiliere(UpdateFiliereRequest $request, int $id)
    {
        $filiere = Filiere::findOrFail($id);

        $filiere->update([
            'code_filiere' => $request->code_filiere,
            'nom_filiere' => $request->nom_filiere,
            'id_departement' => $request->id_departement,
        ]);

        if (function_exists('logActivite')) {
            logActivite('modification', 'Modification de la filière ' . $request->code_filiere . ' - ' . $request->nom_filiere, $filiere);
        }

        return redirect()->route('filieres.index')->with('success', 'Filière modifiée avec succès.');
    }

    // === DELETE FILIERE ===
    public function destroyFiliere(int $id)
    {
        $filiere = Filiere::withCount('cours')->findOrFail($id);

        // Vérifier s'il y a des cours associés
        if ($filiere->cours_count > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer cette filière car elle a des cours associés.');
        }

        $filiere->delete();

        if (function_exists('logActivite')) {
            logActivite('suppression', 'Suppression de la filière ' . $filiere->code_filiere . ' - ' . $filiere->nom_filiere, $filiere);
        }

        return redirect()->route('filieres.index')->with('success', 'Filière supprimée avec succès.');
    }

    // === ATTACH COURS TO FILIERE ===
    public function attachCoursToFiliere(Request $request, int $filiereId)
    {
        $request->validate([
            'id_cours' => 'required|exists:cours,id',
            'semestre' => 'required|in:S1,S2,S3,S4,S5,S6',
            'niveau' => 'required|in:L1,L2,L3,M1,M2',
        ], [
            'id_cours.required' => 'Le cours est requis.',
            'semestre.required' => 'Le semestre est requis.',
            'niveau.required' => 'Le niveau est requis.',
        ]);

        $filiere = Filiere::findOrFail($filiereId);

        // Vérifier si l'association existe déjà
        $exists = $filiere->cours()->wherePivot('id_cours', $request->id_cours)
            ->wherePivot('semestre', $request->semestre)
            ->wherePivot('niveau', $request->niveau)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Cette association existe déjà pour cette filière.');
        }

        $filiere->cours()->attach($request->id_cours, [
            'semestre' => $request->semestre,
            'niveau' => $request->niveau,
        ]);

        if (function_exists('logActivite')) {
            $cours = Cours::find($request->id_cours);
            logActivite('création', "Association du cours {$cours->code_cours} à la filière {$filiere->nom_filiere} ({$request->semestre} - {$request->niveau})", $filiere);
        }

        return redirect()->back()->with('success', 'Cours associé à la filière avec succès.');
    }

    // === DETACH COURS FROM FILIERE ===
    public function detachCoursFromFiliere(int $filiereId, int $coursId, string $semestre, string $niveau)
    {
        $filiere = Filiere::findOrFail($filiereId);

        $filiere->cours()->wherePivot('id_cours', $coursId)
            ->wherePivot('semestre', $semestre)
            ->wherePivot('niveau', $niveau)
            ->detach();

        if (function_exists('logActivite')) {
            $cours = Cours::find($coursId);
            logActivite('suppression', "Dissociation du cours {$cours->code_cours} de la filière {$filiere->nom_filiere} ({$semestre} - {$niveau})", $filiere);
        }

        return redirect()->back()->with('success', 'Cours dissocié de la filière avec succès.');
    }

    // === COURS ===
    public function cours()
    {
        $cours = Cours::withCount('sequencesPedagogiques', 'affectationsCours')->get();

        return view('pedagogie.cours', compact('cours'));
    }

    // === STORE COURS ===
    public function storeCours(StoreCoursRequest $request)
    {
        // Vérifier si un cours avec ce code existe en soft-delete
        $existingCours = Cours::withTrashed()->where('code_cours', $request->code_cours)->first();

        if ($existingCours) {
            // Restaurer le cours soft-deleted
            $existingCours->update([
                'intitule' => $request->intitule,
                'nombre_heures' => $request->nombre_heures,
                'nombre_credits' => $request->nombre_credits,
            ]);
            $existingCours->restore();

            if (function_exists('logActivite')) {
                logActivite('création', 'Restauration du cours ' . $request->code_cours, $existingCours);
            }

            return redirect()->route('cours.index')->with('success', 'Cours restauré avec succès.');
        }

        // Créer un nouveau cours
        $cours = Cours::create([
            'code_cours' => $request->code_cours,
            'intitule' => $request->intitule,
            'nombre_heures' => $request->nombre_heures,
            'nombre_credits' => $request->nombre_credits,
        ]);

        if (function_exists('logActivite')) {
            logActivite('création', 'Création du cours ' . $request->code_cours . ' - ' . $request->intitule, $cours);
        }

        return redirect()->route('cours.index')->with('success', 'Cours créé avec succès.');
    }

    // === UPDATE COURS ===
    public function updateCours(UpdateCoursRequest $request, int $id)
    {
        $cours = Cours::findOrFail($id);

        $cours->update([
            'code_cours' => $request->code_cours,
            'intitule' => $request->intitule,
            'nombre_heures' => $request->nombre_heures,
            'nombre_credits' => $request->nombre_credits,
        ]);

        if (function_exists('logActivite')) {
            logActivite('modification', 'Modification du cours ' . $request->code_cours . ' - ' . $request->intitule, $cours);
        }

        return redirect()->route('cours.index')->with('success', 'Cours modifié avec succès.');
    }

    // === DELETE COURS ===
    public function destroyCours(int $id)
    {
        $cours = Cours::withCount('sequencesPedagogiques', 'affectationsCours')->findOrFail($id);

        // Contrainte : ne pas supprimer si des séquences ou affectations sont associées
        if ($cours->sequences_pedagogiques_count > 0 || $cours->affectations_cours_count > 0) {
            $message = 'Impossible de supprimer ce cours car ';
            if ($cours->sequences_pedagogiques_count > 0) {
                $message .= $cours->sequences_pedagogiques_count . ' séquence(s) pédagogique(s) ';
            }
            if ($cours->affectations_cours_count > 0) {
                $message .= ($cours->sequences_pedagogiques_count > 0 ? 'et ' : '') . $cours->affectations_cours_count . ' affectation(s) ';
            }
            $message .= 'y sont associé(s).';

            return redirect()->route('cours.index')->with('error', $message);
        }

        $code = $cours->code_cours;
        $intitule = $cours->intitule;

        if (function_exists('logActivite')) {
            logActivite('suppression', 'Suppression du cours ' . $code . ' - ' . $intitule, $cours);
        }

        $cours->delete();

        return redirect()->route('cours.index')->with('success', 'Cours supprimé avec succès.');
    }

    // === AFFECTATIONS ===
    public function affectations()
    {
        $affectations = AffectationCours::with(['enseignant.utilisateur', 'cours', 'anneeAcademique'])
            ->withCount('activitesPedagogiques')
            ->get();

        $currentYear = AnneeAcademique::where('statut', 'en_cours')
            ->first();

        return view('pedagogie.affectations', [
            'affectations' => $affectations,
            'currentYear' => $currentYear,
        ]);
    }

    // === STORE AFFECTATION ===
    public function storeAffectation(StoreAffectationRequest $request)
    {
        // Vérifier si une affectation existe déjà pour ce triplet
        $existing = AffectationCours::withTrashed()
            ->where('id_enseignant', $request->id_enseignant)
            ->where('id_cours', $request->id_cours)
            ->where('id_annee', $request->id_annee)
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                // Restaurer l'affectation soft-deleted
                $existing->update([
                    'date_affectation' => $request->date_affectation,
                ]);
                $existing->restore();

                $enseignant = Enseignant::with('utilisateur')->find($request->id_enseignant);
                $cours = Cours::find($request->id_cours);
                $annee = AnneeAcademique::find($request->id_annee);

                if (function_exists('logActivite')) {
                    logActivite('création', "Restauration de l'affectation : {$enseignant->utilisateur->nom} {$enseignant->utilisateur->prenom} - {$cours->code_cours} ({$annee->libelle})", $existing);
                }

                return redirect()->route('affectations.index')->with('success', 'Affectation restaurée avec succès.');
            } else {
                return redirect()->back()->with('error', 'Cette affectation existe déjà pour cet enseignant, ce cours et cette année académique.');
            }
        }

        // Créer une nouvelle affectation
        $affectation = AffectationCours::create([
            'id_enseignant' => $request->id_enseignant,
            'id_cours' => $request->id_cours,
            'id_annee' => $request->id_annee,
            'date_affectation' => $request->date_affectation,
        ]);

        $enseignant = Enseignant::with('utilisateur')->find($request->id_enseignant);
        $cours = Cours::find($request->id_cours);
        $annee = AnneeAcademique::find($request->id_annee);

        if (function_exists('logActivite')) {
            logActivite('création', "Création de l'affectation : {$enseignant->utilisateur->nom} {$enseignant->utilisateur->prenom} - {$cours->code_cours} ({$annee->libelle})", $affectation);
        }

        return redirect()->route('affectations.index')->with('success', 'Affectation créée avec succès.');
    }

    // === UPDATE AFFECTATION ===
    public function updateAffectation(UpdateAffectationRequest $request, int $id)
    {
        $affectation = AffectationCours::with(['enseignant.utilisateur', 'cours', 'anneeAcademique'])->findOrFail($id);

        // Vérifier si une autre affectation existe avec les mêmes données
        $existing = AffectationCours::where('id_enseignant', $request->id_enseignant)
            ->where('id_cours', $request->id_cours)
            ->where('id_annee', $request->id_annee)
            ->where('id', '!=', $id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Cette affectation existe déjà pour cet enseignant, ce cours et cette année académique.');
        }

        $enseignant = Enseignant::with('utilisateur')->find($request->id_enseignant);
        $cours = Cours::find($request->id_cours);
        $annee = AnneeAcademique::find($request->id_annee);

        $affectation->update([
            'id_enseignant' => $request->id_enseignant,
            'id_cours' => $request->id_cours,
            'id_annee' => $request->id_annee,
            'date_affectation' => $request->date_affectation,
        ]);

        if (function_exists('logActivite')) {
            logActivite('modification', "Modification de l'affectation : {$enseignant->utilisateur->nom} {$enseignant->utilisateur->prenom} - {$cours->code_cours} ({$annee->libelle})", $affectation);
        }

        return redirect()->route('affectations.index')->with('success', 'Affectation modifiée avec succès.');
    }

    // === DELETE AFFECTATION ===
    public function destroyAffectation(int $id)
    {
        $affectation = AffectationCours::with(['enseignant.utilisateur', 'cours', 'anneeAcademique'])->withCount('activitesPedagogiques')->findOrFail($id);

        // Vérifier s'il y a des activités pédagogiques associées
        if ($affectation->activites_pedagogiques_count > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer cette affectation car elle a des activités pédagogiques associées.');
        }

        $enseignant = $affectation->enseignant;
        $cours = $affectation->cours;
        $annee = $affectation->anneeAcademique;

        $affectation->delete();

        if (function_exists('logActivite')) {
            logActivite('suppression', "Suppression de l'affectation : {$enseignant->utilisateur->nom} {$enseignant->utilisateur->prenom} - {$cours->code_cours} ({$annee->libelle})", $affectation);
        }

        return redirect()->route('affectations.index')->with('success', 'Affectation supprimée avec succès.');
    }

    // === SEQUENCES ===
    public function sequences()
    {
        $sequences = SequencePedagogique::with('cours')->withCount('ressourcesPedagogiques')
            ->orderBy('id_cours')
            ->orderBy('numero_ordre')
            ->get()
            ->groupBy('id_cours');

        $cours = Cours::all()->keyBy('id');

        return view('pedagogie.sequences', compact('sequences', 'cours'));
    }

    // === STORE SEQUENCE ===
    public function storeSequence(StoreSequenceRequest $request)
    {
        // Vérifier si une séquence existe déjà avec le même titre et le même cours
        $existing = SequencePedagogique::withTrashed()
            ->where('titre', $request->titre)
            ->where('id_cours', $request->id_cours)
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                // Restaurer la séquence soft-deleted
                $existing->restore();

                $cours = Cours::find($request->id_cours);

                if (function_exists('logActivite')) {
                    logActivite('création', "Restauration de la séquence n°{$existing->numero_ordre} : {$request->titre} ({$cours->code_cours})", $existing);
                }

                return redirect()->route('sequences.index')->with('success', 'Séquence restaurée avec succès.');
            } else {
                return redirect()->back()->with('error', 'Ce titre existe déjà pour ce cours.');
            }
        }

        // Calculer le numéro d'ordre automatiquement (max + 1 pour ce cours)
        $maxOrdre = SequencePedagogique::where('id_cours', $request->id_cours)
            ->max('numero_ordre') ?? 0;
        $numero_ordre = $maxOrdre + 1;

        // Créer une nouvelle séquence
        $sequence = SequencePedagogique::create([
            'titre' => $request->titre,
            'numero_ordre' => $numero_ordre,
            'id_cours' => $request->id_cours,
        ]);

        $cours = Cours::find($request->id_cours);

        if (function_exists('logActivite')) {
            logActivite('création', "Création de la séquence n°{$numero_ordre} : {$request->titre} ({$cours->code_cours})", $sequence);
        }

        return redirect()->route('sequences.index')->with('success', 'Séquence créée avec succès.');
    }

    // === UPDATE SEQUENCE ===
    public function updateSequence(UpdateSequenceRequest $request, int $id)
    {
        $sequence = SequencePedagogique::with('cours')->findOrFail($id);

        $sequence->update([
            'titre' => $request->titre,
            'id_cours' => $request->id_cours,
        ]);

        $cours = Cours::find($request->id_cours);

        if (function_exists('logActivite')) {
            logActivite('modification', "Modification de la séquence n°{$sequence->numero_ordre} : {$request->titre} ({$cours->code_cours})", $sequence);
        }

        return redirect()->route('sequences.index')->with('success', 'Séquence modifiée avec succès.');
    }

    // === DESTROY SEQUENCE ===
    public function destroySequence(int $id)
    {
        $sequence = SequencePedagogique::with('cours')->withCount('ressourcesPedagogiques')->findOrFail($id);

        // Vérifier s'il y a des ressources pédagogiques associées
        if ($sequence->ressources_pedagogiques_count > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer cette séquence car elle a des ressources pédagogiques associées.');
        }

        $cours = $sequence->cours;

        $sequence->delete();

        if (function_exists('logActivite')) {
            logActivite('suppression', "Suppression de la séquence n°{$sequence->numero_ordre} : {$sequence->titre} ({$cours->code_cours})", $sequence);
        }

        return redirect()->route('sequences.index')->with('success', 'Séquence supprimée avec succès.');
    }

    // === REORDER SEQUENCES ===
    public function reorderSequences(Request $request)
    {
        $sequenceIds = $request->input('sequence_ids', []);

        if (empty($sequenceIds)) {
            return response()->json(['success' => false, 'message' => 'Aucune séquence à réordonner.'], 400);
        }

        DB::beginTransaction();
        try {
            foreach ($sequenceIds as $index => $sequenceId) {
                $sequence = SequencePedagogique::find($sequenceId);
                if ($sequence) {
                    $sequence->update(['numero_ordre' => $index + 1]);
                }
            }
            DB::commit();

            if (function_exists('logActivite')) {
                logActivite('modification', 'Réordonnancement des séquences pédagogiques', null);
            }

            return response()->json(['success' => true, 'message' => 'Séquences réordonnées avec succès.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erreur lors du réordonnancement.'], 500);
        }
    }

    // === RESSOURCES ===
    public function ressources()
    {
        $ressources = RessourcePedagogique::with(['sequence.cours', 'typeRessource'])
            ->withCount('activitesPedagogiques')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('pedagogie.ressources', compact('ressources'));
    }

    // === STORE RESSOURCE ===
    public function storeRessource(StoreRessourceRequest $request)
    {
        // Vérifier si une ressource existe déjà avec le même titre, séquence et type
        $existing = RessourcePedagogique::withTrashed()
            ->where('titre', $request->titre)
            ->where('id_sequence', $request->id_sequence)
            ->where('id_type', $request->id_type)
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                // Restaurer la ressource soft-deleted
                $existing->restore();

                $sequence = SequencePedagogique::with('cours')->find($request->id_sequence);
                $type = TypeRessource::find($request->id_type);

                if (function_exists('logActivite')) {
                    logActivite('création', "Restauration de la ressource : {$request->titre} ({$type->libelle}) - Séq. {$sequence->numero_ordre} ({$sequence->cours->code_cours})", $existing);
                }

                return redirect()->route('ressources.index')->with('success', 'Ressource restaurée avec succès.');
            } else {
                return redirect()->back()->with('error', 'Cette ressource existe déjà pour cette séquence et ce type.');
            }
        }

        // Créer une nouvelle ressource
        $ressource = RessourcePedagogique::create([
            'titre' => $request->titre,
            'id_sequence' => $request->id_sequence,
            'id_type' => $request->id_type,
        ]);

        $sequence = SequencePedagogique::with('cours')->find($request->id_sequence);
        $type = TypeRessource::find($request->id_type);

        if (function_exists('logActivite')) {
            logActivite('création', "Création de la ressource : {$request->titre} ({$type->libelle}) - Séq. {$sequence->numero_ordre} ({$sequence->cours->code_cours})", $ressource);
        }

        return redirect()->route('ressources.index')->with('success', 'Ressource créée avec succès.');
    }

    // === UPDATE RESSOURCE ===
    public function updateRessource(UpdateRessourceRequest $request, int $id)
    {
        $ressource = RessourcePedagogique::with(['sequence.cours', 'typeRessource'])->findOrFail($id);

        $sequence = SequencePedagogique::with('cours')->find($request->id_sequence);
        $type = TypeRessource::find($request->id_type);

        $ressource->update([
            'titre' => $request->titre,
            'id_sequence' => $request->id_sequence,
            'id_type' => $request->id_type,
        ]);

        if (function_exists('logActivite')) {
            logActivite('modification', "Modification de la ressource : {$request->titre} ({$type->libelle}) - Séq. {$sequence->numero_ordre} ({$sequence->cours->code_cours})", $ressource);
        }

        return redirect()->route('ressources.index')->with('success', 'Ressource modifiée avec succès.');
    }

    // === DESTROY RESSOURCE ===
    public function destroyRessource(int $id)
    {
        $ressource = RessourcePedagogique::with(['sequence.cours', 'typeRessource'])->withCount('activitesPedagogiques')->findOrFail($id);

        // Vérifier s'il y a des activités pédagogiques associées
        if ($ressource->activites_pedagogiques_count > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer cette ressource car elle a des activités pédagogiques associées.');
        }

        $sequence = $ressource->sequence;
        $type = $ressource->typeRessource;

        $ressource->delete();

        if (function_exists('logActivite')) {
            logActivite('suppression', "Suppression de la ressource : {$ressource->titre} ({$type->libelle}) - Séq. {$sequence->numero_ordre} ({$sequence->cours->code_cours})", $ressource);
        }

        return redirect()->route('ressources.index')->with('success', 'Ressource supprimée avec succès.');
    }

    // === TYPES RESSOURCES ===
    public function typesRessources()
    {
        $types = TypeRessource::withCount('ressourcesPedagogiques')->get();
        return view('pedagogie.types-ressources', compact('types'));
    }

    // === STORE TYPE RESSOURCE ===
    public function storeTypeRessource(StoreTypeRessourceRequest $request)
    {
        // Vérifier si un type existe déjà avec ce libellé
        $existing = TypeRessource::withTrashed()
            ->where('libelle', $request->libelle)
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                // Restaurer le type soft-deleted
                $existing->restore();

                if (function_exists('logActivite')) {
                    logActivite('création', "Restauration du type de ressource : {$request->libelle}", $existing);
                }

                return redirect()->route('types.index')->with('success', 'Type de ressource restauré avec succès.');
            } else {
                return redirect()->back()->with('error', 'Ce libellé existe déjà.');
            }
        }

        // Créer un nouveau type de ressource
        $type = TypeRessource::create([
            'libelle' => $request->libelle,
        ]);

        if (function_exists('logActivite')) {
            logActivite('création', "Création du type de ressource : {$request->libelle}", $type);
        }

        return redirect()->route('types.index')->with('success', 'Type de ressource créé avec succès.');
    }

    // === UPDATE TYPE RESSOURCE ===
    public function updateTypeRessource(UpdateTypeRessourceRequest $request, int $id)
    {
        $type = TypeRessource::findOrFail($id);

        $type->update([
            'libelle' => $request->libelle,
        ]);

        if (function_exists('logActivite')) {
            logActivite('modification', "Modification du type de ressource : {$request->libelle}", $type);
        }

        return redirect()->route('types.index')->with('success', 'Type de ressource modifié avec succès.');
    }

    // === DESTROY TYPE RESSOURCE ===
    public function destroyTypeRessource(int $id)
    {
        $type = TypeRessource::withCount('ressourcesPedagogiques')->findOrFail($id);

        // Vérifier s'il y a des ressources pédagogiques associées
        if ($type->ressources_pedagogiques_count > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer ce type car il a des ressources pédagogiques associées.');
        }

        $type->delete();

        if (function_exists('logActivite')) {
            logActivite('suppression', "Suppression du type de ressource : {$type->libelle}", $type);
        }

        return redirect()->route('types.index')->with('success', 'Type de ressource supprimé avec succès.');
    }

    // === ACTIVITES ===
    public function activites()
    {
        return view('pedagogie.activites');
    }

    // === VOLUMES ===
    public function volumes()
    {
        return view('pedagogie.volumes');
    }

    // === COMPLEMENTAIRES ===
    public function complementaires()
    {
        return view('pedagogie.complementaires');
    }
}
