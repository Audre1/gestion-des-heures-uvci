# UVCI — Gestion des Heures des Enseignants

Application web (maquette **front-end uniquement**) pour l'automatisation de la
gestion des heures d'enseignement de l'**Université Virtuelle de Côte d'Ivoire (UVCI)** dans le cadre du PCT.

> ⚠️ Ce dépôt contient **uniquement les vues et la navigation** (aucune logique
> backend : pas d'authentification réelle ni de traitement).
> Les données affichées sont fictives et servent à illustrer le design.

## Stack

- **Laravel** (routes `Route::view`, composants Blade)
- **Bootstrap 5** + **Font Awesome 6** (via CDN)
- CSS personnalisé aux couleurs du logo UVCI :
  - Vert `#00A54E`
  - Violet `#91268F`

## Écrans réalisés

### Authentification
- Page de **connexion**
- **Mot de passe oublié** (saisie de l'email)
- **Réinitialisation** par **code à 6 chiffres** (champ OTP en digits) + nouveau mot de passe

### Application (layout avec sidebar complète + topbar)
- Tableau de bord (statistiques, graphiques, activités récentes)
- **Administration** : utilisateurs, rôles, années académiques, paramètres de calcul, taux horaires, journaux, sauvegardes
- **Gestion pédagogique** : enseignants, grades, départements, filières, cours, affectations, séquences, ressources, types de ressources, niveaux de complexité, activités
- **Volumes & Paiements** : volumes horaires, heures complémentaires, états de paiement, rapports & statistiques
- **Espace Enseignant** : mes activités, mon volume horaire, mes heures complémentaires, mes ressources, mes documents
- **Compte** : mon profil

Le tout est **responsive** (sidebar rétractable sur mobile).

## Démarrage

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

Puis ouvrir http://127.0.0.1:8000 (page de connexion).
Le bouton « Se connecter » redirige vers le tableau de bord (aucune vérification réelle).

## Structure des vues

```
resources/views/
├── layouts/        # auth.blade.php, app.blade.php
├── partials/       # sidebar.blade.php, topbar.blade.php
├── components/     # app-page, data-table (composants Blade réutilisables)
├── auth/           # login, forgot-password, reset-password
├── admin/          # utilisateurs, roles, annees, parametres, taux, journaux, sauvegardes
├── pedagogie/      # enseignants, grades, departements, filieres, cours, ...
├── paiements/      # index
├── rapports/       # index
├── espace/         # espace enseignant
└── compte/         # profil
```

Toutes les routes sont définies dans [`routes/web.php`](routes/web.php).
