# UVCI — Gestion des Heures des Enseignants

Application web pour l'automatisation de la gestion des heures d'enseignement de l'**Université Virtuelle de Côte d'Ivoire (UVCI)**.

## Stack

- **Laravel** (contrôleurs, routes, middleware, Blade)
- **Bootstrap 5** + **Font Awesome 6** (via CDN)
- CSS personnalisé aux couleurs du logo UVCI :
  - Vert `#00A54E`
  - Violet `#91268F`
- **Chart.js** pour les graphiques
- Génération de documents **PDF**

## Fonctionnalités

### Authentification
- Connexion par identifiant / email
- Mot de passe oublié avec envoi de code OTP par email
- Réinitialisation du mot de passe
- Déconnexion

### Rôles utilisateurs

| Rôle | Accès |
|------|-------|
| **Administrateur** | Administration complète + gestion pédagogique |
| **Secrétaire** | Gestion pédagogique, activités, paiements, rapports |
| **Enseignant** | Espace personnel (consultation) |

### Administration (admin uniquement)
- Gestion des **utilisateurs** (CRUD avec rôles et statuts)
- **Années académiques** (création, activation, clôture)
- **Niveaux de complexité** (coefficients de calcul VHT)
- **Paramètres de calcul** (heures/crédit, séquences/crédit, service statutaire, réduction mise à jour)
- **Taux horaires** (barèmes par grade et année académique)
- **Journaux d'activités** (traçabilité des actions)
- **Sauvegardes** (création, téléchargement, restauration, suppression)

### Gestion pédagogique (secretaire)
- **Grades, Départements, Enseignants** (CRUD complet)
- **Filières** (avec association de cours par niveau et semestre)
- **Cours** (catalogue avec crédits et volumes horaires)
- **Affectations** (liaison enseignant → cours → niveau → semestre)
- **Séquences pédagogiques** (découpage des cours en groupes)
- **Ressources et types de ressources**
- **Activités pédagogiques** (enregistrement, validation, restauration)

### Volumes & Paiements (secretaire)
- **Volumes horaires** consolidés par enseignant
- **Heures complémentaires** (au-delà du service statutaire)
- **États de paiement** (génération, validation, paiement, rejet)
- **Rapports & Statistiques** :
  - Fiche individuelle enseignant
  - État global des heures
  - Statistiques pédagogiques
  - Heures complémentaires
  - État de paiement collectif
  - Charge par département

### Espace Enseignant (consultation)
- **Tableau de bord** personnel avec progression et statistiques
- **Mes activités** (historique et statuts)
- **Mon volume horaire** et **mes heures complémentaires**
- **Mes ressources** pédagogiques
- **Mes documents** (téléchargement PDF : récapitulatif, fiche individuelle, état des heures)

### Compte (tous les rôles)
- Consultation et modification du **profil**
- Changement de **mot de passe**

### Fonctionnalités transverses
- **Recherche globale** dans l'application
- **Exports PDF / Excel** des tableaux de données
- Interface **responsive** (sidebar rétractable sur mobile)

## 📖 Manuel d'utilisation

Un guide complet est disponible dans le fichier [`docs/MANUEL_UTILISATION.md`](docs/MANUEL_UTILISATION.md) couvrant l'ensemble des fonctionnalités pour les trois rôles :
- **Administrateur** : gestion des comptes, paramétrage, sauvegardes
- **Secrétaire** : gestion pédagogique, activités, paiements, rapports
- **Enseignant** : espace personnel, consultation, documents

## Démarrage

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

Puis ouvrir http://127.0.0.1:8000.

> **Comptes de démonstration** :
> - Admin : `admin ou admin@uvci.ci` / `admin123`
> - Secrétaire : `secretaire ou secretaire@uvci.ci` / `secretaire123`
> - Enseignant : `enseignant ou enseignant@uvci.ci` / `enseignant123`
>
> *(à adapter selon votre configuration)*

## Structure du projet

```
resources/views/
├── layouts/          # auth.blade.php, app.blade.php
├── partials/         # sidebar.blade.php, topbar.blade.php
├── components/       # app-page, data-table, notifications
├── auth/             # login, forgot-password, reset-password
├── admin/            # utilisateurs, roles, annees, niveaux, parametres, taux, journaux, sauvegardes
├── pedagogie/        # enseignants, grades, departements, filieres, cours, affectations, sequences, ressources, types-ressources, activites, volumes, complementaires
├── paiements/        # index
├── rapports/         # index, fiche-individuelle, etat-global, statistiques, heures-complementaires, paiement-collectif, charge-departement
├── espace/           # activites, volume, complementaires, ressources, documents
├── compte/           # profil
├── exports/          # pdf-template
├── pdf/              # etat-heures, fiche-individuelle, recapitulatif-activites
└── emails/           # reset-password
```

Toutes les routes sont définies dans [`routes/web.php`](routes/web.php).