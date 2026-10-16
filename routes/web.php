<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\FactureController;
use App\Http\Controllers\Admin\LaveurController;
use App\Http\Controllers\Admin\PressingController;
use App\Http\Controllers\Admin\RegleFideliteController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VehiculeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Caisse\CaisseClientLookupController;
use App\Http\Controllers\Caisse\CaisseFactureController;
use App\Http\Controllers\Caisse\CaisseLaveurController;
use App\Http\Controllers\Caisse\CaisseVehiculeController;
use App\Http\Controllers\Caisse\CaissierDashboardController;
use App\Http\Controllers\Caisse\DepotController;
use App\Http\Controllers\Caisse\RetraitController;
use App\Http\Controllers\Caisse\TicketPrintController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirection d'accueil
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentification (Invités)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Routes Authentifiées
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Espace Réservé Administrateur (Middleware 'role:admin')
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // CRUD Établissements (Pressings & Stations de Lavage)
        Route::resource('pressings', PressingController::class)->except(['show']);

        // CRUD Personnel & Caissiers
        Route::resource('users', UserController::class)->except(['show']);

        // CRUD Catalogue des Services
        Route::resource('services', ServiceController::class)->except(['show']);

        // Gestion Centralisée des Clients
        Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');

        // Règles de Fidélité Paramétrables
        Route::resource('fidelite', RegleFideliteController::class)->parameters(['fidelite' => 'regle'])->except(['show']);

        // Gestion des Laveurs (Personnel de Terrain) & Décompte des Salaires
        Route::resource('laveurs', LaveurController::class);
        Route::post('/laveurs/{laveur}/paiement', [LaveurController::class, 'enregistrerPaiement'])->name('laveurs.paiement');
        Route::post('/laveurs/{laveur}/annuler-paiement', [LaveurController::class, 'annulerPaiement'])->name('laveurs.annuler-paiement');

        // Module Rappels Échéances Véhicules (Visite Technique & Assurance)
        Route::get('/vehicules/echeances', [VehiculeController::class, 'echeances'])->name('vehicules.echeances');
        Route::post('/vehicules', [VehiculeController::class, 'store'])->name('vehicules.store');
        Route::put('/vehicules/{vehicule}', [VehiculeController::class, 'update'])->name('vehicules.update');

        // Vue Consolidée des Factures
        Route::get('/factures', [FactureController::class, 'index'])->name('factures.index');
        Route::get('/factures/{facture}', [FactureController::class, 'show'])->name('factures.show');
    });

    // Espace Réservé Caissier (Middleware 'role:caissier')
    Route::middleware('role:caissier')->prefix('caisse')->name('caisse.')->group(function () {
        Route::get('/dashboard', [CaissierDashboardController::class, 'index'])->name('dashboard');

        // Module Nouveau Dépôt / Lavage
        Route::get('/depot', [DepotController::class, 'create'])->name('depot');
        Route::post('/depot', [DepotController::class, 'store'])->name('depot.store');

        // API Lookup Client automatique par téléphone
        Route::get('/clients/lookup', CaisseClientLookupController::class)->name('clients.lookup');

        // Recherche & Encaissement au Retrait
        Route::get('/retrait', [RetraitController::class, 'index'])->name('retrait');
        Route::patch('/factures/{facture:num_ticket}/pret', [RetraitController::class, 'marquerPret'])->name('factures.pret');
        Route::patch('/factures/{facture:num_ticket}/encaisser', [RetraitController::class, 'encaisser'])->name('factures.encaisser');
        Route::patch('/factures/{facture:num_ticket}/laveur', [RetraitController::class, 'assignerLaveur'])->name('factures.assigner-laveur');

        // Suivi des Laveurs et Commissions Journalières (Station Lavage)
        Route::get('/laveurs', [CaisseLaveurController::class, 'index'])->name('laveurs.index');

        // Échéances Visite Technique des Véhicules & Rappels WhatsApp
        Route::get('/vehicules/echeances', [CaisseVehiculeController::class, 'echeances'])->name('vehicules.echeances');
        Route::post('/vehicules', [CaisseVehiculeController::class, 'store'])->name('vehicules.store');
        Route::put('/vehicules/{vehicule}', [CaisseVehiculeController::class, 'update'])->name('vehicules.update');

        // Impression Thermique 80mm
        Route::get('/factures/{facture:num_ticket}/print', [TicketPrintController::class, 'show'])->name('factures.print');

        // Historique & Suivi des Factures de l'agence
        Route::get('/factures', [CaisseFactureController::class, 'index'])->name('factures.index');
    });
});
