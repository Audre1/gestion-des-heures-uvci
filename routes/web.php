<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EspaceController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\PedagogieController;
use App\Http\Controllers\RapportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;

/*
|--------------------------------------------------------------------------
| Web Routes — Vues uniquement
|--------------------------------------------------------------------------
| Chaque route retourne simplement une vue. La navigation (sidebar, liens,
| formulaires) est entièrement fonctionnelle côté affichage.
*/

// Authentification


Route::get('/connexion', [AuthController::class, 'login'])->name('login');
Route::post('/connexion', [AuthController::class, 'authenticate'])->name('login.authenticate');
Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');

Route::get('/mot-de-passe-oublie', [AuthController::class, 'forgotPassword'])->name('password.request');
Route::post('/mot-de-passe-oublie', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reinitialisation', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::post('/verifier-code', [AuthController::class, 'verifyCode'])->name('password.verify');
Route::get('/nouveau-mot-de-passe', [AuthController::class, 'newPassword'])->name('password.new');
Route::post('/nouveau-mot-de-passe', [AuthController::class, 'updatePassword'])->name('password.update');
Route::post('/renvoyer-code', [AuthController::class, 'resendCode'])->name('password.resend');


// Tableau de bord
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/tableau-de-bord', [DashboardController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Administration
|--------------------------------------------------------------------------
*/
Route::get('/utilisateurs', [AdminController::class, 'utilisateurs'])->name('utilisateurs.index');
Route::post('/utilisateurs', [AdminController::class, 'store'])->name('utilisateurs.store');
Route::put('/utilisateurs/{id}', [AdminController::class, 'update'])->name('utilisateurs.update');
Route::delete('/utilisateurs/{id}', [AdminController::class, 'destroy'])->name('utilisateurs.destroy');
// Route::get('/roles', [AdminController::class, 'roles'])->name('roles.index');
Route::get('/annees-academiques', [AdminController::class, 'annees'])->name('annees.index');
Route::post('/annees-academiques', [AdminController::class, 'storeAnnee'])->name('annees.store');
Route::put('/annees-academiques/{id}', [AdminController::class, 'updateAnnee'])->name('annees.update');
Route::delete('/annees-academiques/{id}', [AdminController::class, 'destroyAnnee'])->name('annees.destroy');
Route::patch('/annees-academiques/{id}/activate', [AdminController::class, 'activateAnnee'])->name('annees.activate');
Route::get('/niveaux-complexite', [AdminController::class, 'niveauxComplexite'])->name('niveaux.index');
Route::post('/niveaux-complexite', [AdminController::class, 'storeNiveauComplexite'])->name('niveaux.store');
Route::put('/niveaux-complexite/{id}', [AdminController::class, 'updateNiveauComplexite'])->name('niveaux.update');
Route::delete('/niveaux-complexite/{id}', [AdminController::class, 'destroyNiveauComplexite'])->name('niveaux.destroy');
Route::get('/parametres-calcul', [AdminController::class, 'parametres'])->name('parametres.index');
Route::put('/parametres-calcul', [AdminController::class, 'updateParametres'])->name('parametres.update');
Route::get('/taux-horaires', [AdminController::class, 'taux'])->name('taux.index');
Route::post('/taux-horaires', [AdminController::class, 'storeTaux'])->name('taux.store');
Route::put('/taux-horaires/{id}', [AdminController::class, 'updateTaux'])->name('taux.update');
Route::delete('/taux-horaires/{id}', [AdminController::class, 'destroyTaux'])->name('taux.destroy');
Route::get('/journaux', [AdminController::class, 'journaux'])->name('journaux.index');
Route::get('/sauvegardes', [AdminController::class, 'sauvegardes'])->name('sauvegardes.index');
Route::post('/sauvegardes', [AdminController::class, 'createBackup'])->name('sauvegardes.create');
Route::get('/sauvegardes/{filename}/download', [AdminController::class, 'downloadBackup'])->name('sauvegardes.download');
Route::post('/sauvegardes/{filename}/restore', [AdminController::class, 'restoreBackup'])->name('sauvegardes.restore');
Route::delete('/sauvegardes/{filename}', [AdminController::class, 'deleteBackup'])->name('sauvegardes.destroy');

/*
|--------------------------------------------------------------------------
| Gestion pédagogique (Secrétaire Principal)
|--------------------------------------------------------------------------
*/
Route::get('/enseignants', [PedagogieController::class, 'enseignants'])->name('enseignants.index');
Route::post('/enseignants', [PedagogieController::class, 'storeEnseignant'])->name('enseignants.store');
Route::put('/enseignants/{id}', [PedagogieController::class, 'updateEnseignant'])->name('enseignants.update');
Route::delete('/enseignants/{id}', [PedagogieController::class, 'destroyEnseignant'])->name('enseignants.destroy');
Route::get('/grades', [PedagogieController::class, 'grades'])->name('grades.index');
Route::post('/grades', [PedagogieController::class, 'storeGrade'])->name('grades.store');
Route::put('/grades/{id}', [PedagogieController::class, 'updateGrade'])->name('grades.update');
Route::delete('/grades/{id}', [PedagogieController::class, 'destroyGrade'])->name('grades.destroy');
Route::get('/departements', [PedagogieController::class, 'departements'])->name('departements.index');
Route::post('/departements', [PedagogieController::class, 'storeDepartement'])->name('departements.store');
Route::put('/departements/{id}', [PedagogieController::class, 'updateDepartement'])->name('departements.update');
Route::delete('/departements/{id}', [PedagogieController::class, 'destroyDepartement'])->name('departements.destroy');
Route::get('/filieres', [PedagogieController::class, 'filieres'])->name('filieres.index');
Route::post('/filieres', [PedagogieController::class, 'storeFiliere'])->name('filieres.store');
Route::put('/filieres/{id}', [PedagogieController::class, 'updateFiliere'])->name('filieres.update');
Route::delete('/filieres/{id}', [PedagogieController::class, 'destroyFiliere'])->name('filieres.destroy');
Route::post('/filieres/{filiereId}/attach-cours', [PedagogieController::class, 'attachCoursToFiliere'])->name('filieres.attach-cours');
Route::delete('/filieres/{filiereId}/detach-cours/{coursId}/{semestre}/{niveau}', [PedagogieController::class, 'detachCoursFromFiliere'])->name('filieres.detach-cours');
Route::get('/cours', [PedagogieController::class, 'cours'])->name('cours.index');
Route::post('/cours', [PedagogieController::class, 'storeCours'])->name('cours.store');
Route::put('/cours/{id}', [PedagogieController::class, 'updateCours'])->name('cours.update');
Route::delete('/cours/{id}', [PedagogieController::class, 'destroyCours'])->name('cours.destroy');
Route::get('/affectations', [PedagogieController::class, 'affectations'])->name('affectations.index');
Route::post('/affectations', [PedagogieController::class, 'storeAffectation'])->name('affectations.store');
Route::put('/affectations/{id}', [PedagogieController::class, 'updateAffectation'])->name('affectations.update');
Route::delete('/affectations/{id}', [PedagogieController::class, 'destroyAffectation'])->name('affectations.destroy');
Route::get('/sequences', [PedagogieController::class, 'sequences'])->name('sequences.index');
Route::post('/sequences', [PedagogieController::class, 'storeSequence'])->name('sequences.store');
Route::put('/sequences/{id}', [PedagogieController::class, 'updateSequence'])->name('sequences.update');
Route::delete('/sequences/{id}', [PedagogieController::class, 'destroySequence'])->name('sequences.destroy');
Route::post('/sequences/reorder', [PedagogieController::class, 'reorderSequences'])->name('sequences.reorder');
Route::get('/ressources', [PedagogieController::class, 'ressources'])->name('ressources.index');
Route::post('/ressources', [PedagogieController::class, 'storeRessource'])->name('ressources.store');
Route::put('/ressources/{id}', [PedagogieController::class, 'updateRessource'])->name('ressources.update');
Route::delete('/ressources/{id}', [PedagogieController::class, 'destroyRessource'])->name('ressources.destroy');
Route::get('/types-ressources', [PedagogieController::class, 'typesRessources'])->name('types.index');
Route::post('/types-ressources', [PedagogieController::class, 'storeTypeRessource'])->name('types.store');
Route::put('/types-ressources/{id}', [PedagogieController::class, 'updateTypeRessource'])->name('types.update');
Route::delete('/types-ressources/{id}', [PedagogieController::class, 'destroyTypeRessource'])->name('types.destroy');
Route::get('/activites', [PedagogieController::class, 'activites'])->name('activites.index');
Route::get('/volumes-horaires', [PedagogieController::class, 'volumes'])->name('volumes.index');
Route::get('/heures-complementaires', [PedagogieController::class, 'complementaires'])->name('complementaires.index');

/*
|--------------------------------------------------------------------------
| Paiements & Rapports
|--------------------------------------------------------------------------
*/
Route::get('/etats-paiement', [PaiementController::class, 'index'])->name('paiements.index');
Route::get('/rapports', [RapportController::class, 'index'])->name('rapports.index');

/*
|--------------------------------------------------------------------------
| Espace Enseignant
|--------------------------------------------------------------------------
*/
Route::get('/espace/activites', [EspaceController::class, 'activites'])->name('espace.activites');
Route::get('/espace/volume-horaire', [EspaceController::class, 'volume'])->name('espace.volume');
Route::get('/espace/heures-complementaires', [EspaceController::class, 'complementaires'])->name('espace.complementaires');
Route::get('/espace/ressources', [EspaceController::class, 'ressources'])->name('espace.ressources');
Route::get('/espace/documents', [EspaceController::class, 'documents'])->name('espace.documents');
Route::get('/documents/recapitulatif-activites', [DocumentController::class, 'recapitulatifActivites'])->name('documents.recapitulatif');
Route::get('/documents/fiche-individuelle', [DocumentController::class, 'ficheIndividuelle'])->name('documents.fiche');
Route::get('/documents/etat-heures', [DocumentController::class, 'etatHeures'])->name('documents.heures');
/*
|--------------------------------------------------------------------------
| Compte
|--------------------------------------------------------------------------
*/
Route::get('/profil', [CompteController::class, 'profil'])->name('profil.index');
