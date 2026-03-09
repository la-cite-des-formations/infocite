# Infocite — Portail Intranet de La Cité des Formations

Portail intranet interactif construit avec **Laravel 9** et **Livewire 2**, destiné à la communication interne, à la gestion documentaire et à l'administration des utilisateurs de [La Cité des Formations](https://www.lacitedesformations.com/) (Tours, France).

---

## Sommaire

- [Fonctionnalités](#fonctionnalités)
- [Stack technique](#stack-technique)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Compilation des assets](#compilation-des-assets)
- [Architecture du projet](#architecture-du-projet)
- [Modèles de données](#modèles-de-données)
- [Système de droits (RBAC)](#système-de-droits-rbac)
- [Tableaux de bord d'administration](#tableaux-de-bord-dadministration)
- [Documentation technique](#documentation-technique)

---

## Fonctionnalités

### Côté Usage (Front-office)

- **Gestion de contenus** : Rédaction, publication, archivage et suppression automatique d'articles organisés par rubriques, avec éditeur WYSIWYG (TinyMCE) et nettoyage sémantique avancé du contenu collé.
- **Page « À la Une »** : Agrégation d'articles avec filtres (favoris, non lus, par rubrique) et tris (les plus consultés, récents, commentés). Possibilité d'épingler jusqu'à 4 articles.
- **Applications** : Catalogue d'applications institutionnelles et personnelles avec gestion des favoris et récupération automatique des favicons.
- **Commentaires** : Système de commentaires modérables sur les articles, avec possibilité de blocage par article.
- **Favoris** : Mise en favoris des articles et des rubriques pour un accès rapide.
- **Organigramme** : Affichage dynamique de l'organigramme de l'établissement via Google Charts.
- **Recherche globale** : Recherche transversale sur les articles et les applications.
- **Notifications push** : Notifications en temps réel via Firebase Cloud Messaging (FCM) lors de la publication ou la modification d'un article ou d'un commentaire.
- **Profil utilisateur** : Gestion des préférences de notification (bureau, favoris uniquement).

### Côté Administration (Back-office)

- **Tableau de bord principal** : Accès centralisé à la gestion des utilisateurs, profils, groupes, applications, rubriques, contenus, commentaires et droits.
- **Organigramme** : Gestion des nœuds graphiques, des mises en forme et des libellés référents.
- **Statistiques** : Visualisation des connexions, des consultations d'articles et de l'usage global (éditeurs, commentateurs, applications personnelles) par année scolaire.

---

## Stack technique

| Couche        | Technologie                                                               |
|---------------|---------------------------------------------------------------------------|
| **Backend**   | PHP 8.1+, Laravel 9, Livewire 2                                          |
| **Frontend**  | Bootstrap 5, jQuery, Material Icons, SCSS                                 |
| **Éditeur**   | TinyMCE 7                                                                |
| **Auth**      | Laravel UI + Google OAuth 2.0 (Socialite)                                 |
| **Notifs**    | Firebase Cloud Messaging (kreait/firebase-php)                            |
| **BDD**       | MySQL                                                                     |
| **Build**     | Laravel Mix 6 (Webpack)                                                   |
| **Sécurité**  | Content Security Policy (spatie/laravel-csp), CORS (fruitcake/laravel-cors) |
| **Serveur**   | Hébergé sur Plesk                                                         |

---

## Prérequis

- **PHP** ≥ 8.1 avec les extensions `intl`, `mbstring`, `xml`, `mysql`
- **Composer** ≥ 2.x
- **Node.js** ≥ 16.x et **npm**
- **MySQL** ≥ 5.7
- Un projet **Firebase** configuré (pour les notifications push)
- Des identifiants **Google OAuth** (pour l'authentification via Google)

---

## Installation

```bash
# 1. Cloner le dépôt
git clone <url-du-depot> infocite
cd infocite

# 2. Installer les dépendances PHP
composer install

# 3. Installer les dépendances Node.js
npm install

# 4. Copier et configurer l'environnement
cp .env.example .env
php artisan key:generate

# 5. Configurer la base de données dans .env, puis :
php artisan migrate --seed

# 6. Créer le lien symbolique pour le stockage public
php artisan storage:link
```

---

## Configuration

Variables d'environnement essentielles à configurer dans le fichier `.env` :

| Variable                | Description                                    |
|-------------------------|------------------------------------------------|
| `APP_NAME`              | Nom de l'application (ex: `Infocite`)          |
| `APP_URL`               | URL de base du portail                         |
| `DB_*`                  | Paramètres de connexion MySQL                  |
| `GOOGLE_CLIENT_ID`      | ID client Google OAuth                         |
| `GOOGLE_CLIENT_SECRET`  | Secret client Google OAuth                     |
| `GOOGLE_REDIRECT_URI`   | URL de callback Google (`/login/google/callback`) |
| `FIREBASE_*`            | Configuration du projet Firebase (clés serveur) |

---

## Compilation des assets

```bash
# Développement (avec source maps)
npm run dev

# Développement avec rechargement automatique
npm run watch

# Production (minifié)
npm run prod
```

Les fichiers compilés sont générés dans `public/js/` et `public/css/`. Le Service Worker Firebase est compilé directement à la racine de `public/`.

---

## Architecture du projet

```
app/
├── Casts/                  # Casts Eloquent personnalisés
├── Channels/               # Canal FCM pour les notifications push
├── Charts/                 # Logique de génération de graphiques (statistiques)
├── Console/Commands/       # Commandes Artisan (purge des tokens FCM)
├── CustomFacades/          # Façade AP (Application Parameters) — paramétrage central
├── Http/
│   ├── Controllers/        # Contrôleurs (Vue, Admin, Dashboard, Auth)
│   └── Livewire/
│       ├── Admin/          # Composants Livewire d'administration (15 managers)
│       ├── Modals/         # Modales (Admin + Usage)
│       ├── Usage/          # Composants Livewire côté usage (10 managers)
│       └── With*.php       # Traits partagés (filtres, favoris, notifications, etc.)
├── Jobs/                   # Jobs asynchrones (purge des tokens FCM invalides)
├── Models/                 # 17 modèles Eloquent
├── Notifications/          # Notification Firebase (AppNotification)
├── Observers/              # Observer sur le modèle Post
├── Policies/               # 7 politiques d'autorisation
├── Providers/              # Service Providers (Auth, Firebase, Custom, etc.)
├── Statistics/             # Classes de calcul de statistiques
└── Support/                # Helpers globaux (normalisation Unicode, etc.)
```

---

## Modèles de données

| Modèle          | Description                                              |
|-----------------|----------------------------------------------------------|
| `User`          | Utilisateur du portail (rôles, groupes, préférences)     |
| `Employee`      | Profil employé associé à un utilisateur                  |
| `Learner`       | Profil apprenant associé à un utilisateur                |
| `Post`          | Article de contenu (publication, archivage, épinglé)     |
| `Rubric`        | Rubrique de navigation (arborescence parent/enfant)      |
| `Comment`       | Commentaire sur un article                               |
| `App`            | Application (institutionnelle ou personnelle)            |
| `Group`         | Groupe d'utilisateurs (classe, équipe, fonction, système)|
| `Right`         | Paramétrage d'un droit d'accès                           |
| `Roles`         | Constantes de niveaux de rôles (Lecteur → Administrateur)|
| `Chartnode`     | Nœud de l'organigramme dynamique                         |
| `Actor`         | Lien hiérarchique dans l'organigramme                    |
| `Format`        | Mise en forme visuelle d'un nœud d'organigramme          |
| `Interaction`   | Suivi des interactions (connexion, vue, commentaire)     |
| `Notification`  | Notification interne (nouvel article, commentaire)       |
| `FcmToken`      | Token Firebase Cloud Messaging d'un utilisateur          |
| `Phone`         | Numéro de téléphone associé à un utilisateur             |

---

## Système de droits (RBAC)

Le portail implémente un système de contrôle d'accès basé sur les rôles (RBAC) à granularité fine :

- **4 niveaux de rôles** : Lecteur (`IS_READR`), Éditeur (`IS_EDITR`), Modérateur (`IS_MODER`), Administrateur (`IS_ADMIN`).
- **Attribution contextuelle** : Les droits peuvent être attribués globalement, par groupe, par profil ou individuellement, et peuvent cibler une ressource spécifique (un article, une rubrique, etc.).
- **7 politiques (Policies)** : `AppPolicy`, `CommentPolicy`, `GroupPolicy`, `PostPolicy`, `RightPolicy`, `RubricPolicy`, `UserPolicy`.
- **Gates dynamiques** : Les accès aux tableaux de bord et aux gestionnaires d'administration sont définis dynamiquement via la façade `AP`.

---

## Tableaux de bord d'administration

| Dashboard         | Fonctions                                                |
|-------------------|----------------------------------------------------------|
| **Principal**     | Utilisateurs, Profils, Groupes, Applications, Rubriques, Contenus, Commentaires, Droits |
| **Organigramme**  | Mise en forme, Nœuds graphiques, Libellés référents      |
| **Statistiques**  | Connexions, Consultations d'articles, Usage global       |

---

## Documentation technique

La documentation PHPDoc est générée via **phpDocumentor** et accessible dans le dossier `public/docs/`.

Pour la régénérer :

```bash
php phpDocumentor.phar -d app -t public/docs
```

---

## Licence

Projet propriétaire — © La Cité des Formations. Tous droits réservés.
