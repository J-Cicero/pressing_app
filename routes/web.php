<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\FactureController;
use App\Http\Controllers\Admin\PressingController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
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

        // CRUD Pressings
        Route::resource('pressings', PressingController::class)->except(['show']);

        // CRUD Personnel / Utilisateurs
        Route::resource('users', UserController::class)->except(['show']);

        // CRUD Catalogue des Services
        Route::resource('services', ServiceController::class)->except(['show']);

        // Vue Consolidée des Factures (Route Key Binding via num_ticket)
        Route::get('/factures', [FactureController::class, 'index'])->name('factures.index');
        Route::get('/factures/{facture}', [FactureController::class, 'show'])->name('factures.show');
    });

    // Espace Réservé Caissier (Middleware 'role:caissier')
    Route::middleware('role:caissier')->prefix('caisse')->name('caisse.')->group(function () {
        Route::get('/dashboard', [CaissierDashboardController::class, 'index'])->name('dashboard');

        // Module Nouveau Dépôt
        Route::get('/depot', [DepotController::class, 'create'])->name('depot');
        Route::post('/depot', [DepotController::class, 'store'])->name('depot.store');

        // Recherche & Encaissement au Retrait
        Route::get('/retrait', [RetraitController::class, 'index'])->name('retrait');
        Route::patch('/factures/{facture:num_ticket}/pret', [RetraitController::class, 'marquerPret'])->name('factures.pret');
        Route::patch('/factures/{facture:num_ticket}/encaisser', [RetraitController::class, 'encaisser'])->name('factures.encaisser');

        // Impression Thermique 80mm
        Route::get('/factures/{facture:num_ticket}/print', [TicketPrintController::class, 'show'])->name('factures.print');
    });
});
