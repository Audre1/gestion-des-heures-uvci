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

## 📖 Documentations

### Manuel d'utilisation
Un guide complet est disponible dans le fichier [`docs/MANUEL_UTILISATION.md`](docs/MANUEL_UTILISATION.md) couvrant l'ensemble des fonctionnalités pour les trois rôles :
- **Administrateur** : gestion des comptes, paramétrage, sauvegardes
- **Secrétaire** : gestion pédagogique, activités, paiements, rapports
- **Enseignant** : espace personnel, consultation, documents

### Documentation technique
La documentation technique détaillée est disponible dans le fichier [`docs/DOCUMENTATION_TECHNIQUE.md`](docs/DOCUMENTATION_TECHNIQUE.md) couvrant :
- Architecture MVC et flux de navigation
- Technologies utilisées et structure des dossiers
- Modèle conceptuel des données (16 tables) avec toutes les relations
- Modules et fonctionnalités avec extraits de code
- Sécurité (middleware, validation, CSRF, soft deletes)
- Sauvegarde et restauration
- Déploiement (Nginx, optimisation)
- Maintenance et journal des versions

## Base de données

L'application utilise **MySQL** comme système de gestion de base de données. Les fichiers de migration et de seed sont déjà en place.

### Configuration

1. Créez une base de données MySQL nommée `uvci_ptc` (ou un autre nom de votre choix).
2. Configurez vos identifiants de connexion dans le fichier `.env` :
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=uvci_ptc
   DB_USERNAME=root
   DB_PASSWORD=
   ```

## Email

L'application utilise l'envoi d'emails pour la réinitialisation du mot de passe (envoi du code OTP à 6 chiffres).

### Configuration

Dans le fichier `.env`, configurez le service d'envoi d'emails selon votre environnement :

**En développement (logs)** :
```
MAIL_MAILER=log
```
Les emails seront écrits dans le fichier de log au lieu d'être réellement envoyés.

**Avec un service SMTP** (ex : Gmail, Mailtrap, etc.) :
```
  MAIL_MAILER=smtp
  MAIL_HOST=smtp.gmail.com
  MAIL_PORT=587
  MAIL_USERNAME=votre.email@gmail.com
  MAIL_PASSWORD=votre-mot-de-passe-d-application
  MAIL_ENCRYPTION=tls
  MAIL_FROM_ADDRESS="noreply@uvci.ci"
  MAIL_FROM_NAME="${APP_NAME}"
```

> 💡 Pour Gmail, utilisez un **mot de passe d'application** (Application Password) plutôt que votre mot de passe personnel.
> Pour tester en développement, [Mailtrap](https://mailtrap.io) est une excellente alternative.

## Démarrage

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

Puis ouvrir http://127.0.0.1:8000.

> **Comptes de démonstration** (créés par les seeders) :
> - **Admin** : `admin@uvci.ci` / `admin123`
> - **Secrétaire** : `secretaire@uvci.ci` / `secretaire123`
> - **Enseignant** : `enseignant@uvci.ci` / `enseignant123`
>
> Les seeders créent également des données de démonstration : enseignants, grades, départements, cours, affectations, activités, etc.

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