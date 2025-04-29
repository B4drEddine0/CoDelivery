<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommandController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\LivreurController;
use App\Http\Controllers\TrackingController;

Route::get('/', function () {
    return view('welcome');
});

// Auth
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Common route for both clients and livreurs
Route::middleware('auth')->put('/commands/{command}', [CommandController::class, 'update'])->name('commands.update');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->isClient()) {
            return redirect()->route('client.dashboard');
        } else {
            return redirect()->route('livreur.dashboard');
        }
    })->name('dashboard');
    
    Route::prefix('client')->name('client.')->group(function () {
        Route::middleware(\App\Http\Middleware\ClientMiddleware::class)->group(function () {
            Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('dashboard');
            Route::get('/commands', [ClientController::class, 'commands'])->name('commands');

            Route::get('/commands/create', [CommandController::class, 'create'])->name('commands.create');
            Route::post('/commands', [CommandController::class, 'store'])->name('commands.store');
            Route::get('/commands/{command}', [CommandController::class, 'show'])->name('commands.show');
            Route::post('/commands/{command}/cancel', [CommandController::class, 'cancel'])->name('commands.cancel');
            Route::post('/store-location', [ClientController::class, 'storeLocation'])->name('store-location');

            // Command tracking route
            Route::get('/commands/{command}/track', [TrackingController::class, 'showTrackingView'])->name('commands.track');
        });
    });
    
    Route::prefix('livreur')->name('livreur.')->group(function () {
        Route::middleware(\App\Http\Middleware\LivreurMiddleware::class)->group(function () {
            Route::get('/dashboard', [LivreurController::class, 'dashboard'])->name('dashboard');
            Route::get('/commands', [LivreurController::class, 'commands'])->name('commands');
            Route::get('/historique', [LivreurController::class, 'historique'])->name('historique');
            
            Route::get('/commands/{command}', [CommandController::class, 'show'])->name('commands.show');
            Route::post('/commands/{command}/accept', [CommandController::class, 'accept'])->name('commands.accept');
            Route::post('/commands/{command}/start', [CommandController::class, 'startDelivery'])->name('commands.start');
            Route::post('/commands/{command}/complete', [CommandController::class, 'completeDelivery'])->name('commands.complete');
            Route::post('/commands/{command}/cancel', [CommandController::class, 'cancel'])->name('commands.cancel');
            Route::post('/commands/{command}/reset', [CommandController::class, 'reset'])->name('commands.reset');
            Route::post('/store-location', [LivreurController::class, 'storeLocation'])->name('store-location');

            // Command tracking route
            Route::get('/commands/{command}/track', [TrackingController::class, 'showTrackingView'])->name('commands.track');
        });
    });

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
            Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users');
            Route::get('/drivers', [App\Http\Controllers\AdminController::class, 'drivers'])->name('drivers');
            Route::get('/deliveries', [App\Http\Controllers\AdminController::class, 'deliveries'])->name('deliveries');
            Route::delete('/users/{user}', [App\Http\Controllers\AdminController::class, 'deleteUser'])->name('users.delete');
            Route::delete('/deliveries/{command}', [App\Http\Controllers\AdminController::class, 'deleteDelivery'])->name('deliveries.delete');
            
            // Detail routes
            Route::get('/users/{user}', [App\Http\Controllers\AdminController::class, 'showUser'])->name('users.show');
            Route::get('/drivers/{user}', [App\Http\Controllers\AdminController::class, 'showDriver'])->name('drivers.show');
            Route::get('/deliveries/{command}', [App\Http\Controllers\AdminController::class, 'showDelivery'])->name('deliveries.show');
        });
    });
});
