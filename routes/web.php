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

/*
|--------------------------------------------------------------------------
| Web Routes — Vues uniquement
|--------------------------------------------------------------------------
| Chaque route retourne simplement une vue. La navigation (sidebar, liens,
| formulaires) est entièrement fonctionnelle côté affichage.
*/

// Authentification
Route::get('/connexion', [AuthController::class, 'login'])->name('login');
Route::post('/connexion', [AuthController::class, 'authenticate'])->name('authenticate');
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
Route::get('/grades', [PedagogieController::class, 'grades'])->name('grades.index');
Route::get('/departements', [PedagogieController::class, 'departements'])->name('departements.index');
Route::get('/filieres', [PedagogieController::class, 'filieres'])->name('filieres.index');
Route::get('/cours', [PedagogieController::class, 'cours'])->name('cours.index');
Route::get('/affectations', [PedagogieController::class, 'affectations'])->name('affectations.index');
Route::get('/sequences', [PedagogieController::class, 'sequences'])->name('sequences.index');
Route::get('/ressources', [PedagogieController::class, 'ressources'])->name('ressources.index');
Route::get('/types-ressources', [PedagogieController::class, 'typesRessources'])->name('types.index');
Route::get('/niveaux-complexite', [PedagogieController::class, 'niveauxComplexite'])->name('niveaux.index');
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

/*
|--------------------------------------------------------------------------
| Compte
|--------------------------------------------------------------------------
*/
Route::get('/profil', [CompteController::class, 'profil'])->name('profil.index');
