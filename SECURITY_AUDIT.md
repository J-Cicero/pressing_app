# Rapport d'Audit et de Remédiation de Sécurité — Projet Laravel `pressing_app`

**Date de l'audit et de la remédiation :** 29 octobre 2026  
**Dépôt :** `C:\Users\Eric\pressing_app`  
**Branche Git :** `develop`  
**Système :** Windows 10 Pro 64 bits  

---

## 1. Versions avant et après remédiation

| Composant | Version avant remédiation | Version après remédiation | Statut |
| :--- | :--- | :--- | :--- |
| **PHP (CLI / Runtime)** | `8.5.11` (NTS x64 VS17) | **`8.5.12`** (`8.5.12-1-nts-Win32-vs17-x64`) | Corrigé (CVE-2026-109618, CVE-2026-109619) |
| **Composer** | `2.10.3` | **`2.10.3`** (pointant sur `php-8.5.12`) | À jour |
| **Laravel Framework** (`laravel/framework`) | `v13.34.0` | **`v13.39.0`** | Mis à jour (dernière 13.x compatible) |
| **Symfony Mime** (`symfony/mime`) | `v7.4.19` | **`v8.1.10`** | Corrigé (`CVE-2026-45070`) |
| **Symfony HttpFoundation** (`symfony/http-foundation`) | `v7.4.20` | **`v8.1.10`** | Mis à jour |
| **Symfony Mailer** (`symfony/mailer`) | `v7.4.19` | **`v8.1.10`** | Mis à jour |
| **Vite** (`vite`) | `8.3.0` (`package-lock.json`) | **`8.3.5`** | Corrigé (GHSA-4w7w-66w2-5vf9, GHSA-v2wj-q39q-566r, GHSA-p9ff-h696-f583) |
| **shell-quote** (transitif via `concurrently`) | `1.8.4` (`package-lock.json`) | **`1.10.1`** | Corrigé (GHSA-596m-4h6j-3w5x / CVE-2026-34040) |

---

## 2. Vulnérabilités confirmées et corrigées

### 2.1. Environnement et dépendances
1. **[CRITIQUE — CVSS 9.2] PHP 8.5.11 — `CVE-2026-109618` & `CVE-2026-109619` ("PHP2Shell")**
   - **Remédiation :** Téléchargement officiel de `php-8.5.12-1-nts-Win32-vs17-x64.zip` depuis `windows.php.net`, vérification de l'empreinte officielle SHA-256 (`2a36a2edcb7a5b9252f638fd5e4cf84d7f6e066092654220dc0b63e5cf4e9723`), installation dans `C:\Users\Eric\DevTools\php-8.5.12` (en conservant `C:\Users\Eric\DevTools\php-8.5.11`), migration et durcissement de `php.ini` (`expose_php = Off`, `session.use_strict_mode = 1`, `session.cookie_httponly = 1`, `cacert.pem`), mise à jour du `PATH` utilisateur et de `C:\Users\Eric\DevTools\bin\composer.cmd`.
2. **[MOYENNE] `symfony/mime` `v7.4.19` — `CVE-2026-45070` / `GHSA-cfhh-v37f-r767`**
   - **Remédiation :** Mise à jour de `symfony/mime` vers `v8.1.10` et de `laravel/framework` vers `v13.39.0`. `composer audit` retourne désormais `No security vulnerability advisories found.`
3. **[CRITIQUE / HAUTE] `vite` (`8.3.0`) & `shell-quote` (`1.8.4`)**
   - **Remédiation :** Exécution de `npm.cmd install --ignore-scripts` puis `npm.cmd audit fix` et `npm.cmd run build`. `vite` est passé en `8.3.5` et `shell-quote` en `1.10.1`. `npm.cmd audit` retourne désormais `found 0 vulnerabilities`.

### 2.2. Code applicatif Laravel
1. **[HAUTE] Absence de Rate Limiting sur `/login` et `/caisse/clients/lookup`**
   - Ajout des limiteurs `login` (5 tentatives/min par couple email+IP) et `client-lookup` (60 req/min par utilisateur/IP) dans `app/Providers/AppServiceProvider.php` et application des middlewares `throttle:login` et `throttle:client-lookup` dans `routes/web.php`.
2. **[HAUTE] IDOR inter-agences sur les laveurs (`laveur_id`) et privilèges de fidélité inter-activités**
   - Dans `app/Http/Controllers/Caisse/DepotController.php` et `app/Http/Controllers/Caisse/RetraitController.php`, restriction de `laveur_id` via `Rule::exists('laveurs', 'id')->where('pressing_id', $pressing->id)->where('actif', true)`.
   - Dans `app/Http/Controllers/Caisse/DepotController.php`, vérification que l'avantage ou la règle de fidélité appliquée correspond bien au `type_activite` de l'agence (`[$pressing->type_activite, 'global']`).
3. **[MOYENNE] XSS DOM via `innerHTML` dans les vues de dépôt**
   - Ajout et utilisation systématique d'une fonction d'échappement HTML `escapeHtml()` sur `s.designation`, `s.id` et `s.prix_unitaire` dans `resources/views/caisse/lavage/depot.blade.php`, `resources/views/caisse/pressing/depot.blade.php` et `resources/views/caisse/depot.blade.php`.
4. **[MOYENNE] Identifiants par défaut pré-remplis et affichés en clair sur `/login`**
   - Dans `resources/views/auth/login.blade.php`, restriction des valeurs par défaut (`admin@pressing.com` / `password`) et des boutons d'accès rapide aux seuls environnements `local` et `testing` (`@env(['local', 'testing'])`).
5. **[MOYENNE] Conditions de concurrence (TOCTOU) sur l'encaissement, la consommation et le déblocage des avantages fidélité**
   - Verrouillage pessimiste `lockForUpdate()` dans `DepotController::store`, `RetraitController::encaisser` et `Client::debloquerAvantagesEligibles`.
6. **[MOYENNE] Révocation des sessions actives et protection contre l'auto-rétrogradation admin**
   - Dans `app/Http/Controllers/Admin/UserController.php`, blocage du retrait de son propre rôle `admin`, rotation du `remember_token` et suppression des sessions actives (`DB::table('sessions')->where('user_id', $user->id)->delete()`) lors d'un changement de mot de passe, de rôle, d'agence ou de suppression du compte.
7. **[FAIBLE / MOYENNE] Injection de jokers SQL `LIKE` (`%`, `_`) et groupement logique `OR` dans les recherches**
   - Échappement des caractères `%`, `_`, `\` via `addcslashes()` et encapsulage des clauses `orWhere` dans des sous-requêtes `where(function ($sub) ...)` dans `CaisseClientLookupController`, `RetraitController`, `Admin\LaveurController`, `Admin\PressingController` et `Admin\ClientController`.
8. **[FAIBLE] En-têtes HTTP de sécurité, HTTPS en production et robustesse contrôleurs**
   - Création et enregistrement global de `app/Http/Middleware/SecurityHeaders.php` (`X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy: strict-origin-when-cross-origin`, `Permissions-Policy`, `Strict-Transport-Security` en HTTPS).
   - Forçage HTTPS (`URL::forceScheme('https')`) en production dans `app/Providers/AppServiceProvider.php`.
   - Validation robuste des dates/mois dans `CaisseLaveurController` et correction de la variable `$pressing` dans `CaisseVehiculeController`.

---

## 3. Résultats des vérifications et suites de tests

- `composer validate` : `./composer.json is valid`
- `composer audit --format=plain` : `No security vulnerability advisories found.`
- `npm.cmd audit` : `found 0 vulnerabilities`
- `npm.cmd run build` : Build de production Vite v8.3.5 réussi (`✓ built in 3.68s`).
- `vendor/bin/pint --dirty --format agent` : `{"tool":"pint","result":"passed"}`
- `php artisan test --compact tests/Feature/SecurityAuditTest.php` : **7 tests passés sur 7 (29 assertions)**.
- `php artisan test --compact` (suite complète) : **83 tests passés sur 85 (322 assertions)**. Les 2 échecs restants (`CaisseParcoursTest::test_caissier_can_view_depot_form_with_own_services` et `ErpMultiServicesTest::test_cashier_can_store_and_update_vehicle_and_deadlines`) sont préexistants et liés au libellé UI `Net à Payer au Retrait` ainsi qu'au format de date SQLite `YYYY-MM-DD 00:00:00` sur le cast `date`.
