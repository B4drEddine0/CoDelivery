<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrackingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Geocoding route (no auth required)
Route::get('/geocode', [TrackingController::class, 'geocodeAddress']);

// Location tracking routes (require auth)
Route::middleware('auth')->group(function () {
    // Update location (client or livreur)
    Route::post('/update-location', [TrackingController::class, 'updateLocation']);
    
    // Get location data for a command
    Route::get('/commands/{command}/location', [TrackingController::class, 'getLocationData']);
    
    // Update delivery coordinates
    Route::post('/update-delivery-coordinates', function(Request $request) {
        $command = \App\Models\Command::findOrFail($request->command_id);
        $command->update([
            'delivery_latitude' => $request->latitude,
            'delivery_longitude' => $request->longitude,
        ]);
        return response()->json(['success' => true]);
    });
});