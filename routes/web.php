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
Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/connexion', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');
Route::get('/mot-de-passe-oublie', [AuthController::class, 'forgotPassword'])->name('password.request');
Route::post('/mot-de-passe-oublie', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reinitialisation', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::post('/reinitialisation', [AuthController::class, 'updatePassword'])->name('password.update');

// Tableau de bord
Route::get('/tableau-de-bord', [DashboardController::class, 'index'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Administration
|--------------------------------------------------------------------------
*/
Route::get('/utilisateurs', [AdminController::class, 'utilisateurs'])->name('utilisateurs.index');
// Route::get('/roles', [AdminController::class, 'roles'])->name('roles.index');
Route::get('/annees-academiques', [AdminController::class, 'annees'])->name('annees.index');
Route::get('/parametres-calcul', [AdminController::class, 'parametres'])->name('parametres.index');
Route::get('/taux-horaires', [AdminController::class, 'taux'])->name('taux.index');
Route::get('/journaux', [AdminController::class, 'journaux'])->name('journaux.index');
Route::get('/sauvegardes', [AdminController::class, 'sauvegardes'])->name('sauvegardes.index');

/*
|--------------------------------------------------------------------------
| Gestion pédagogique (Secrétaire Principal)
|--------------------------------------------------------------------------
*/
Route::get('/enseignants', [PedagogieController::class, 'enseignants'])->name('enseignants.index');
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
