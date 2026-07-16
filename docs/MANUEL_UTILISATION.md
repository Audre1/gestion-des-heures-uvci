# MANUEL D'UTILISATION — SYSTÈME DE GESTION DES HEURES UVCI
## Université Virtuelle de Côte d'Ivoire (UVCI)

________________________________________

## 1. INTRODUCTION

Ce manuel d'utilisation décrit les fonctionnalités de la plateforme de gestion des heures d'enseignement de l'Université Virtuelle de Côte d'Ivoire (UVCI). Il a pour objectif de guider les utilisateurs (Administrateurs, Secrétaires et Enseignants) dans la prise en main des différents modules de l'application.

La plateforme permet :
- La gestion des comptes utilisateurs et des rôles
- La gestion pédagogique (enseignants, cours, filières, départements, etc.)
- L'enregistrement et le suivi des activités pédagogiques
- Le calcul des volumes horaires et des heures complémentaires
- La génération d'états de paiement
- La production de rapports et statistiques
- La consultation de l'espace personnel pour les enseignants

________________________________________

## 2. PARTIE COMMUNE AUX 3 RÔLES UTILISATEURS

Cette section décrit les fonctionnalités accessibles à tous les utilisateurs connectés, quel que soit leur rôle (Administrateur, Secrétaire ou Enseignant).

### 2.1 Authentification

#### 2.1.1 Connexion
**Menu :** Page d'accueil `/connexion`

1. Accédez à l'URL de l'application.
2. Saisissez votre **identifiant** (login ou email) dans le champ prévu.
3. Saisissez votre **mot de passe**.
4. Cochez l'option **"Se souvenir de moi"** si vous souhaitez rester connecté.
5. Cliquez sur le bouton **"Se connecter"**.

**Contrainte :** Si les identifiants sont incorrects, un message d'erreur s'affiche. Vous devez fournir un login et un mot de passe valides.

#### 2.1.2 Mot de passe oublié
**Menu :** Lien "Mot de passe oublié ?" sur la page de connexion

1. Saisissez votre **adresse email**.
2. Cliquez sur **"Envoyer le code"**.
3. Un code de réinitialisation à 6 chiffres vous est envoyé par email.

**Contrainte :** L'email doit correspondre à un compte existant dans le système.

#### 2.1.3 Réinitialisation du mot de passe
**Menu :** Après réception du code

1. Saisissez le **code à 6 chiffres** reçu par email.
2. Cliquez sur **"Vérifier le code"**.
3. Si le code est correct, vous êtes redirigé vers la page de saisie du nouveau mot de passe.
4. Saisissez un **nouveau mot de passe** et **confirmez-le**.
5. Cliquez sur **"Réinitialiser le mot de passe"**.

**Contraintes :**
- Le code OTP expire après un certain délai.
- Vous pouvez demander un **renvoi du code** si nécessaire.
- Le nouveau mot de passe doit respecter les règles de sécurité (longueur minimale, etc.).

#### 2.1.4 Déconnexion
Cliquez sur le lien **"Déconnexion"** dans la sidebar ou utilisez le bouton de déconnexion dans la topbar. Une confirmation vous sera demandée avant la déconnexion effective.

### 2.2 Tableau de bord

**Menu :** Tableau de bord (lien d'accueil)

Le tableau de bord affiche un résumé de l'activité et s'adapte selon le rôle de l'utilisateur connecté.

#### 2.2.1 Éléments communs
- **Année académique active** : affichée dans le sous-titre de la page
- **Cartes statistiques** : varient selon le rôle (voir sections spécifiques)

### 2.3 Mon Profil

**Menu :** Compte > Mon profil

#### 2.3.1 Consultation du profil
Cette page affiche les informations personnelles de l'utilisateur connecté :
- Nom, prénom
- Email
- Login
- Rôle
- Statut du compte

#### 2.3.2 Modification du profil
1. Cliquez sur **"Modifier le profil"**.
2. Modifiez les champs souhaités (nom, prénom, email, téléphone).
3. Cliquez sur **"Enregistrer"**.

#### 2.3.3 Changement de mot de passe
Dans la même page :
1. Saisissez l'**ancien mot de passe**.
2. Saisissez le **nouveau mot de passe**.
3. Confirmez le nouveau mot de passe.
4. Cliquez sur **"Changer le mot de passe"**.

### 2.4 Recherche globale

**Menu :** Champ de recherche dans la topbar

1. Saisissez un mot-clé dans la barre de recherche.
2. Les résultats s'affichent en temps réel ou après validation.
3. Cliquez sur un résultat pour accéder à la page correspondante.

**Contrainte :** La recherche s'effectue sur l'ensemble des données accessibles selon votre rôle.

### 2.5 Exports (PDF / Excel)

**Accès :** Disponible via les boutons d'export dans les différentes pages (tableaux de données)

- **Export PDF** : génère un fichier PDF du tableau affiché
- **Export Excel** : génère un fichier Excel du tableau affiché

**Contrainte :** Les exports reprennent les données filtrées ou complètes selon le contexte de la page.

________________________________________

## 3. SECTION ADMINISTRATEUR

Rôle : **Administrateur** — Accès à tous les modules d'administration et de gestion.

### 3.1 Tableau de bord Administrateur

**Menu :** Tableau de bord

#### 3.1.1 Cartes indicatrices

| Carte | Contenu |
|-------|---------|
| Enseignants | Nombre total d'enseignants enregistrés |
| Cours affectés | Nombre de cours avec affectation |
| Volume horaire total | Cumul des heures de cours (VHT) |
| Heures complémentaires | Total des heures au-delà du service statutaire |

#### 3.1.2 Graphiques statistiques
- **Volume horaire par département** : graphique en barres affichant la répartition des heures par département pour l'année en cours.
- **Répartition des activités** : graphique en barres horizontales avec pourcentages par type d'activité (création / mise à jour).

### 3.2 Utilisateurs

**Menu :** Administration > Utilisateurs

#### 3.2.1 Objectif
Gérer les comptes utilisateurs de la plateforme (création, modification, suppression).

#### 3.2.2 Liste des utilisateurs
Tableau listant tous les utilisateurs avec :
- **Utilisateur** : nom, prénom, email
- **Login** : identifiant de connexion (format `prenom.nom`)
- **Rôle** : Administrateur, Secrétaire ou Enseignant
- **Date création** : date de création du compte
- **Créé par** : utilisateur ayant créé le compte
- **Statut** : Actif, Inactif ou Suspendu
- **Actions** : Modifier, Supprimer

**Filtres disponibles :**
- Par rôle
- Par statut du compte

#### 3.2.3 Créer un utilisateur
1. Cliquez sur **"Nouvel utilisateur"**.
2. Remplissez les informations obligatoires :
   - Nom, Prénom
   - Email
   - Mot de passe + confirmation
   - Rôle
   - Statut (Actif par défaut)
3. Champs optionnels : Téléphone.
4. Cliquez sur **"Enregistrer l'utilisateur"**.

**Contrainte :**
- Le login est généré automatiquement au format `prenom.nom`.
- En cas de doublon, un numéro est ajouté (ex: `jean.dupont2`).
- L'email doit être unique dans le système.

#### 3.2.4 Modifier un utilisateur
1. Cliquez sur l'icône **crayon vert** (Modifier).
2. Modifiez les champs souhaités.
3. Laissez les champs mot de passe vides si vous ne souhaitez pas le modifier.
4. Cliquez sur **"Enregistrer les modifications"**.

#### 3.2.5 Supprimer un utilisateur
1. Cliquez sur l'icône **poubelle rouge** (Supprimer).
2. Confirmez la suppression dans la fenêtre modale.
3. **Attention** : cette action est irréversible.

### 3.3 Rôles

**Menu :** Administration > Rôles

#### 3.3.1 Objectif
Visualiser les profils d'accès existants dans le système.

#### 3.3.2 Profils disponibles
- **Administrateur** : gestion technique, paramétrage, comptes et supervision
- **Secrétaire Principal** : gestion des enseignants, activités, états et paiements
- **Enseignant** : consultation de l'espace personnel (lecture seule)

**Note :** Chaque fiche rôle affiche le nombre d'utilisateurs associés et propose des actions de modification et de gestion des permissions.

### 3.4 Années académiques

**Menu :** Administration > Années académiques

#### 3.4.1 Objectif
Définir les périodes et calendriers académiques.

#### 3.4.2 Liste des années
Tableau avec :
- **Libellé** (ex : 2026-2027)
- **Date début** et **Date fin**
- **Statut** : En cours, À venir, Clôturée
- **Actions** : Activer, Modifier, Supprimer

#### 3.4.3 Créer une année académique
1. Cliquez sur **"Nouvelle année"**.
2. Saisissez le libellé, la date de début et la date de fin.
3. Cliquez sur **"Enregistrer l'année"**.

**Contrainte :**
- Une seule année peut être active à la fois.
- La date de fin doit être postérieure à la date de début.

#### 3.4.4 Activer une année
1. Cliquez sur l'icône **power-off** (Activer).
2. Confirmez l'activation. L'année précédemment active sera automatiquement désactivée.

### 3.5 Niveaux de complexité

**Menu :** Administration > Niveaux de complexité

#### 3.5.1 Objectif
Définir les niveaux de complexité qui servent au calcul des coefficients de volume horaire (VHT).

#### 3.5.2 Liste des niveaux
Affichage sous forme de cartes avec :
- **Libellé** (ex : Niveau 1, Niveau 2)
- **Coefficient** (ex : 0,40)
- **Description**
- **Nombre d'activités associées**

#### 3.5.3 Créer un niveau de complexité
1. Cliquez sur **"Nouveau niveau"**.
2. Saisissez le **libellé**, le **coefficient** et une **description** (optionnelle).
3. Cliquez sur **"Enregistrer"**.

#### 3.5.4 Modifier / Supprimer
- **Modifier** : cliquez sur le bouton Modifier dans la carte
- **Supprimer** : impossible si le niveau est associé à des activités pédagogiques

### 3.6 Paramètres de calcul

**Menu :** Administration > Paramètres de calcul

#### 3.6.1 Objectif
Configurer les règles de calcul des volumes horaires.

#### 3.6.2 Grille des coefficients
Affiche un tableau avec les valeurs VHT calculées pour chaque combinaison :
- **Type** : Création / Mise à jour
- **Niveau de complexité**
- **Nombre d'heures types** : 10h, 20h, 30h (avec conversion en séquences)

#### 3.6.3 Règles générales
Paramètres modifiables :
- **Heures par crédit** (ex: 10h)
- **Séquences par crédit** (ex: 3 séquences)
- **Service statutaire** (nombre d'heures/an pour un permanent)
- **Réduction mise à jour** (pourcentage appliqué au coefficient de mise à jour)

**Calcul :** Le coefficient de mise à jour est calculé automatiquement : `coeff_maj = coeff_creation × (1 - réduction/100)`

### 3.7 Taux horaires

**Menu :** Administration > Taux horaires

#### 3.7.1 Objectif
Définir les barèmes de rémunération par grade et par année académique.

#### 3.7.2 Liste des taux
Tableau avec :
- **Grade**
- **Montant** horaire
- **Devise** (XOF, FCFA, EUR, USD)
- **Année** académique concernée
- **Date d'application**
- **Statut** : Actif ou Expiré (selon présence d'une date de fin)

**Filtres :** Par grade, par statut (Actif/Expiré)

#### 3.7.3 Créer un taux horaire
1. Cliquez sur **"Nouveau taux"**.
2. Sélectionnez le **grade**, l'**année académique**, le **montant**, la **devise**.
3. Définissez la **date d'application**.
4. Optionnel : date de fin si le taux a une durée limitée.
5. Cliquez sur **"Enregistrer le taux"**.

**Contrainte :** Un seul taux doit être défini pour un même grade et une même année académique.

### 3.8 Journaux d'activités

**Menu :** Administration > Journaux d'activités

#### 3.8.1 Objectif
Consulter la traçabilité des actions réalisées dans le système.

#### 3.8.2 Liste des journaux
Tableau avec :
- **Date/Heure** de l'action
- **Utilisateur** ayant effectué l'action
- **Action** : création, modification, suppression, connexion, déconnexion
- **Description** détaillée de l'action
- **Adresse IP**

**Filtre :** Par type d'action

**Contrainte :** Les journaux sont en lecture seule et ne peuvent pas être modifiés ni supprimés.

### 3.9 Sauvegardes

**Menu :** Administration > Sauvegardes

#### 3.9.1 Objectif
Sauvegarde et restauration des données du système.

#### 3.9.2 Indicateurs
- **Dernière sauvegarde** : date de la dernière sauvegarde effectuée
- **Taille des données** : volume total des sauvegardes
- **Fréquence** : Manuelle (actuellement)

#### 3.9.3 Lancer une sauvegarde
1. Cliquez sur **"Lancer une sauvegarde"**.
2. Le système crée une archive des données.

#### 3.9.4 Gestion des sauvegardes
Pour chaque sauvegarde :
- **Télécharger** : télécharge le fichier de sauvegarde
- **Restaurer** : remplace les données actuelles par la sauvegarde (action irréversible)
- **Supprimer** : efface définitivement la sauvegarde

**Contrainte :** La restauration remplace **toutes** les données actuelles — procédez avec prudence.

________________________________________

## 4. SECTION SECRÉTAIRE

Rôle : **Secrétaire** — Accès à la gestion pédagogique et aux volumes/paiements.

### 4.1 Tableau de bord Secrétaire

**Menu :** Tableau de bord

#### 4.1.1 Cartes indicatrices

| Carte | Contenu |
|-------|---------|
| Enseignants | Nombre total d'enseignants |
| Cours actifs | Nombre de cours en activité |
| Affectations | Nombre d'affectations d'enseignants à des cours |
| Activités en attente | Nombre d'activités en attente de validation |

#### 4.1.2 Activités en attente de validation
Tableau listant les dernières activités soumises par les enseignants, en attente de validation par le secrétaire.

#### 4.1.3 Dernières affectations
Tableau des affectations récentes (enseignant, cours, niveau, semestre).

#### 4.1.4 Actions rapides
Boutons d'accès direct vers :
- Gérer les enseignants
- Gérer les affectations
- Gérer les cours
- Valider des activités
- Volumes horaires

---

### 4.2 Grades

**Menu :** Gestion pédagogique > Grades

#### 4.2.1 Objectif
Gérer les grades des enseignants (ex : Maître de Conférences, Professeur Titulaire, etc.).

#### 4.2.2 Opérations possibles
- **Créer** : ajouter un nouveau grade avec un libellé
- **Modifier** : mettre à jour le libellé du grade
- **Supprimer** : retirer un grade (sous réserve qu'aucun enseignant ne lui soit associé)

---

### 4.3 Départements

**Menu :** Gestion pédagogique > Départements

#### 4.3.1 Objectif
Gérer les départements de l'université.

#### 4.3.2 Opérations possibles
- **Créer** : ajouter un département avec son nom
- **Modifier** : modifier le nom du département
- **Supprimer** : retirer un département

---

### 4.4 Enseignants

**Menu :** Gestion pédagogique > Enseignants

#### 4.4.1 Objectif
Gérer les informations personnelles et professionnelles des enseignants.

#### 4.4.2 Liste des enseignants
Tableau avec :
- **Enseignant** : nom, prénom
- **Matricule**
- **Département**
- **Grade**
- **Statut** : Permanent ou Vacataire
- **Taux horaire perso** : taux personnalisé (optionnel)
- **Téléphone**
- **Créé par**

**Filtres :** Par département, par grade

#### 4.4.3 Ajouter un enseignant
1. Cliquez sur **"Ajouter un enseignant"**.
2. Remplissez les **informations utilisateur** (nom, prénom, email, téléphone, mot de passe).
3. Remplissez les **informations professionnelles** :
   - Matricule
   - Département
   - Grade
   - Statut (Permanent/Vacataire)
   - Taux horaire perso (optionnel)
   - Date de recrutement
4. Cliquez sur **"Enregistrer l'enseignant"**.

**Contrainte :** Un compte utilisateur est automatiquement créé avec un login au format `prenom.nom`.

#### 4.4.4 Modifier un enseignant
1. Cliquez sur l'icône **crayon vert**.
2. Modifiez les champs souhaités.
3. Cliquez sur **"Enregistrer les modifications"**.

#### 4.4.5 Supprimer un enseignant
1. Cliquez sur l'icône **poubelle rouge**.
2. **Contrainte :** La suppression est impossible si l'enseignant a des affectations de cours ou des états de paiement associés.

---

### 4.5 Filières

**Menu :** Gestion pédagogique > Filières

#### 4.5.1 Objectif
Gérer les filières de formation de l'université.

#### 4.5.2 Opérations possibles
- **Créer** : ajouter une filière
- **Modifier** : modifier les informations de la filière
- **Associer des cours** : attacher un cours à une filière avec un niveau et un semestre spécifiques
- **Détacher un cours** : retirer un cours d'une filière
- **Supprimer** : retirer une filière

**Contrainte :** Un même cours peut être associé à plusieurs filières avec des niveaux et semestres différents.

---

### 4.6 Cours

**Menu :** Gestion pédagogique > Cours

#### 4.6.1 Objectif
Gérer le catalogue des cours.

#### 4.6.2 Opérations possibles
- **Créer** : ajouter un cours (code, intitulé, nombre de crédits, volume horaire)
- **Modifier** : mettre à jour les informations du cours
- **Supprimer** : retirer un cours

---

### 4.7 Affectations

**Menu :** Gestion pédagogique > Affectations

#### 4.7.1 Objectif
Affecter un enseignant à un cours spécifique, pour un niveau, un semestre et une année académique donnés.

#### 4.7.2 Liste des affectations
Tableau avec :
- Enseignant
- Cours
- Niveau
- Semestre
- Année académique
- Actions

#### 4.7.3 Créer une affectation
1. Cliquez sur **"Nouvelle affectation"**.
2. Sélectionnez l'**enseignant**, le **cours**, le **niveau**, le **semestre** et l'**année académique**.
3. Cliquez sur **"Enregistrer"**.

**Contrainte :** Un enseignant ne peut avoir qu'une seule affectation par cours, niveau et semestre pour une année donnée.

---

### 4.8 Séquences pédagogiques

**Menu :** Gestion pédagogique > Séquences pédagogiques

#### 4.8.1 Objectif
Définir les séquences (groupes de cours) pour chaque affectation.

#### 4.8.2 Opérations possibles
- **Créer** : ajouter une séquence pour une affectation donnée
- **Modifier** : modifier les paramètres de la séquence
- **Réordonner** : modifier l'ordre d'affichage des séquences
- **Supprimer** : retirer une séquence

**Note :** Les séquences permettent de découper un cours en plusieurs groupes (TD, TP, etc.).

---

### 4.9 Ressources pédagogiques

**Menu :** Gestion pédagogique > Ressources pédagogiques

#### 4.9.1 Objectif
Gérer les ressources pédagogiques (documents, supports de cours).

#### 4.9.2 Opérations possibles
- **Créer** : ajouter une ressource
- **Modifier** : mettre à jour la ressource
- **Supprimer** : retirer une ressource

---

### 4.10 Types de ressources

**Menu :** Gestion pédagogique > Types de ressources

#### 4.10.1 Objectif
Gérer les catégories de ressources pédagogiques.

#### 4.10.2 Opérations possibles
- **Créer** : ajouter un nouveau type
- **Modifier** : modifier le libellé
- **Supprimer** : retirer un type

---

### 4.11 Activités pédagogiques

**Menu :** Activités pédagogiques > Activités

#### 4.11.1 Objectif
Enregistrement et validation des activités des enseignants.

#### 4.11.2 Liste des activités
Tableau avec :
- **Enseignant**
- **Cours**
- **Type** : Création ou Mise à jour
- **Niveau** de complexité
- **Séquences** effectuées
- **Coefficient** appliqué
- **VHT** (Volume Horaire Théorique) calculé
- **Date** de réalisation
- **Statut** : En cours, Validée, Rejetée
- **Actions**

**Filtres :** Par type, par niveau, par statut

#### 4.11.3 Créer une activité
1. Cliquez sur **"Nouvelle activité"**.
2. Sélectionnez l'**affectation** (enseignant-cours), le **type** (création/mise à jour), le **niveau de complexité**.
3. Le système calcule automatiquement le VHT selon la formule :
   - `VHT = coefficient_niveau × nombre_séquences` (pour une création)
   - `VHT = (coefficient_niveau × (1 - réduction/100)) × nombre_séquences` (pour une mise à jour)
4. Cliquez sur **"Enregistrer"**.

#### 4.11.4 Valider une activité
1. Depuis la ligne de l'activité, cliquez sur l'icône de validation.
2. L'activité passe au statut **"Validée"**.

#### 4.11.5 Restaurer une activité
Une activité supprimée peut être restaurée via l'action dédiée.

---

### 4.12 Volumes horaires

**Menu :** Volumes & Paiements > Volumes horaires

#### 4.12.1 Objectif
Consulter les volumes horaires consolidés par enseignant.

#### 4.12.2 Tableau des volumes
Affiche pour chaque enseignant :
- Volume horaire réalisé
- Service statutaire
- Heures complémentaires
- Taux de réalisation

#### 4.12.3 Export
Possibilité d'exporter les volumes horaires au format PDF/Excel.

---

### 4.13 Heures complémentaires

**Menu :** Volumes & Paiements > Heures complémentaires

#### 4.13.1 Objectif
Consulter les heures effectuées au-delà du service statutaire pour chaque enseignant.

#### 4.13.2 Contenu
Tableau listant les enseignants avec leurs heures complémentaires, calculées automatiquement à partir des activités validées.

---

### 4.14 États de paiement

**Menu :** Volumes & Paiements > États de paiement

#### 4.14.1 Objectif
Génération et suivi des états de paiement des enseignants.

#### 4.14.2 Indicateurs
- Nombre d'états **en attente**
- Nombre d'états **validés**
- Nombre d'états **payés**
- Nombre d'états **rejetés**

#### 4.14.3 Générer un état de paiement
1. Cliquez sur **"Générer un état"**.
2. Sélectionnez l'année académique et les paramètres souhaités.
3. Cliquez sur **"Générer"**.

#### 4.14.4 Gestion des états
Pour chaque état de paiement, le secrétaire peut :
- **Valider** l'état
- **Marquer comme payé**
- **Rejeter** l'état
- **Supprimer** l'état

**Filtres :** Par année académique, par statut

---

### 4.15 Rapports & Statistiques

**Menu :** Volumes & Paiements > Rapports & Statistiques

#### 4.15.1 Objectif
Générer des états récapitulatifs et exports.

#### 4.15.2 Types de rapports disponibles

| Rapport | Description |
|---------|-------------|
| **Fiche individuelle enseignant** | Bilan détaillé des activités et heures d'un enseignant |
| **État global des heures** | Récapitulatif de tous les volumes horaires de l'université |
| **Statistiques pédagogiques** | Répartition des activités par type, niveau et département |
| **État des heures complémentaires** | Liste des heures effectuées au-delà du service statutaire |
| **État de paiement collectif** | Synthèse des rémunérations dues par période |
| **Charge par département** | Volume horaire consolidé par département et filière |

#### 4.15.3 Générer un rapport
1. Cliquez sur **"Générer"** pour le rapport souhaité.
2. Sélectionnez les paramètres de génération (année, département, enseignant, etc.).
3. Le rapport est affiché et peut être exporté en PDF.

________________________________________

## 5. SECTION ENSEIGNANT

Rôle : **Enseignant** — Accès à l'espace personnel (consultation uniquement).

### 5.1 Tableau de bord Enseignant

**Menu :** Tableau de bord

#### 5.1.1 Cartes indicatrices personnelles

| Carte | Contenu |
|-------|---------|
| Cours assignés | Nombre de cours qui vous sont affectés |
| Volume réalisé | Total des heures de cours effectuées |
| Volume total | Volume horaire total prévu |
| Heures compl. | Heures complémentaires accumulées |

#### 5.1.2 Barre de progression
Affiche graphiquement votre progression par rapport au **service statutaire** (en heures/an).
- Pourcentage de réalisation
- Heures réalisées vs heures prévues

#### 5.1.3 Mes dernières activités
Tableau des dernières activités enregistrées :
- Date
- Cours
- Type (création/mise à jour)
- Volume (VHT)
- Statut (en cours, validée, rejetée)

#### 5.1.4 Récapitulatif
- Activités validées
- Activités en cours
- Activités rejetées
- Heures complémentaires
- Service statutaire

#### 5.1.5 Actions rapides
- Ajouter une activité
- Voir mon volume horaire
- Heures complémentaires
- Mes ressources
- Mes documents

---

### 5.2 Mes activités

**Menu :** Espace Enseignant > Mes activités

#### 5.2.1 Objectif
Consulter l'historique de vos activités pédagogiques.

#### 5.2.2 Affichage
Tableau des activités avec :
- **Cours**
- **Type** (Création / Mise à jour)
- **Niveau** d'affectation
- **Semestre**
- **Niveau** de complexité
- **Séquences** réalisées
- **VHT** calculé
- **Année** académique
- **Date** de réalisation
- **Statut** (en cours, validée, rejetée)
- **Détail** : informations complémentaires

**Filtres :** Par type, par niveau

**Contrainte :** L'espace enseignant est en **lecture seule** — vous ne pouvez pas créer, modifier ou supprimer des activités depuis cette interface.

---

### 5.3 Mon volume horaire

**Menu :** Espace Enseignant > Mon volume horaire

#### 5.3.1 Objectif
Consulter le détail de votre volume horaire.

#### 5.3.2 Contenu
- Volume horaire par cours
- Cumul par type d'activité
- Comparaison avec le service statutaire
- Détail des heures complémentaires

---

### 5.4 Mes heures complémentaires

**Menu :** Espace Enseignant > Mes heures compl.

#### 5.4.1 Objectif
Consulter vos heures complémentaires (au-delà du service statutaire).

#### 5.4.2 Affichage
Tableau listant le détail des heures complémentaires par activité.

---

### 5.5 Mes ressources

**Menu :** Espace Enseignant > Mes ressources

#### 5.5.1 Objectif
Consulter les ressources pédagogiques qui vous sont associées.

#### 5.5.2 Contenu
Liste des ressources avec possibilité de consultation.

---

### 5.6 Mes documents

**Menu :** Espace Enseignant > Mes documents

#### 5.6.1 Objectif
Télécharger vos récapitulatifs et fiches individuelles au format PDF.

#### 5.6.2 Documents disponibles

| Document | Description |
|----------|-------------|
| **Récapitulatif d'activités** | Bilan complet de vos activités pédagogiques |
| **Fiche individuelle** | Vos informations et charge horaire consolidée |
| **État des heures** | Détail de vos volumes horaires et heures complémentaires |

#### 5.6.3 Procédure de téléchargement
1. Sélectionnez l'**année académique** souhaitée dans le menu déroulant.
2. Cliquez sur **"Télécharger (PDF)"** pour le document désiré.
3. Le fichier PDF est généré et téléchargé automatiquement.

**Contrainte :** Les documents sont générés en fonction de vos données et de l'année sélectionnée.

________________________________________

## 6. ANNEXES

### 6.1 Format des exports PDF disponibles
- Fiche individuelle enseignant
- État global des heures
- Statistiques pédagogiques
- Heures complémentaires
- État de paiement collectif
- Charge par département

### 6.2 Codes couleurs utilisés dans l'interface

| Élément | Code couleur |
|---------|--------------|
| Succès, Validé, Actif | Vert `#00A54E` |
| Primaire, Enseignant | Violet `#91268F` |
| En attente, En cours | Ambre `#D97706` |
| Rejeté, Erreur, Suppression | Rouge `#DC2626` |
| Information, Bleu | Bleu `#2563EB` |

### 6.3 Contraintes générales du système

1. **Authentification** : Seuls les utilisateurs avec un compte actif peuvent se connecter.
2. **Rôles** : Les permissions sont liées au rôle (admin, secretaire, enseignant).
3. **Année académique** : Une seule année peut être active simultanément.
4. **Suppressions** : Impossible de supprimer une ressource référencée par d'autres entités.
5. **Lecture seule (enseignant)** : L'espace enseignant est consultatif — aucune création/modification/suppression.
6. **Traçabilité** : Toutes les actions sont enregistrées dans les journaux d'activités.

### 6.4 Glossaire

| Terme | Définition |
|-------|------------|
| **VHT** | Volume Horaire Théorique : nombre d'heures calculé selon le coefficient de complexité et le nombre de séquences |
| **Service statutaire** | Nombre d'heures annuel qu'un enseignant permanent doit effectuer (ex: 192h) |
| **Heures complémentaires** | Heures effectuées au-delà du service statutaire, ouvrant droit à rémunération |
| **Séquence** | Unité de découpage d'un cours (groupe, TD, TP) |
| **Affectation** | Lien entre un enseignant, un cours, un niveau et un semestre |
| **OTP** | One-Time Password : code à usage unique utilisé pour la réinitialisation du mot de passe |
| **Coefficient de complexité** | Valeur numérique attribuée à un niveau de complexité pour le calcul du VHT |

________________________________________

> **Document mis à jour le :** Juillet 2026  
> **Application :** UVCI — Gestion des Heures des Enseignants  
> **Version :** 1.0.0 (maquette front-end)