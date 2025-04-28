<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    /**
     * Update the client's location in the database.
     */
    public function updateClientLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'command_id' => 'required|integer|exists:commands,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid location data',
                'errors' => $validator->errors()
            ], 422);
        }

        $command = Command::findOrFail($request->command_id);
        
        // Security check - only the client associated with this command can update location
        if ($command->client_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Store location in the session for now
        // This could be stored in the database with a separate location tracking model
        // depending on your requirements
        session()->put('client_location', [
            'command_id' => $request->command_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy,
            'updated_at' => now()->toIso8601String(),
        ]);

        // Log location update (optional, for debugging)
        Log::info('Client location updated', [
            'user_id' => Auth::id(),
            'command_id' => $request->command_id,
            'location' => [
                'lat' => $request->latitude,
                'lng' => $request->longitude,
            ]
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Location updated successfully'
        ]);
    }

    /**
     * Update the livreur's location in the database.
     */
    public function updateLivreurLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'command_id' => 'required|integer|exists:commands,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid location data',
                'errors' => $validator->errors()
            ], 422);
        }

        $command = Command::findOrFail($request->command_id);
        
        // Security check - only the assigned livreur can update location
        if ($command->livreur_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Store location in the session for now
        // This could be stored in the database with a separate location tracking model
        // depending on your requirements
        session()->put('livreur_location', [
            'command_id' => $request->command_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy,
            'updated_at' => now()->toIso8601String(),
        ]);

        // Log location update (optional, for debugging)
        Log::info('Livreur location updated', [
            'user_id' => Auth::id(),
            'command_id' => $request->command_id,
            'location' => [
                'lat' => $request->latitude,
                'lng' => $request->longitude,
            ]
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Location updated successfully'
        ]);
    }
}