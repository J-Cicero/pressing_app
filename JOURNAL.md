# Journal de Bord - Développement Pressing Multi-Agences

Application de gestion de pressing multi-agences développée avec **Laravel 11**.

---

## Règles Fondamentales du Projet

1. **UI / Design** : Style monochrome et minimaliste strict. Palette autorisée exclusivement :
   - Noir : `#000` / `#000000`
   - Blanc : `#FFF` / `#FFFFFF`
   - Gris clair : `#F3F4F6`
   - Gris anthracite : `#374151`
2. **Sécurité & URLs** :
   - Route Key Binding sur `num_ticket` (ex: `TK-2026-0891` via `getRouteKeyName()`).
   - Masquage systématique de tous les IDs numériques dans les URLs publiques.
3. **Règle Financière** : 100% du paiement est perçu au **RETRAIT** du linge (aucun acompte à la commande).
4. **Suivi de Projet** : Tenue à jour du présent fichier `JOURNAL.md` à chaque étape franchie.

---

## Étape 1 : Migrations, Modèles et Authentification

**Date** : 01 Octobre 2026  
**Statut** : ✅ Terminé avec succès (15/15 tests validés)

### 1. Migrations Exécutées

Les migrations couvrent les 5 entités clés du domaine :

- **`pressings`** (`database/migrations/2026_10_01_122022_create_pressings_table.php`) :
  - `id` (bigint unsigned)
  - `nom` (string)
  - `ville` (string)
  - `quartier` (string)
  - `telephone` (string, nullable)
  - `created_at`, `updated_at` (timestamps)
  - Contrainte de clé étrangère sur `users.pressing_id` -> `pressings.id` (nullOnDelete).

- **`users`** (`database/migrations/0001_01_01_000000_create_users_table.php`) :
  - `id` (bigint unsigned)
  - `pressing_id` (foreignId, nullable)
  - `name` (string)
  - `email` (string, unique)
  - `password` (string)
  - `role` (enum: `'admin'`, `'caissier'`, default: `'caissier'`)
  - `remember_token`, `timestamps`

- **`services`** (`database/migrations/2026_10_01_122023_create_services_table.php`) :
  - `id` (bigint unsigned)
  - `pressing_id` (foreignId, constrained to `pressings`, cascadeOnDelete)
  - `designation` (string)
  - `prix_unitaire` (decimal: 10, 2)
  - `timestamps`

- **`factures`** (`database/migrations/2026_10_01_122024_create_factures_table.php`) :
  - `id` (bigint unsigned)
  - `num_ticket` (string, unique) — sert de Route Key Binding
  - `pressing_id` (foreignId, constrained to `pressings`, cascadeOnDelete)
  - `user_id` (foreignId, constrained to `users`, cascadeOnDelete)
  - `montant_total` (decimal: 10, 2)
  - `statut` (enum: `'depose'`, `'pret'`, `'paye_retire'`, default: `'depose'`)
  - `paye_at` (timestamp, nullable)
  - `timestamps`

- **`ligne_factures`** (`database/migrations/2026_10_01_122025_create_ligne_factures_table.php`) :
  - `id` (bigint unsigned)
  - `facture_id` (foreignId, constrained to `factures`, cascadeOnDelete)
  - `service_id` (foreignId, constrained to `services`, cascadeOnDelete)
  - `quantite` (integer)
  - `prix_applique` (decimal: 10, 2)
  - `timestamps`

---

### 2. Modèles Eloquent et Relations

- **[`App\Models\Pressing`](file:///home/jude/Public/pressing-app/app/Models/Pressing.php)** :
  - `hasMany(User::class)`
  - `hasMany(Service::class)`
  - `hasMany(Facture::class)`
  - `$fillable = ['nom', 'ville', 'quartier', 'telephone']`

- **[`App\Models\User`](file:///home/jude/Public/pressing-app/app/Models/User.php)** :
  - `belongsTo(Pressing::class)`
  - `hasMany(Facture::class)`
  - Méthodes utilitaires : `isAdmin(): bool`, `isCaissier(): bool`
  - `$fillable = ['pressing_id', 'name', 'email', 'password', 'role']`

- **[`App\Models\Service`](file:///home/jude/Public/pressing-app/app/Models/Service.php)** :
  - `belongsTo(Pressing::class)`
  - `hasMany(LigneFacture::class)`
  - `$fillable = ['pressing_id', 'designation', 'prix_unitaire']`
  - `$casts = ['prix_unitaire' => 'decimal:2']`

- **[`App\Models\Facture`](file:///home/jude/Public/pressing-app/app/Models/Facture.php)** :
  - `belongsTo(Pressing::class)`
  - `belongsTo(User::class)`
  - `hasMany(LigneFacture::class)`
  - Clé de liaison de route personnalisée :
    ```php
    public function getRouteKeyName(): string
    {
        return 'num_ticket';
    }
    ```
  - `$fillable = ['num_ticket', 'pressing_id', 'user_id', 'montant_total', 'statut', 'paye_at']`
  - `$casts = ['montant_total' => 'decimal:2', 'paye_at' => 'datetime']`

- **[`App\Models\LigneFacture`](file:///home/jude/Public/pressing-app/app/Models/LigneFacture.php)** :
  - `belongsTo(Facture::class)`
  - `belongsTo(Service::class)`
  - `$fillable = ['facture_id', 'service_id', 'quantite', 'prix_applique']`
  - `$casts = ['quantite' => 'integer', 'prix_applique' => 'decimal:2']`

---

### 3. Système d'Authentification & Rôles

- **Middleware [`EnsureUserHasRole`](file:///home/jude/Public/pressing-app/app/Http/Middleware/EnsureUserHasRole.php)** :
  - Enregistré avec l'alias `role` dans [`bootstrap/app.php`](file:///home/jude/Public/pressing-app/bootstrap/app.php).
  - Contrôle dynamique d'accès multi-rôles (`role:admin`, `role:caissier`, etc.).
  - Renvoie HTTP `403` si rôle non autorisé et redirige vers `/login` si non authentifié.
- **Contrôleur [`AuthController`](file:///home/jude/Public/pressing-app/app/Http/Controllers/AuthController.php)** :
  - `showLogin()` : formulaire de connexion monochrome.
  - `login()` : validation des identifiants et régénération de session.
  - `logout()` : clôture de session et régénération du token CSRF.
  - `dashboard()` : affichage de l'espace de bord contextualisé par rôle et agence.
- **Vues & Design Monochrome Strict** :
  - [`resources/views/layouts/app.blade.php`](file:///home/jude/Public/pressing-app/resources/views/layouts/app.blade.php) : Layout monochrome (#000, #FFF, #F3F4F6, #374151).
  - [`resources/views/auth/login.blade.php`](file:///home/jude/Public/pressing-app/resources/views/auth/login.blade.php) : Vue de connexion minimaliste.
  - [`resources/views/dashboard.blade.php`](file:///home/jude/Public/pressing-app/resources/views/dashboard.blade.php) : Tableau de bord avec indicateurs admin et caissier.
- **Seeding & Démonstration** :
  - [`database/seeders/DatabaseSeeder.php`](file:///home/jude/Public/pressing-app/database/seeders/DatabaseSeeder.php) :
    - Pressing : `Pressing Central (Cotonou, Haie Vive)`
    - Admin : `admin@pressing.local` (mot de passe: `password`)
    - Caissier : `caissier@pressing.local` (mot de passe: `password`)
    - Services par défaut : Nettoyage Costume, Lavage Chemise, Repassage Pantalon, Nettoyage Robe, Lavage Couette.

---

### 4. Tests & Validation (Étape 1)

- **Résultat global** : 15 tests exécutés, 15 réussis, 33 assertions validées.

---

## Étape 2 : Espace Super Admin (Zéro Donnée Mockée)

**Date** : 01 Octobre 2026  
**Statut** : ✅ Terminé avec succès (36/36 tests validés)

### 1. Dashboard Super Admin (`/admin/dashboard`)

- **Contrôleur** : [`App\Http\Controllers\Admin\AdminDashboardController`](file:///home/jude/Public/pressing-app/app/Http/Controllers/Admin/AdminDashboardController.php)
- **Requêtes réelles MySQL Eloquent** (aucune donnée codée en dur) :
  * **CA du jour** :
    ```php
    Facture::where('statut', 'paye_retire')
        ->whereDate('paye_at', now()->toDateString())
        ->sum('montant_total');
    ```
  * **CA du mois** :
    ```php
    Facture::where('statut', 'paye_retire')
        ->whereYear('paye_at', now()->year)
        ->whereMonth('paye_at', now()->month)
        ->sum('montant_total');
    ```
  * **Total impayés / en attente** :
    ```php
    Facture::where('statut', '!=', 'paye_retire')
        ->sum('montant_total');
    ```
  * **Tableau comparatif dynamique par agence** :
    ```php
    Pressing::query()
        ->withCount('factures as total_tickets')
        ->withSum(['factures as ca_mois' => function ($q) use ($now) {
            $q->where('statut', 'paye_retire')
                ->whereYear('paye_at', $now->year)
                ->whereMonth('paye_at', $now->month);
        }], 'montant_total')
        ->withSum(['factures as total_impayes' => function ($q) {
            $q->where('statut', '!=', 'paye_retire');
        }], 'montant_total')
        ->withCount('users as total_personnel')
        ->orderBy('nom')
        ->get();
    ```
  * **Filtre temporel dynamique** : paramètre `?periode=jour`, `?periode=mois`, `?periode=tout` filtrant les KPI et les sous-requêtes.
- **Vue** : [`resources/views/admin/dashboard.blade.php`](file:///home/jude/Public/pressing-app/resources/views/admin/dashboard.blade.php) (UI monochrome, cartes de métriques et tableau comparatif).

---

### 2. Gestion des Pressings (`/admin/pressings`)

- **Contrôleur** : [`App\Http\Controllers\Admin\PressingController`](file:///home/jude/Public/pressing-app/app/Http/Controllers/Admin/PressingController.php)
- **Fonctionnalités** :
  - `index()` : liste paginée avec recherche (`q`), compteurs réels de personnel, services et factures par agence.
  - `create()`, `store()` : validation et création (`nom`, `ville`, `quartier`, `telephone`).
  - `edit()`, `update()` : modification des informations d'agence.
  - `destroy()` : suppression sécurisée (rejet si des factures sont rattachées).
- **Vues** :
  - [`resources/views/admin/pressings/index.blade.php`](file:///home/jude/Public/pressing-app/resources/views/admin/pressings/index.blade.php)
  - [`resources/views/admin/pressings/create.blade.php`](file:///home/jude/Public/pressing-app/resources/views/admin/pressings/create.blade.php)
  - [`resources/views/admin/pressings/edit.blade.php`](file:///home/jude/Public/pressing-app/resources/views/admin/pressings/edit.blade.php)

---

### 3. Gestion du Personnel (`/admin/users`)

- **Contrôleur** : [`App\Http\Controllers\Admin\UserController`](file:///home/jude/Public/pressing-app/app/Http/Controllers/Admin/UserController.php)
- **Fonctionnalités** :
  - `index()` : liste des comptes avec filtre par agence (`pressing_id`), par rôle (`role`) et recherche par nom/email.
  - `create()`, `store()` : création de compte avec sélection du rôle (`admin`, `caissier`) et agence obligatoire pour caissier.
  - `edit()`, `update()` : mise à jour des accès et réaffectation d'agence.
  - `destroy()` : suppression sécurisée (interdiction d'auto-suppression et protection des comptes ayant émis des factures).
- **Vues** :
  - [`resources/views/admin/users/index.blade.php`](file:///home/jude/Public/pressing-app/resources/views/admin/users/index.blade.php)
  - [`resources/views/admin/users/create.blade.php`](file:///home/jude/Public/pressing-app/resources/views/admin/users/create.blade.php)
  - [`resources/views/admin/users/edit.blade.php`](file:///home/jude/Public/pressing-app/resources/views/admin/users/edit.blade.php)

---

### 4. Catalogue des Prestations (`/admin/services`)

- **Contrôleur** : [`App\Http\Controllers\Admin\ServiceController`](file:///home/jude/Public/pressing-app/app/Http/Controllers/Admin/ServiceController.php)
- **Fonctionnalités** :
  - `index()` : catalogue complet avec filtre par agence, recherche par désignation, affichage des prix et nombre de factures associées.
  - `create()`, `store()` : ajout d'une prestation avec affectation à une agence (`designation`, `prix_unitaire`, `pressing_id`).
  - `edit()`, `update()` : mise à jour des libellés et des tarifs.
  - `destroy()` : suppression sécurisée (rejet si référencé dans `ligne_factures`).
- **Vues** :
  - [`resources/views/admin/services/index.blade.php`](file:///home/jude/Public/pressing-app/resources/views/admin/services/index.blade.php)
  - [`resources/views/admin/services/create.blade.php`](file:///home/jude/Public/pressing-app/resources/views/admin/services/create.blade.php)
  - [`resources/views/admin/services/edit.blade.php`](file:///home/jude/Public/pressing-app/resources/views/admin/services/edit.blade.php)

---

### 5. Vue Consolidée des Factures (`/admin/factures`)

- **Contrôleur** : [`App\Http\Controllers\Admin\FactureController`](file:///home/jude/Public/pressing-app/app/Http/Controllers/Admin/FactureController.php)
- **Fonctionnalités** :
  - `index()` : liste dynamique centralisée avec eager loading (`pressing`, `user`, `ligneFactures.service`), calcul du total et du volume des factures filtrées.
  - Filtres fonctionnels :
    * Par agence de pressing (`pressing_id`)
    * Par statut de traitement (`statut` : `depose`, `pret`, `paye_retire`)
    * Recherche textuelle par numéro de ticket (`num_ticket`)
  - `show(Facture $facture)` : consultation de la fiche détaillée d'un ticket avec ventilation des lignes d'articles, quantités, sous-totaux et rappels financiers.
  - **Sécurité des URLs** : Route Key Binding natif sur `num_ticket` (ex: `/admin/factures/TK-2026-0001`), aucun ID numérique exposé dans l'URL.
- **Vues** :
  - [`resources/views/admin/factures/index.blade.php`](file:///home/jude/Public/pressing-app/resources/views/admin/factures/index.blade.php)
  - [`resources/views/admin/factures/show.blade.php`](file:///home/jude/Public/pressing-app/resources/views/admin/factures/show.blade.php)

---

### 6. Sécurité des Routes et Rôles

Toutes les routes d'administration sont encapsulées dans le groupe de routes protégé par le middleware `role:admin` dans [`routes/web.php`](file:///home/jude/Public/pressing-app/routes/web.php) :
- `admin.dashboard`
- `admin.pressings.*` (Resource CRUD)
- `admin.users.*` (Resource CRUD)
- `admin.services.*` (Resource CRUD)
- `admin.factures.index`, `admin.factures.show`

---

### 7. Tests & Validation Globale (Étape 2)

Tests automatisés exécutés via PHPUnit :
- **[`tests/Feature/AdminDashboardTest.php`](file:///home/jude/Public/pressing-app/tests/Feature/AdminDashboardTest.php)** : vérification du contrôle d'accès, des agrégats BDD et des filtres temporels.
- **[`tests/Feature/AdminPressingsTest.php`](file:///home/jude/Public/pressing-app/tests/Feature/AdminPressingsTest.php)** : vérification du CRUD complet des pressings.
- **[`tests/Feature/AdminUsersTest.php`](file:///home/jude/Public/pressing-app/tests/Feature/AdminUsersTest.php)** : vérification du CRUD personnel et affectation agence.
- **[`tests/Feature/AdminServicesTest.php`](file:///home/jude/Public/pressing-app/tests/Feature/AdminServicesTest.php)** : vérification du CRUD prestations et prix unitaires.
- **[`tests/Feature/AdminFacturesTest.php`](file:///home/jude/Public/pressing-app/tests/Feature/AdminFacturesTest.php)** : vérification des filtres (agence, statut, ticket) et du Route Key Binding via `num_ticket`.
- **Résultat global** : **36 tests exécutés, 36 réussis, 92 assertions validées**.

---

## Étape 3 : Espace Caissier & Impression Thermique 80mm

**Date** : 02 Octobre 2026  
**Statut** : ✅ Terminé avec succès (47/47 tests validés)

### 1. Migration & Évolution du Schéma de Données

- **Migration** : [`database/migrations/2026_10_02_065338_add_client_and_date_retrait_to_factures_table.php`](file:///home/jude/Public/pressing-app/database/migrations/2026_10_02_065338_add_client_and_date_retrait_to_factures_table.php)
  - `client_nom` (string, nullable)
  - `client_telephone` (string, nullable, indexé) pour les recherches rapides au guichet
  - `date_retrait_prevue` (date, nullable)
- Modèle [`Facture`](file:///home/jude/Public/pressing-app/app/Models/Facture.php) mis à jour avec les attributs `$fillable` et le cast `date_retrait_prevue => 'date'`.

---

### 2. Tableau de Bord Caissier (`/caisse/dashboard`)

- **Contrôleur** : [`App\Http\Controllers\Caisse\CaissierDashboardController`](file:///home/jude/Public/pressing-app/app/Http/Controllers/Caisse/CaissierDashboardController.php)
- **Isolation d'agence** : filtrage strict sur le `pressing_id` du caissier authentifié.
- **Métriques en temps réel issues de MySQL** :
  * Nombre de tickets déposés aujourd'hui dans l'agence (`whereDate('created_at', today())`)
  * Nombre de factures prêtes au retrait (`statut = 'pret'`)
  * Total encaissé aujourd'hui (`statut = 'paye_retire'` et `whereDate('paye_at', today())`)
  * Total de tickets en cours d'agence (`statut in ('depose', 'pret')`)
  * Tableau des derniers tickets récents émis au guichet.
- **Accès rapide** : boutons d'action "Nouveau Dépôt" et "Gestion / Encaissement Ticket".
- **Vue** : [`resources/views/caisse/dashboard.blade.php`](file:///home/jude/Public/pressing-app/resources/views/caisse/dashboard.blade.php).

---

### 3. Module Nouveau Dépôt (`/caisse/depot`)

- **Contrôleur** : [`App\Http\Controllers\Caisse\DepotController`](file:///home/jude/Public/pressing-app/app/Http/Controllers/Caisse/DepotController.php)
- **Formulaire Dynamique & Règle Financière Stricte** :
  - Saisie du nom, téléphone client, et date de retrait prévue.
  - Sélection des prestations restreintes au catalogue de l'agence du caissier.
  - Répéteur JavaScript dynamique de lignes d'articles avec calcul automatique du sous-total et du total TTC en FCFA.
  - **Acompte verrouillé à 0 FCFA** (application stricte de la règle financière : 100% du règlement lors du retrait des vêtements).
  - Reste à payer au retrait égal au total TTC.
- **Enregistrement en Transaction BDD** :
  - Génération d'un numéro de ticket unique au format `TCK-YYYYMMDD-XXXX`.
  - Calcul fiable du montant total côté serveur à partir des tarifs en BDD.
  - Enregistrement de la `facture` avec `statut = 'depose'`, `paye_at = null`, et insertion des `ligne_factures` au sein d'une transaction `DB::transaction`.
  - Redirection automatique vers le ticket thermique d'impression.
- **Vue** : [`resources/views/caisse/depot.blade.php`](file:///home/jude/Public/pressing-app/resources/views/caisse/depot.blade.php).

---

### 4. Ticket de Caisse Thermal 80mm (`/caisse/factures/{facture:num_ticket}/print`)

- **Contrôleur** : [`App\Http\Controllers\Caisse\TicketPrintController`](file:///home/jude/Public/pressing-app/app/Http/Controllers/Caisse/TicketPrintController.php)
- **Caractéristiques de la Vue** :
  - Optimisée pour rouleau papier 80mm (`@page { size: 80mm auto; margin: 0; }`, largeur 80mm, marges réduites, police monospace).
  - En-tête : nom du pressing, quartier, ville, téléphone.
  - Métadonnées : N° Ticket, date/heure dépôt, nom & tél client, nom caissier, date retrait prévue.
  - Tableau des articles : désignation, quantité, prix unitaire, total par ligne.
  - Montant total TTC, acompte (0 FCFA), et net à payer.
  - **Mention légale obligatoire** : *"PAIEMENT À 100% LORS DU RETRAIT DE VOS ARTICLES."*
  - Script d'impression automatique `window.print()` au chargement.
  - Barre d'outils à l'écran masquée à l'impression via `@media print`.
- **Vue** : [`resources/views/caisse/print.blade.php`](file:///home/jude/Public/pressing-app/resources/views/caisse/print.blade.php).

---

### 5. Recherche & Encaissement au Retrait (`/caisse/retrait`)

- **Contrôleur** : [`App\Http\Controllers\Caisse\RetraitController`](file:///home/jude/Public/pressing-app/app/Http/Controllers/Caisse/RetraitController.php)
- **Fonctionnalités** :
  - Recherche en temps réel par numéro de ticket (`num_ticket`), numéro de téléphone client ou nom.
  - Filtres par statut (`depose`, `pret`, `paye_retire`) avec compteurs par catégorie.
  - **Action 1 : Marquer Prêt** (`PATCH /caisse/factures/{facture:num_ticket}/pret`) : transition du statut `'depose'` vers `'pret'` (vêtements nettoyés et repassés, disponibles au retrait).
  - **Action 2 : Encaisser & Restituer** (`PATCH /caisse/factures/{facture:num_ticket}/encaisser`) : transition vers `'paye_retire'`, enregistrement immédiat de l'horodatage `paye_at = now()`, formalisant la perception à 100% du montant.
  - Lien direct vers la réimpression du ticket/reçu thermique 80mm.
- **Vue** : [`resources/views/caisse/retrait.blade.php`](file:///home/jude/Public/pressing-app/resources/views/caisse/retrait.blade.php).

---

### 6. Sécurité & Navigation Caissier

- **Middleware `role:caissier`** : applique un sas d'accès étanche aux routes `/caisse/*`.
- **Isolation des données** : chaque caissier ne peut voir, déposer, modifier ou encaisser que les factures rattachées à son `pressing_id`.
- **Navigation Layout** : menu dédié dans [`resources/views/layouts/app.blade.php`](file:///home/jude/Public/pressing-app/resources/views/layouts/app.blade.php) (Dashboard Caisse, Nouveau Dépôt, Retraits & Encaissement).

---

### 7. Tests & Validation Globale (Étape 3)

- **Test Suite** : [`tests/Feature/CaisseParcoursTest.php`](file:///home/jude/Public/pressing-app/tests/Feature/CaisseParcoursTest.php)
  - `test_guest_is_redirected_from_caisse` : ✅
  - `test_admin_cannot_access_caissier_dashboard_directly` : ✅
  - `test_caissier_can_access_caisse_dashboard_and_sees_own_agency_metrics` : ✅
  - `test_caissier_can_view_depot_form_with_own_services` : ✅
  - `test_caissier_can_create_depot_and_gets_redirected_to_thermal_print` : ✅
  - `test_cannot_create_depot_with_service_from_another_agency` : ✅
  - `test_caissier_can_view_thermal_print_ticket_via_route_key_binding` : ✅
  - `test_caissier_cannot_view_print_ticket_from_another_agency` : ✅
  - `test_caissier_can_mark_ticket_as_pret` : ✅
  - `test_caissier_can_encaisser_et_restituer_with_100_percent_payment_and_timestamp` : ✅
  - `test_caissier_can_search_tickets_by_phone_and_num_ticket` : ✅
- **Résultat global** : **47 tests exécutés, 47 réussis, 134 assertions validées**.

---

## Configuration de l'Environnement de Démonstration & Test (DatabaseSeeder)

**Date** : 02 Octobre 2026  
**Statut** : ✅ Terminé et initialisé (`php artisan migrate:fresh --seed`)

### 1. Comptes et Agences Configurés

- **Super Administrateur** :
  - Nom : `Administrateur Principal`
  - Email : `admin@pressing.com`
  - Mot de passe : `password`
  - Rôle : `admin`

- **Agence 1 : Pressing Centre-Ville** :
  - Ville : `Lomé`
  - Quartier : `Centre-Ville`
  - Téléphone : `+228 90 00 00 01`
  - Caissier rattaché : `caissier1@pressing.com` (Nom: `Caissier Centre-Ville`, Mdp: `password`, Rôle: `caissier`)

- **Agence 2 : Pressing GTA** :
  - Ville : `Lomé`
  - Quartier : `GTA`
  - Téléphone : `+228 90 00 00 02`
  - Caissier rattaché : `caissier2@pressing.com` (Nom: `Caissier GTA`, Mdp: `password`, Rôle: `caissier`)

### 2. Catalogue des Prestations Réelles (par agence)

- `Chemise` : 1 000 FCFA
- `Costume 2 Pièces` : 3 500 FCFA
- `Robe de soirée` : 2 500 FCFA
- `Pantalon` : 1 200 FCFA
- `Draps / Couette` : 4 000 FCFA

### 3. Factures Initiales Ensemencées

- Factures réalistes multi-statuts (`depose`, `pret`, `paye_retire`) avec lignes d'articles associées pour alimenter immédiatement les dashboards d'administration et de caisse sans aucune donnée mockée.

### 4. Validation des Tests

- Exécution de `php artisan test` : **47 tests exécutés, 47 réussis, 134 assertions (100% au vert)**.

