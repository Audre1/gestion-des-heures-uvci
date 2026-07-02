<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Vues uniquement
|--------------------------------------------------------------------------
| Chaque route retourne simplement une vue. La navigation (sidebar, liens,
| formulaires) est entièrement fonctionnelle côté affichage.
*/

// Authentification
Route::view('/', 'auth.login')->name('login');
Route::view('/mot-de-passe-oublie', 'auth.forgot-password')->name('password.request');
Route::view('/reinitialisation', 'auth.reset-password')->name('password.reset');
Route::view('/deconnexion', 'auth.login')->name('logout');

// Tableau de bord
Route::view('/tableau-de-bord', 'dashboard')->name('dashboard');

/*
|--------------------------------------------------------------------------
| Administration
|--------------------------------------------------------------------------
*/
Route::view('/utilisateurs', 'admin.utilisateurs')->name('utilisateurs.index');
// Route::view('/roles', 'admin.roles')->name('roles.index');
Route::view('/annees-academiques', 'admin.annees')->name('annees.index');
Route::view('/parametres-calcul', 'admin.parametres')->name('parametres.index');
Route::view('/taux-horaires', 'admin.taux')->name('taux.index');
Route::view('/journaux', 'admin.journaux')->name('journaux.index');
Route::view('/sauvegardes', 'admin.sauvegardes')->name('sauvegardes.index');

/*
|--------------------------------------------------------------------------
| Gestion pédagogique (Secrétaire Principal)
|--------------------------------------------------------------------------
*/
Route::view('/enseignants', 'pedagogie.enseignants')->name('enseignants.index');
Route::view('/grades', 'pedagogie.grades')->name('grades.index');
Route::view('/departements', 'pedagogie.departements')->name('departements.index');
Route::view('/filieres', 'pedagogie.filieres')->name('filieres.index');
Route::view('/cours', 'pedagogie.cours')->name('cours.index');
Route::view('/affectations', 'pedagogie.affectations')->name('affectations.index');
Route::view('/sequences', 'pedagogie.sequences')->name('sequences.index');
Route::view('/ressources', 'pedagogie.ressources')->name('ressources.index');
Route::view('/types-ressources', 'pedagogie.types-ressources')->name('types.index');
Route::view('/niveaux-complexite', 'pedagogie.niveaux')->name('niveaux.index');
Route::view('/activites', 'pedagogie.activites')->name('activites.index');
Route::view('/volumes-horaires', 'pedagogie.volumes')->name('volumes.index');
Route::view('/heures-complementaires', 'pedagogie.complementaires')->name('complementaires.index');

/*
|--------------------------------------------------------------------------
| Paiements & Rapports
|--------------------------------------------------------------------------
*/
Route::view('/etats-paiement', 'paiements.index')->name('paiements.index');
Route::view('/rapports', 'rapports.index')->name('rapports.index');

/*
|--------------------------------------------------------------------------
| Espace Enseignant
|--------------------------------------------------------------------------
*/
Route::view('/espace/activites', 'espace.activites')->name('espace.activites');
Route::view('/espace/volume-horaire', 'espace.volume')->name('espace.volume');
Route::view('/espace/heures-complementaires', 'espace.complementaires')->name('espace.complementaires');
Route::view('/espace/ressources', 'espace.ressources')->name('espace.ressources');
Route::view('/espace/documents', 'espace.documents')->name('espace.documents');

/*
|--------------------------------------------------------------------------
| Compte
|--------------------------------------------------------------------------
*/
Route::view('/profil', 'compte.profil')->name('profil.index');
