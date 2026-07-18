# DOCUMENTATION TECHNIQUE — SYSTÈME DE GESTION DES HEURES UVCI

## Université Virtuelle de Côte d'Ivoire (UVCI)

**Version :** 1.0.0
**Dernière mise à jour :** Juillet 2026

---

## TABLE DES MATIÈRES

1. [Présentation générale](#1-présentation-générale)
2. [Architecture de l'application](#2-architecture-de-lapplication)
3. [Technologies utilisées](#3-technologies-utilisées)
4. [Structure des dossiers](#4-structure-des-dossiers)
5. [Base de données](#5-base-de-données)
6. [Installation et configuration](#6-installation-et-configuration)
7. [Modules et fonctionnalités](#7-modules-et-fonctionnalités)
8. [Sécurité](#8-sécurité)
9. [Sauvegarde et restauration](#9-sauvegarde-et-restauration)
10. [Déploiement](#10-déploiement)
11. [Maintenance](#11-maintenance)
12. [Journal des versions](#12-journal-des-versions)

---

## 1. PRÉSENTATION GÉNÉRALE

### 1.1 Description du projet

L'application **UVCI — Gestion des Heures des Enseignants** est une plateforme web développée pour l'Université Virtuelle de Côte d'Ivoire (UVCI). Elle permet d'automatiser la gestion des heures d'enseignement, incluant :

- La gestion des comptes utilisateurs et des rôles (Administrateur, Secrétaire, Enseignant)
- La gestion pédagogique (enseignants, cours, filières, départements, etc.)
- L'enregistrement et la validation des activités pédagogiques
- Le calcul automatisé des volumes horaires (VHT) et des heures complémentaires
- La génération d'états de paiement
- La production de rapports et statistiques

### 1.2 Public cible

- **Administrateurs** : gestion technique et paramétrage du système
- **Secrétaires** : gestion pédagogique et suivi des activités
- **Enseignants** : consultation de l'espace personnel

---

## 2. ARCHITECTURE DE L'APPLICATION

### 2.1 Modèle architectural

L'application suit une architecture **MVC (Modèle-Vue-Contrôleur)** implémentée via le framework Laravel.

```
┌─────────────────────────────────────────────────────────┐
│                     NAVIGATEUR WEB                       │
└────────────────────┬────────────────────────────────────┘
                     │ HTTP Request / Response
┌────────────────────▼────────────────────────────────────┐
│                      ROUTES (web.php)                    │
│                     Middleware (auth, rôle)              │
└────────────────────┬────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────┐
│                    CONTROLLERS                           │
│  AdminController | PedagogieController | AuthController  │
│  DashboardController | PaiementController | Rapport...  │
└───────┬──────────────────────────────┬──────────────────┘
        │                              │
┌───────▼──────────┐    ┌──────────────▼─────────────────┐
│   FORM REQUESTS  │    │          SERVICES               │
│  (Validation)    │    │  BackupService | RapportService │
└──────────────────┘    │  ExportService                  │
                        └────────────────────────────────┘
        │                              │
┌───────▼──────────────────────────────▼──────────────────┐
│                     MODELS (Eloquent ORM)                │
│  Utilisateur | Enseignant | Cours | ActivitePedagogique │
│  AffectationCours | ParametreCalcul | EtatPaiement ...  │
└────────────────────┬────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────┐
│                  BASE DE DONNÉES (MySQL)                 │
└─────────────────────────────────────────────────────────┘
```

### 2.2 Flux de navigation

1. L'utilisateur se connecte via `AuthController`
2. Le middleware `auth` vérifie l'authentification
3. Le middleware de rôle (`CheckAdmin`, `CheckSecretaire`) vérifie les permissions
4. Le `DashboardController` redirige vers la vue appropriée selon le rôle
5. Les contrôleurs traitent les requêtes CRUD via les `FormRequest` pour la validation
6. Les `Services` (BackupService, RapportService, ExportService) encapsulent la logique métier complexe
7. Les `Models` Eloquent interagissent avec la base de données

---

## 3. TECHNOLOGIES UTILISÉES

### 3.1 Backend

| Technologie | Version | Utilisation |
|-------------|---------|-------------|
| PHP | 8.x | Langage de programmation |
| Laravel | 11.x | Framework MVC |
| MySQL | 8.x | Base de données relationnelle |

### 3.2 Frontend

| Technologie | Version | Utilisation |
|-------------|---------|-------------|
| Bootstrap | 5.x | Framework CSS responsive |
| Font Awesome | 6.x | Icônes vectorielles |
| Chart.js | 4.x | Graphiques statistiques |
| Blade | - | Moteur de templates Laravel |

### 3.3 Librairies et packages

| Package | Utilisation |
|---------|-------------|
| Laravel UI | Authentification et scaffolding |
| DomPDF | Génération de documents PDF |
| Laravel Excel | Export de données au format Excel |
| Mail (Laravel) | Envoi d'emails (OTP, notifications) |

---

## 4. STRUCTURE DES DOSSIERS

### 4.1 Arborescence principale

```
uvci-gestion-heures/
│
├── app/
│   ├── Helpers/
│   │   └── functions.php              # Fonctions globales (logActivite)
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php          # Controller de base
│   │   │   ├── AdminController.php     # Gestion administration (CRUD utilisateurs, années, etc.)
│   │   │   ├── AuthController.php      # Authentification (connexion, OTP, réinitialisation)
│   │   │   ├── CompteController.php    # Gestion du profil utilisateur
│   │   │   ├── DashboardController.php # Tableaux de bord (admin, secretaire, enseignant)
│   │   │   ├── DocumentController.php  # Génération de documents PDF pour enseignants
│   │   │   ├── EspaceController.php    # Espace enseignant (consultation)
│   │   │   ├── ExportController.php    # Export PDF/Excel des tableaux
│   │   │   ├── PaiementController.php  # Gestion des états de paiement
│   │   │   ├── PedagogieController.php # Gestion pédagogique (CRUD complet)
│   │   │   ├── RapportController.php   # Génération des rapports
│   │   │   └── SearchController.php    # Recherche globale
│   │   │
│   │   ├── Middleware/
│   │   │   ├── AutoBackupMiddleware.php # Sauvegarde automatique
│   │   │   ├── CheckAdmin.php          # Vérification rôle admin
│   │   │   ├── CheckEnseignant.php     # Vérification rôle enseignant
│   │   │   ├── CheckRole.php           # Vérification multi-rôles (admin, secretaire)
│   │   │   └── CheckSecretaire.php     # Vérification rôle secrétaire
│   │   │
│   │   ├── Requests/                   # 30 FormRequests (validation)
│   │   │   ├── StoreUtilisateurRequest.php
│   │   │   ├── UpdateUtilisateurRequest.php
│   │   │   ├── StoreEnseignantRequest.php
│   │   │   ├── StoreActivitePedagogiqueRequest.php
│   │   │   ├── StoreAffectationRequest.php
│   │   │   ├── StoreAnneeAcademiqueRequest.php
│   │   │   ├── StoreCoursRequest.php
│   │   │   ├── StoreDepartementRequest.php
│   │   │   ├── StoreFiliereRequest.php
│   │   │   ├── StoreGradeRequest.php
│   │   │   ├── StoreNiveauComplexiteRequest.php
│   │   │   ├── StoreRessourceRequest.php
│   │   │   ├── StoreSequenceRequest.php
│   │   │   ├── StoreTauxHoraireRequest.php
│   │   │   ├── StoreTypeRessourceRequest.php
│   │   │   ├── GenerateEtatPaiementRequest.php
│   │   │   ├── UpdateActivitePedagogiqueRequest.php
│   │   │   ├── UpdateAffectationRequest.php
│   │   │   ├── UpdateAnneeAcademiqueRequest.php
│   │   │   ├── UpdateCoursRequest.php
│   │   │   ├── UpdateDepartementRequest.php
│   │   │   ├── UpdateEnseignantRequest.php
│   │   │   ├── UpdateFiliereRequest.php
│   │   │   ├── UpdateGradeRequest.php
│   │   │   ├── UpdateNiveauComplexiteRequest.php
│   │   │   ├── UpdateParametreCalculRequest.php
│   │   │   ├── UpdateRessourceRequest.php
│   │   │   ├── UpdateSequenceRequest.php
│   │   │   ├── UpdateTauxHoraireRequest.php
│   │   │   └── UpdateTypeRessourceRequest.php
│   │   │
│   │   └── ViewComposers/
│   │       └── TopbarComposer.php       # Composition de la topbar
│   │
│   ├── Mail/
│   │   └── ResetPasswordMail.php        # Email de réinitialisation du mot de passe
│   │
│   ├── Models/                          # 18 modèles Eloquent
│   │   ├── Utilisateur.php              # Authenticatable (table users)
│   │   ├── User.php                     # Modèle Laravel par défaut
│   │   ├── Role.php
│   │   ├── Enseignant.php
│   │   ├── Grade.php
│   │   ├── Departement.php
│   │   ├── Filiere.php
│   │   ├── Cours.php
│   │   ├── AffectationCours.php
│   │   ├── SequencePedagogique.php
│   │   ├── ActivitePedagogique.php
│   │   ├── NiveauComplexite.php
│   │   ├── AnneeAcademique.php
│   │   ├── TauxHoraire.php
│   │   ├── ParametreCalcul.php
│   │   ├── RessourcePedagogique.php
│   │   ├── TypeRessource.php
│   │   ├── EtatPaiement.php
│   │   └── JournalActivite.php
│   │
│   ├── Providers/
│   │   └── AppServiceProvider.php       # Enregistrement des View Composers
│   │
│   └── Services/
│       ├── BackupService.php            # Sauvegarde/restauration base de données
│       ├── ExportService.php            # Export PDF et Excel
│       └── RapportService.php           # Génération des rapports métier
│
├── bootstrap/
│
├── config/
│   ├── app.php
│   ├── database.php
│   ├── mail.php
│   └── ...
│
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   │
│   ├── migrations/                      # 20 fichiers de migration
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2026_01_01_000010_create_roles_table.php
│   │   ├── 2026_01_01_000020_create_utilisateurs_table.php
│   │   ├── 2026_01_01_000030_create_grades_table.php
│   │   ├── 2026_01_01_000040_create_departements_table.php
│   │   ├── 2026_01_01_000050_create_enseignants_table.php
│   │   ├── 2026_01_01_000060_create_annees_academiques_table.php
│   │   ├── 2026_01_01_000070_create_taux_horaires_table.php
│   │   ├── 2026_01_01_000080_create_filieres_table.php
│   │   ├── 2026_01_01_000090_create_cours_table.php
│   │   ├── 2026_01_01_000100_create_filiere_cours_table.php
│   │   ├── 2026_01_01_000110_create_sequences_pedagogiques_table.php
│   │   ├── 2026_01_01_000120_create_type_ressources_table.php
│   │   ├── 2026_01_01_000130_create_ressources_pedagogiques_table.php
│   │   ├── 2026_01_01_000140_create_niveaux_complexite_table.php
│   │   ├── 2026_01_01_000150_create_affectations_cours_table.php
│   │   ├── 2026_01_01_000160_create_activites_pedagogiques_table.php
│   │   ├── 2026_01_01_000170_create_etats_paiement_table.php
│   │   ├── 2026_07_08_041656_add_created_by_to_users_table.php
│   │   ├── 2026_07_10_000001_create_parametres_calcul_table.php
│   │   ├── 2026_07_10_135046_create_journaux_activites_table.php
│   │   ├── 2026_07_10_182740_modify_enseignants_statut_and_add_taux_horaire_perso.php
│   │   ├── 2026_07_11_182028_remove_coefficient_columns_from_parametres_calcul_table.php
│   │   ├── 2026_07_12_181318_update_type_activite_enum_in_activites_pedagogiques_table.php
│   │   └── 2026_07_17_140249_add_backup_settings_to_parametres_calcul_table.php
│   │
│   └── seeders/                         # 16 seeders
│       ├── DatabaseSeeder.php           # Seeder principal
│       ├── RoleSeeder.php
│       ├── AdminSeeder.php
│       ├── UserSeeder.php
│       ├── GradeSeeder.php
│       ├── DepartementSeeder.php
│       ├── EnseignantSeeder.php
│       ├── AnneeAcademiqueSeeder.php
│       ├── NiveauComplexiteSeeder.php
│       ├── ParametreCalculSeeder.php
│       ├── TauxHoraireSeeder.php
│       ├── CoursSeeder.php
│       ├── FiliereSeeder.php
│       ├── AffectationCoursSeeder.php
│       ├── SequencePedagogiqueSeeder.php
│       ├── ActivitePedagogiqueSeeder.php
│       ├── RessourcePedagogiqueSeeder.php
│       └── TypeRessourceSeeder.php
│
├── docs/
│   ├── MANUEL_UTILISATION.md            # Guide utilisateur
│   └── DOCUMENTATION_TECHNIQUE.md       # Documentation technique (ce fichier)
│
├── resources/
│   ├── views/                           # Vues Blade (voir section spécifique)
│   └── ...
│
├── routes/
│   └── web.php                          # Routes de l'application
│
└── public/
```

### 4.2 Structure des vues Blade

```
resources/views/
├── layouts/
│   ├── auth.blade.php                   # Layout pour les pages d'authentification
│   └── app.blade.php                    # Layout principal de l'application
│
├── partials/
│   ├── sidebar.blade.php                # Navigation latérale (adaptée au rôle)
│   └── topbar.blade.php                 # Barre supérieure (recherche, notifications)
│
├── components/
│   ├── app-page.blade.php               # Composant page standard avec titre et actions
│   ├── data-table.blade.php             # Composant tableau avec recherche, filtre, export
│   └── notifications.blade.php          # Composant de notifications
│
├── auth/
│   ├── login.blade.php                  # Page de connexion
│   ├── forgot-password.blade.php        # Demande de réinitialisation
│   ├── reset-password.blade.php         # Saisie du code OTP
│   └── new-password.blade.php           # Nouveau mot de passe
│
├── admin/                               # 8 pages (admin uniquement)
│   ├── utilisateurs.blade.php           # Gestion des utilisateurs
│   ├── roles.blade.php                  # Profils d'accès
│   ├── annees.blade.php                 # Années académiques
│   ├── niveaux.blade.php                # Niveaux de complexité
│   ├── parametres.blade.php             # Paramètres de calcul
│   ├── taux.blade.php                   # Taux horaires
│   ├── journaux.blade.php               # Journaux d'activités
│   └── sauvegardes.blade.php            # Sauvegardes
│
├── pedagogie/                           # 12 pages (secretaire)
│   ├── enseignants.blade.php            # Gestion des enseignants
│   ├── grades.blade.php                 # Grades
│   ├── departements.blade.php           # Départements
│   ├── filieres.blade.php               # Filières
│   ├── cours.blade.php                  # Cours
│   ├── affectations.blade.php           # Affectations
│   ├── sequences.blade.php              # Séquences pédagogiques
│   ├── ressources.blade.php             # Ressources pédagogiques
│   ├── types-ressources.blade.php       # Types de ressources
│   ├── activites.blade.php              # Activités pédagogiques
│   ├── volumes.blade.php                # Volumes horaires
│   └── complementaires.blade.php        # Heures complémentaires
│
├── paiements/
│   └── index.blade.php                  # États de paiement
│
├── rapports/
│   ├── index.blade.php                  # Liste des rapports disponibles
│   ├── fiche-individuelle.blade.php     # Formulaire fiche individuelle
│   ├── etat-global.blade.php            # Formulaire état global
│   ├── statistiques.blade.php           # Formulaire statistiques
│   ├── heures-complementaires.blade.php # Formulaire heures complémentaires
│   ├── paiement-collectif.blade.php     # Formulaire paiement collectif
│   └── charge-departement.blade.php     # Formulaire charge département
│
├── espace/                              # 6 pages (enseignant)
│   ├── activites.blade.php              # Mes activités
│   ├── volume.blade.php                 # Mon volume horaire
│   ├── complementaires.blade.php        # Mes heures complémentaires
│   ├── ressources.blade.php             # Mes ressources
│   └── documents.blade.php              # Mes documents (téléchargement PDF)
│
├── compte/
│   └── profil.blade.php                 # Profil utilisateur
│
├── doc/
│   ├── recapitulatif-activites.blade.php # Template PDF récapitulatif
│   ├── fiche-individuelle.blade.php     # Template PDF fiche individuelle
│   └── etat-heures.blade.php            # Template PDF état des heures
│
├── pdf/
│   ├── recapitulatif-activites.blade.php
│   ├── fiche-individuelle.blade.php
│   └── etat-heures.blade.php
│
├── exports/
│   └── pdf-template.blade.php           # Template générique PDF
│
├── emails/
│   └── reset-password.blade.php         # Template email réinitialisation
│
└── errors/
    ├── 403.blade.php                    # Page accès refusé
    └── 404.blade.php                    # Page non trouvée
```

---

## 5. BASE DE DONNÉES

### 5.1 Modèle Conceptuel de Données (MCD)

L'application repose sur 16 tables principales :

```
roles (1) ────< (0,N) users (utilisateurs)
users (1) ────< (0,1) enseignants
grades (1) ────< (0,N) enseignants
departements (1) ────< (0,N) enseignants
enseignants (1) ────< (0,N) affectations_cours
cours (1) ────< (0,N) affectations_cours
annees_academiques (1) ────< (0,N) affectations_cours
affectations_cours (1) ────< (0,N) sequences_pedagogiques
affectations_cours (1) ────< (0,N) activites_pedagogiques
cours (1) ────< (0,N) filiere_cours (pivot)
filieres (1) ────< (0,N) filiere_cours (pivot)
niveaux_complexite (1) ────< (0,N) activites_pedagogiques
ressources_pedagogiques (1) ────< (0,N) activites_pedagogiques
types_ressources (1) ────< (0,N) ressources_pedagogiques
grades (1) ────< (0,N) taux_horaires
annees_academiques (1) ────< (0,N) taux_horaires
annees_academiques (1) ────< (0,N) parametres_calcul
annees_academiques (1) ────< (0,N) etats_paiement
enseignants (1) ────< (0,N) etats_paiement
```

### 5.2 Description des tables

#### roles
| Champ | Type | Contrainte | Description |
|-------|------|-----------|-------------|
| id | BIGINT PK | Auto-incrément | Identifiant |
| code | VARCHAR(50) | UNIQUE | Code du rôle (admin, secretaire, enseignant) |
| libelle | VARCHAR(100) | UNIQUE | Libellé du rôle |
| created_at | TIMESTAMP | | Date de création |
| updated_at | TIMESTAMP | | Date de modification |
| deleted_at | TIMESTAMP | NULL | Soft delete |

#### users (utilisateurs)
| Champ | Type | Contrainte | Description |
|-------|------|-----------|-------------|
| id | BIGINT PK | Auto-incrément | Identifiant |
| nom | VARCHAR(100) | NOT NULL | Nom de famille |
| prenom | VARCHAR(100) | NOT NULL | Prénom |
| email | VARCHAR(150) | UNIQUE | Adresse email |
| telephone | VARCHAR(20) | NULL | Numéro de téléphone |
| login | VARCHAR(100) | UNIQUE | Identifiant de connexion |
| mot_de_passe | VARCHAR(255) | NOT NULL | Mot de passe (hashé) |
| date_creation | TIMESTAMP | CURRENT_TIMESTAMP | Date de création du compte |
| statut_compte | ENUM | 'actif' | actif, inactif, suspendu |
| remember_token | VARCHAR(100) | NULL | Token de session |
| id_role | BIGINT FK | NOT NULL | Référence vers roles |
| created_by | BIGINT FK | NULL | Référence vers users (créateur) |
| created_at | TIMESTAMP | | Date de création |
| updated_at | TIMESTAMP | | Date de modification |
| deleted_at | TIMESTAMP | NULL | Soft delete |

#### enseignants
| Champ | Type | Contrainte | Description |
|-------|------|-----------|-------------|
| id | BIGINT PK | Auto-incrément | Identifiant |
| matricule | VARCHAR(50) | NOT NULL | Matricule de l'enseignant |
| statut | VARCHAR(20) | NOT NULL | Permanent ou Vacataire |
| taux_horaire_perso | DECIMAL(10,2) | NULL | Taux horaire personnalisé |
| date_recrutement | DATE | NOT NULL | Date de recrutement |
| id_grade | BIGINT FK | NOT NULL | Référence vers grades |
| id_departement | BIGINT FK | NOT NULL | Référence vers departements |
| id_utilisateur | BIGINT FK | NOT NULL | Référence vers users |
| created_at | TIMESTAMP | | Date de création |
| updated_at | TIMESTAMP | | Date de modification |
| deleted_at | TIMESTAMP | NULL | Soft delete |

#### grades
| Champ | Type | Contrainte | Description |
|-------|------|-----------|-------------|
| id | BIGINT PK | Auto-incrément | Identifiant |
| libelle | VARCHAR(100) | NOT NULL | Libellé du grade |
| code_grade | VARCHAR(20) | NULL | Code du grade |
| created_at | TIMESTAMP | | Date de création |
| updated_at | TIMESTAMP | | Date de modification |
| deleted_at | TIMESTAMP | NULL | Soft delete |

#### departements
| Champ | Type | Contrainte | Description |
|-------|------|-----------|-------------|
| id | BIGINT PK | Auto-incrément | Identifiant |
| nom_departement | VARCHAR(100) | NOT NULL | Nom du département |
| code_departement | VARCHAR(20) | NULL | Code du département |
| created_at | TIMESTAMP | | Date de création |
| updated_at | TIMESTAMP | | Date de modification |
| deleted_at | TIMESTAMP | NULL | Soft delete |

#### annees_academiques
| Champ | Type | Contrainte | Description |
|-------|------|-----------|-------------|
| id | BIGINT PK | Auto-incrément | Identifiant |
| libelle | VARCHAR(50) | NOT NULL | Ex: 2026-2027 |
| date_debut | DATE | NOT NULL | Date de début |
| date_fin | DATE | NOT NULL | Date de fin |
| statut | ENUM | 'a_venir' | a_venir, en_cours, cloturee |
| created_at | TIMESTAMP | | Date de création |
| updated_at | TIMESTAMP | | Date de modification |
| deleted_at | TIMESTAMP | NULL | Soft delete |

#### cours
| Champ | Type | Contrainte | Description |
|-------|------|-----------|-------------|
| id | BIGINT PK | Auto-incrément | Identifiant |
| code_cours | VARCHAR(20) | NOT NULL | Code du cours |
| intitule | VARCHAR(200) | NOT NULL | Intitulé du cours |
| nombre_credits | INT | NOT NULL | Nombre de crédits |
| nombre_heures | INT | NOT NULL | Volume horaire |
| created_at | TIMESTAMP | | Date de création |
| updated_at | TIMESTAMP | | Date de modification |
| deleted_at | TIMESTAMP | NULL | Soft delete |

#### filieres
| Champ | Type | Contrainte | Description |
|-------|------|-----------|-------------|
| id | BIGINT PK | Auto-incrément | Identifiant |
| code_filiere | VARCHAR(20) | NOT NULL | Code de la filière |
| nom_filiere | VARCHAR(200) | NOT NULL | Nom de la filière |
| id_departement | BIGINT FK | NOT NULL | Référence vers departements |
| created_at | TIMESTAMP | | Date de création |
| updated_at | TIMESTAMP | | Date de modification |
| deleted_at | TIMESTAMP | NULL | Soft delete |

#### filiere_cours (table pivot)
| Champ | Type | Contrainte | Description |
|-------|------|-----------|-------------|
| id | BIGINT PK | Auto-incrément | Identifiant |
| id_filiere | BIGINT FK | NOT NULL | Référence vers filieres |
| id_cours | BIGINT FK | NOT NULL | Référence vers cours |
| semestre | VARCHAR(2) | NOT NULL | Semestre (S1, S2) |
| niveau | VARCHAR(2) | NOT NULL | Niveau (L1, L2, M1, etc.) |
| created_at | TIMESTAMP | | Date de création |
| updated_at | TIMESTAMP | | Date de modification |

#### affectations_cours
| Champ | Type | Contrainte | Description |
|-------|------|-----------|-------------|
| id | BIGINT PK | Auto-incrément | Identifiant |
| date_affectation | DATE | NOT NULL | Date de l'affectation |
| id_enseignant | BIGINT FK | NOT NULL | Référence vers enseignants |
| id_cours | BIGINT FK | NOT NULL | Référence vers cours |
| niveau | VARCHAR(2) | NOT NULL | Niveau |
| semestre | VARCHAR(2) | NOT NULL | Semestre |
| id_annee | BIGINT FK | NOT NULL | Référence vers annees_academiques |
| created_at | TIMESTAMP | | Date de création |
| updated_at | TIMESTAMP | | Date de modification |
| deleted_at | TIMESTAMP | NULL | Soft delete |

**Contrainte d'unicité :** `UNIQUE(id_cours, niveau, semestre, id_annee)`

#### sequences_pedagogiques
| Champ | Type | Contrainte | Description |
|-------|------|-----------|-------------|
| id | BIGINT PK | Auto-incrément | Identifiant |
| libelle | VARCHAR(100) | NOT NULL | Libellé de la séquence |
| ordre | INT | NOT NULL | Ordre d'affichage |
| id_affectation | BIGINT FK | NOT NULL | Référence vers affectations_cours |
| created_at | TIMESTAMP | | Date de création |
| updated_at | TIMESTAMP | | Date de modification |
| deleted_at | TIMESTAMP | NULL | Soft delete |

#### activites_pedagogiques
| Champ | Type | Contrainte | Description |
|-------|------|-----------|-------------|
| id | BIGINT PK | Auto-incrément | Identifiant |
| type_activite | ENUM | NOT NULL | creation, maj |
| date_activite | DATE | NOT NULL | Date de l'activité |
| statut | ENUM | 'en_cours' | en_cours, validee, rejetee |
| coefficient | DECIMAL(5,3) | NOT NULL | Coefficient appliqué |
| nb_sequences | INT | NOT NULL | Nombre de séquences |
| volume_horaire | DECIMAL(8,2) | NOT NULL | VHT calculé |
| id_affectation | BIGINT FK | NOT NULL | Référence vers affectations_cours |
| id_ressource | BIGINT FK | NULL | Référence vers ressources_pedagogiques |
| id_niveau | BIGINT FK | NOT NULL | Référence vers niveaux_complexite |
| created_at | TIMESTAMP | | Date de création |
| updated_at | TIMESTAMP | | Date de modification |
| deleted_at | TIMESTAMP | NULL | Soft delete |

#### niveaux_complexite
| Champ | Type | Contrainte | Description |
|-------|------|-----------|-------------|
| id | BIGINT PK | Auto-incrément | Identifiant |
| libelle | VARCHAR(100) | NOT NULL | Libellé du niveau |
| coefficient | DECIMAL(5,3) | NOT NULL | Coefficient de complexité |
| description | TEXT | NULL | Description optionnelle |
| created_at | TIMESTAMP | | Date de création |
| updated_at | TIMESTAMP | | Date de modification |
| deleted_at | TIMESTAMP | NULL | Soft delete |

#### parametres_calcul
| Champ | Type | Contrainte | Description |
|-------|------|-----------|-------------|
| id | BIGINT PK | Auto-incrément | Identifiant |
| annee_id | BIGINT FK | NOT NULL | Référence vers annees_academiques |
| heures_par_credit | INT | NOT NULL | Ex: 10h par crédit |
| sequences_par_credit | INT | NOT NULL | Ex: 3 séquences par crédit |
| service_statutaire | INT | NOT NULL | Ex: 192h/an |
| reduction_mise_a_jour | INT | NOT NULL | Pourcentage |
| sauvegarde_auto_delai | INT | NULL | Délai entre sauvegardes auto (heures) |
| sauvegarde_auto_rotation | INT | NULL | Nombre de sauvegardes auto à conserver |
| created_at | TIMESTAMP | | Date de création |
| updated_at | TIMESTAMP | | Date de modification |

#### taux_horaires
| Champ | Type | Contrainte | Description |
|-------|------|-----------|-------------|
| id | BIGINT PK | Auto-incrément | Identifiant |
| id_grade | BIGINT FK | NOT NULL | Référence vers grades |
| id_annee | BIGINT FK | NOT NULL | Référence vers annees_academiques |
| montant | DECIMAL(12,2) | NOT NULL | Montant horaire |
| devise | VARCHAR(10) | NOT NULL | Devise (XOF, EUR, USD) |
| date_application | DATE | NOT NULL | Date d'effet |
| date_fin_application | DATE | NULL | Date de fin (si expiré) |
| created_at | TIMESTAMP | | Date de création |
| updated_at | TIMESTAMP | | Date de modification |
| deleted_at | TIMESTAMP | NULL | Soft delete |

#### etats_paiement
| Champ | Type | Contrainte | Description |
|-------|------|-----------|-------------|
| id | BIGINT PK | Auto-incrément | Identifiant |
| id_enseignant | BIGINT FK | NOT NULL | Référence vers enseignants |
| id_annee | BIGINT FK | NOT NULL | Référence vers annees_academiques |
| periode | VARCHAR(50) | NOT NULL | Période concernée |
| montant_total | DECIMAL(12,2) | NOT NULL | Montant total |
| statut | ENUM | 'en_attente' | en_attente, valide, paye, rejete |
| date_generation | DATE | NOT NULL | Date de génération |
| created_at | TIMESTAMP | | Date de création |
| updated_at | TIMESTAMP | | Date de modification |
| deleted_at | TIMESTAMP | NULL | Soft delete |

#### ressources_pedagogiques
| Champ | Type | Contrainte | Description |
|-------|------|-----------|-------------|
| id | BIGINT PK | Auto-incrément | Identifiant |
| titre | VARCHAR(200) | NOT NULL | Titre de la ressource |
| description | TEXT | NULL | Description |
| fichier | VARCHAR(255) | NULL | Chemin du fichier |
| id_type | BIGINT FK | NOT NULL | Référence vers types_ressources |
| created_at | TIMESTAMP | | Date de création |
| updated_at | TIMESTAMP | | Date de modification |
| deleted_at | TIMESTAMP | NULL | Soft delete |

#### types_ressources
| Champ | Type | Contrainte | Description |
|-------|------|-----------|-------------|
| id | BIGINT PK | Auto-incrément | Identifiant |
| libelle | VARCHAR(100) | NOT NULL | Libellé du type |
| created_at | TIMESTAMP | | Date de création |
| updated_at | TIMESTAMP | | Date de modification |
| deleted_at | TIMESTAMP | NULL | Soft delete |

#### journaux_activites
| Champ | Type | Contrainte | Description |
|-------|------|-----------|-------------|
| id | BIGINT PK | Auto-incrément | Identifiant |
| utilisateur_id | BIGINT FK | NULL | Référence vers users |
| action | VARCHAR(50) | NOT NULL | Type d'action |
| description | TEXT | NOT NULL | Description |
| model_type | VARCHAR(255) | NULL | Type du modèle concerné |
| model_id | BIGINT | NULL | ID du modèle concerné |
| ip_address | VARCHAR(45) | NULL | Adresse IP |
| user_agent | TEXT | NULL | User-Agent |
| created_at | TIMESTAMP | | Date de l'action |
| updated_at | TIMESTAMP | | Date de modification |

### 5.3 Relations clés

| Relation | Type | Description |
|----------|------|-------------|
| Utilisateur ↔ Role | N:1 | Un utilisateur a un rôle |
| Utilisateur → Enseignant | 1:1 | Un utilisateur peut être un enseignant |
| Enseignant ↔ Grade | N:1 | Un enseignant a un grade |
| Enseignant ↔ Département | N:1 | Un enseignant appartient à un département |
| AffectationCours ↔ Enseignant | N:1 | Une affectation concerne un enseignant |
| AffectationCours ↔ Cours | N:1 | Une affectation concerne un cours |
| AffectationCours → AnneeAcademique | N:1 | Une affectation est sur une année |
| AffectationCours → ActivitePedagogique | 1:N | Une affectation a plusieurs activités |
| ActivitePedagogique ↔ NiveauComplexite | N:1 | Une activité a un niveau de complexité |
| ParametreCalcul ↔ AnneeAcademique | 1:1 | Paramètres liés à une année |
| TauxHoraire ↔ Grade | N:1 | Un taux est lié à un grade |
| TauxHoraire ↔ AnneeAcademique | N:1 | Un taux est lié à une année |
| Filiere ↔ Cours (filiere_cours) | N:N | Relation pivot avec niveau et semestre |

---

## 6. INSTALLATION ET CONFIGURATION

### 6.1 Prérequis

- **PHP** 8.1 ou supérieur
- **Composer** (gestionnaire de dépendances PHP)
- **MySQL** 8.0 ou supérieur
- **Node.js** et **NPM** (optionnel, pour les assets)

### 6.2 Installation

```bash
# 1. Cloner le dépôt
git clone https://github.com/Audre1/gestion-des-heures-uvci.git
cd uvci-gestion-heures

# 2. Installer les dépendances PHP
composer install

# 3. Copier et configurer le fichier d'environnement
cp .env.example .env

# 4. Générer la clé d'application
php artisan key:generate

# 5. Configurer la base de données dans .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=uvci_ptc
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Créer la base de données MySQL
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS uvci_ptc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 7. Exécuter les migrations et les seeders
php artisan migrate
php artisan db:seed

# 8. (Optionnel) Installer et compiler les assets
npm install
npm run build

# 9. Lancer le serveur de développement
php artisan serve
```

### 6.3 Variables d'environnement

| Variable | Description | Valeur par défaut |
|----------|-------------|-------------------|
| `APP_NAME` | Nom de l'application | UVCI Gestion Heures |
| `APP_ENV` | Environnement | local, production |
| `APP_DEBUG` | Mode debug | true (désactiver en prod) |
| `APP_URL` | URL de base | http://localhost |
| `DB_CONNECTION` | Type de base de données | mysql |
| `DB_HOST` | Hôte MySQL | 127.0.0.1 |
| `DB_PORT` | Port MySQL | 3306 |
| `DB_DATABASE` | Nom de la base | uvci_ptc |
| `DB_USERNAME` | Utilisateur MySQL | root |
| `DB_PASSWORD` | Mot de passe MySQL | |
| `MAIL_MAILER` | Service mail | smtp, log |
| `MAIL_HOST` | Hôte SMTP | smtp.gmail.com |
| `MAIL_PORT` | Port SMTP | 587 |
| `MAIL_USERNAME` | Utilisateur SMTP | |
| `MAIL_PASSWORD` | Mot de passe SMTP | |
| `MAIL_ENCRYPTION` | Chiffrement | tls |
| `MAIL_FROM_ADDRESS` | Expéditeur | noreply@uvci.ci |

### 6.4 Seeders

Les seeders créent des données de démonstration dans cet ordre :

1. `RoleSeeder` : 3 rôles (admin, secretaire, enseignant)
2. `AdminSeeder` : 3 utilisateurs de démonstration
3. `GradeSeeder` : 4 grades (Assistant, Maître-Assistant, Maître de Conférences, Professeur Titulaire)
4. `DepartementSeeder` : 4 départements (Informatique, Mathématiques, Physique, Lettres)
5. `EnseignantSeeder` : Enseignants associés aux utilisateurs
6. `AnneeAcademiqueSeeder` : Année en cours
7. `NiveauComplexiteSeeder` : Niveaux avec coefficients
8. `ParametreCalculSeeder` : Paramètres de calcul
9. `TauxHoraireSeeder` : Taux par grade
10. `CoursSeeder` : Catalogue de cours
11. `FiliereSeeder` : Filières avec association de cours
12. `AffectationCoursSeeder` : Affectations enseignants-cours
13. `SequencePedagogiqueSeeder` : Séquences
14. `ActivitePedagogiqueSeeder` : Activités de démonstration
15. `RessourcePedagogiqueSeeder` : Ressources
16. `TypeRessourceSeeder` : Types de ressources

---

## 7. MODULES ET FONCTIONNALITÉS

### 7.1 Authentification

**Fichier :** `app/Http/Controllers/AuthController.php`

Le module d'authentification gère :
- **Connexion** : identification par login ou email + mot de passe
- **Vérification du statut** : bloque les comptes inactifs ou suspendus
- **Mot de passe oublié** : envoi d'un code OTP à 6 chiffres par email
- **Réinitialisation** : vérification du code OTP et mise à jour du mot de passe
- **Renvoi du code** : possibilité de demander un nouveau code
- **Déconnexion** : invalidation de la session

**Flux de réinitialisation :**
```
1. POST /mot-de-passe-oublie → génération code OTP + email
2. GET /reinitialisation → formulaire saisie code OTP
3. POST /verifier-code → validation du code
4. GET /nouveau-mot-de-passe → formulaire nouveau mot de passe
5. POST /nouveau-mot-de-passe → mise à jour du mot de passe
```

### 7.2 Tableaux de bord

**Fichier :** `app/Http/Controllers/DashboardController.php`

Le tableau de bord est adapté selon le rôle de l'utilisateur :

- **Admin** : statistiques générales (enseignants, cours, volumes), graphiques (volume par département, répartition des activités), activités récentes, enseignants en dépassement
- **Secrétaire** : statistiques (enseignants, cours, affectations), activités en attente de validation, dernières affectations, actions rapides
- **Enseignant** : statistiques personnelles (cours assignés, volumes), barre de progression du service statutaire, activités récentes, récapitulatif, actions rapides

### 7.3 Administration

**Fichier :** `app/Http/Controllers/AdminController.php`

8 modules accessibles uniquement à l'administrateur :

| Module | Méthodes | Description |
|--------|----------|-------------|
| Utilisateurs | index, store, update, destroy | CRUD avec génération automatique du login |
| Années académiques | index, storeAnnee, updateAnnee, destroyAnnee, activateAnnee | Gestion des périodes |
| Niveaux de complexité | index, storeNiveauComplexite, updateNiveauComplexite, destroyNiveauComplexite | Coefficients de calcul VHT |
| Paramètres de calcul | index, updateParametres | Règles de calcul (heures/crédit, etc.) |
| Taux horaires | index, storeTaux, updateTaux, destroyTaux | Barèmes par grade et année |
| Journaux | index | Traçabilité des actions |
| Sauvegardes | index, createBackup, downloadBackup, restoreBackup, deleteBackup | Gestion des sauvegardes |

**Génération du login :**
```php
$premierPrenom = explode(' ', trim($request->prenom))[0];
$login = strtolower($premierPrenom . '.' . $request->nom);
$login = Str::slug($login, '.');

// Gestion des doublons
$baseLogin = $login;
$suffix = 2;
while (Utilisateur::where('login', $login)->exists()) {
    $login = $baseLogin . $suffix;
    $suffix++;
}
```

### 7.4 Gestion pédagogique

**Fichier :** `app/Http/Controllers/PedagogieController.php` (1475 lignes)

Le plus gros contrôleur de l'application, il gère 12 entités :

| Module | Routes | Description |
|--------|--------|-------------|
| Enseignants | 4 routes | CRUD avec création automatique du compte utilisateur |
| Grades | 3 routes | CRUD |
| Départements | 3 routes | CRUD |
| Filières | 5 routes | CRUD + attach/detach cours |
| Cours | 3 routes | CRUD |
| Affectations | 3 routes | CRUD |
| Séquences | 4 routes | CRUD + reorder |
| Ressources | 3 routes | CRUD |
| Types de ressources | 3 routes | CRUD |
| Activités | 5 routes | CRUD + restore + valider |
| Volumes | 2 routes | index + export |
| Heures complémentaires | 1 route | index |

**Création d'un enseignant avec son compte utilisateur :**
```php
// 1. Créer l'utilisateur
$utilisateur = Utilisateur::create([...]);

// 2. Créer l'enseignant lié
$enseignant = Enseignant::create([
    'matricule' => $request->matricule,
    'id_utilisateur' => $utilisateur->id,
    ...
]);
```

### 7.5 Calcul des volumes horaires

**Fichier :** `app/Models/ParametreCalcul.php`

Le calcul du **VHT (Volume Horaire Théorique)** suit ces formules :

```
ratio = sequences_par_credit / heures_par_credit
nb_sequences = nb_heures × ratio

coeff_creation = coefficient_du_niveau
coeff_maj = coeff_creation × (1 - réduction / 100)

VHT_creation = nb_sequences × coeff_creation
VHT_maj = nb_sequences × coeff_maj
```

**Heures complémentaires :**
```
// Pour un Permanent
heures_complementaires = max(0, VHT_total - service_statutaire)

// Pour un Vacataire
heures_complementaires = VHT_total (tout est complémentaire)
```

### 7.6 Activités pédagogiques

**Fichier :** `app/Models/ActivitePedagogique.php`

Le modèle utilise un **hook Eloquent** (`static::creating()`) pour calculer automatiquement le coefficient, le nombre de séquences et le VHT avant la création de l'activité :

```php
static::creating(function (ActivitePedagogique $activite) {
    $activite->calculerEtRemplir();
});
```

### 7.7 Paiements

**Fichier :** `app/Http/Controllers/PaiementController.php`

Gère le cycle de vie des états de paiement :
```
Génération → En attente → Validé → Payé
                         → Rejeté
```

### 7.8 Rapports

**Fichier :** `app/Services/RapportService.php`

Service regroupant la logique de génération de 6 types de rapports :

1. **Fiche individuelle enseignant** : bilan détaillé par enseignant
2. **État global des heures** : tous les volumes horaires
3. **Statistiques pédagogiques** : répartition par type, niveau, département
4. **Heures complémentaires** : liste avec montants estimés
5. **État de paiement collectif** : synthèse des rémunérations
6. **Charge par département** : volumes par département

### 7.9 Exports

**Fichier :** `app/Services/ExportService.php`

Gère les exports PDF et Excel des tableaux de données avec génération de fichiers temporaires et téléchargement.

### 7.10 Espace Enseignant

**Fichier :** `app/Http/Controllers/EspaceController.php` et `app/Http/Controllers/DocumentController.php`

L'espace enseignant est en **lecture seule** et permet :
- Consultation des activités (avec filtres)
- Consultation du volume horaire
- Consultation des heures complémentaires
- Consultation des ressources
- Téléchargement de documents PDF (récapitulatif, fiche individuelle, état des heures)

### 7.11 Recherche globale

**Fichier :** `app/Http/Controllers/SearchController.php`

Recherche textuelle dans l'ensemble des données accessibles selon le rôle de l'utilisateur connecté.

### 7.12 Profil

**Fichier :** `app/Http/Controllers/CompteController.php`

Permet à tout utilisateur connecté de :
- Consulter ses informations personnelles
- Modifier son profil (nom, prénom, email, téléphone)
- Changer son mot de passe

### 7.13 Journalisation

**Fichier :** `app/Helpers/functions.php`

Fonction globale `logActivite()` utilisée dans toute l'application pour tracer les actions :

```php
function logActivite(string $action, string $description, $model = null): ?JournalActivite
```

Actions tracées : création, modification, suppression, connexion, déconnexion, consultation, sauvegarde, restauration, téléchargement, etc.

**Fichier :** `app/Models/JournalActivite.php`

Les journaux enregistrent pour chaque action :
- L'utilisateur connecté
- Le type d'action
- Une description textuelle
- Le modèle concerné (type et ID)
- L'adresse IP et le User-Agent

---

## 8. SÉCURITÉ

### 8.1 Authentification et sessions

- Utilisation du système d'authentification natif de Laravel (guards, sessions)
- Régénération de l'ID de session après chaque connexion
- Vérification du statut du compte (actif/inactif/suspendu) à chaque connexion
- Hashage des mots de passe avec Bcrypt (via le cast `'mot_de_passe' => 'hashed'`)

### 8.2 Middleware de rôles

| Middleware | Fichier | Rôle requis |
|------------|---------|-------------|
| `auth` | Laravel | Utilisateur connecté |
| `admin` | CheckAdmin.php | admin |
| `secretaire` | CheckSecretaire.php | secretaire |
| `enseignant` | CheckEnseignant.php | enseignant |
| `role:admin,secretaire` | CheckRole.php | admin ou secretaire |

Les middlewares sont appliqués dans `routes/web.php` :

```php
Route::middleware('auth')->group(function () {
    // Routes accessibles à tous les utilisateurs connectés
    
    Route::middleware('admin')->group(function () {
        // Routes admin uniquement
    });
    
    Route::middleware('secretaire')->group(function () {
        // Routes secrétaire (et admin via le middleware role)
    });
});
```

### 8.3 Validation des données

Toutes les données entrantes sont validées via des **FormRequest** dédiés (30 classes dans `app/Http/Requests/`). Chaque classe contient :
- Règles de validation (`rules()`)
- Messages d'erreur personnalisés (`messages()`)
- Autorisation (`authorize()`)

### 8.4 Protection CSRF

Tous les formulaires POST, PUT, PATCH et DELETE incluent un token CSRF via `@csrf` dans les vues Blade.

### 8.5 Soft Deletes

Toutes les tables principales utilisent le trait `SoftDeletes` de Laravel, permettant la suppression logique et la restauration des données.

### 8.6 Contraintes d'intégrité

- **Clés étrangères** : toutes les relations sont définies au niveau base de données avec `restrictOnDelete`
- **Contraintes d'unicité** : email, login, combinaisons (cours + niveau + semestre + année)

---

## 9. SAUVEGARDE ET RESTAURATION

### 9.1 Service de sauvegarde

**Fichier :** `app/Services/BackupService.php`

Le service `BackupService` gère l'ensemble des opérations de sauvegarde :

| Méthode | Description |
|---------|-------------|
| `backup()` | Crée une sauvegarde manuelle complète |
| `autoBackup()` | Crée une sauvegarde automatique avec rotation |
| `getBackups()` | Liste toutes les sauvegardes disponibles |
| `deleteBackup()` | Supprime une sauvegarde |
| `downloadBackup()` | Télécharge un fichier de sauvegarde |
| `restore()` | Restaure la base depuis une sauvegarde |
| `getTotalSize()` | Calcule la taille totale des sauvegardes |
| `getLastBackupDate()` | Date de la dernière sauvegarde |
| `hasRecentAutoBackup()` | Vérifie si une sauvegarde récente existe |

### 9.2 Format des sauvegardes

Les sauvegardes sont des fichiers **SQL** stockés dans `storage/app/backups/` :
- `backup_YYYY-mm-dd_HH-ii-ss.sql` (manuelle)
- `backup_auto_YYYY-mm-dd_HH-ii-ss.sql` (automatique)

### 9.3 Sauvegarde automatique

**Fichier :** `app/Http/Middleware/AutoBackupMiddleware.php`

Un middleware vérifie périodiquement si une sauvegarde automatique doit être créée, selon les paramètres configurés dans `ParametreCalcul` (délai et rotation).

### 9.4 Rotation des sauvegardes

Les sauvegardes automatiques sont soumises à une rotation : seules les N plus récentes sont conservées (paramètre `sauvegarde_auto_rotation`).

---

## 10. DÉPLOIEMENT

### 10.1 Prérequis serveur

- Serveur web **Apache** ou **Nginx**
- **PHP** 8.1+ avec extensions : BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, MySQL
- **MySQL** 8.0+
- **Composer**

### 10.2 Étapes de déploiement

```bash
# 1. Transférer les fichiers sur le serveur
# 2. Installer les dépendances
composer install --no-dev --optimize-autoloader

# 3. Configurer l'environnement
cp .env.example .env
php artisan key:generate
# Configurer les variables DB_*, MAIL_*, APP_ENV=production, APP_DEBUG=false

# 4. Exécuter les migrations et seeders
php artisan migrate --force
php artisan db:seed --force

# 5. Optimiser Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Définir les permissions
chmod -R 775 storage bootstrap/cache
chmod -R 775 storage/app/backups

# 7. Configurer le serveur web
# Pour Nginx : pointer la racine vers public/
```

### 10.3 Configuration Nginx

```nginx
server {
    listen 80;
    server_name uvci-gestion-heures.example.com;
    root /var/www/uvci-gestion-heures/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 11. MAINTENANCE

### 11.1 Commandes artisan utiles

| Commande | Description |
|----------|-------------|
| `php artisan migrate` | Exécute les migrations |
| `php artisan migrate:rollback` | Annule la dernière migration |
| `php artisan db:seed` | Exécute les seeders |
| `php artisan cache:clear` | Vide le cache |
| `php artisan config:clear` | Vide le cache de configuration |
| `php artisan route:clear` | Vide le cache des routes |
| `php artisan view:clear` | Vide le cache des vues |
| `php artisan storage:link` | Crée le lien symbolique storage |

### 11.2 Sauvegardes régulières

Il est recommandé de configurer une tâche CRON pour les sauvegardes automatiques :

```bash
# Exemple de tâche CRON (tous les jours à 2h du matin)
0 2 * * * cd /var/www/uvci-gestion-heures && php artisan backup:run
```

### 11.3 Logs et surveillance

- Les logs sont stockés dans `storage/logs/laravel.log` (canal `stack`)
- Les erreurs sont visibles via le journal d'activités de l'application (interface admin)
- En production, configurer un service de surveillance (ex: Laravel Horizon, Sentry)

---

## 12. JOURNAL DES VERSIONS

### Version 1.0.0 (Juillet 2026)

**Nouveautés :**
- ✅ Authentification complète (connexion, OTP, réinitialisation)
- ✅ Tableaux de bord adaptés par rôle (admin, secretaire, enseignant)
- ✅ Administration : utilisateurs, rôles, années académiques, niveaux de complexité
- ✅ Paramètres de calcul (heures/crédit, séquences, service statutaire)
- ✅ Taux horaires par grade et année académique
- ✅ Gestion pédagogique : enseignants, grades, départements, filières, cours
- ✅ Affectations des enseignants aux cours
- ✅ Séquences pédagogiques
- ✅ Activités pédagogiques avec validation
- ✅ Calcul automatique du VHT
- ✅ Volumes horaires et heures complémentaires
- ✅ États de paiement (génération, validation, paiement)
- ✅ 6 types de rapports et statistiques
- ✅ Espace enseignant (consultation, documents PDF)
- ✅ Profil utilisateur
- ✅ Recherche globale
- ✅ Exports PDF et Excel
- ✅ Sauvegardes et restauration de la base de données
- ✅ Journalisation des activités
- ✅ Interface responsive
- ✅ Documentation utilisateur et technique

---

> **Document mis à jour le :** Juillet 2026
> **Application :** UVCI — Gestion des Heures des Enseignants
> **Version :** 1.0.0
>
> Pour toute question technique, contacter l'équipe de développement.