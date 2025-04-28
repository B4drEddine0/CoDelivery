<?php

namespace App\Http\Controllers;

use App\Models\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TrackingController extends Controller
{
    /**
     * Geocode an address to get coordinates using Mapbox API.
     */
    public function geocodeAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $address = $request->input('address');
        $mapboxToken = config('services.mapbox.public_token');
        // Use Mapbox Geocoding API
        $url = "https://api.mapbox.com/geocoding/v5/mapbox.places/" . urlencode($address) . ".json?access_token=" . $mapboxToken . "&limit=1";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);
        if (isset($data['features']) && count($data['features']) > 0) {
            $feature = $data['features'][0];
            $coordinates = $feature['center']; // [longitude, latitude]
            return response()->json([
                'success' => true,
                'coordinates' => [
                    'lat' => $coordinates[1],
                    'lng' => $coordinates[0],
                ],
                'place_name' => $feature['place_name'],
            ]);
        }
        // Fallback to default coordinates (Morocco)
        return response()->json([
            'success' => false,
            'coordinates' => [
                'lat' => 31.7917,
                'lng' => -7.0926,
            ],
            'message' => 'Could not geocode address. Using default coordinates.',
        ]);
    }

    /**
     * Update client or livreur location for a specific command.
     */
    public function updateLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'command_id' => 'required|exists:commands,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|in:client,livreur',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $commandId = $request->input('command_id');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $type = $request->input('type');
        $user = Auth::user();

        $command = Command::findOrFail($commandId);

        // Check if user has permission to update this command's location
        if (($type === 'client' && $command->client_id !== $user->id) || 
            ($type === 'livreur' && $command->livreur_id !== $user->id)) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        // Update the appropriate location
        if ($type === 'client') {
            $command->update([
                'client_latitude' => $latitude,
                'client_longitude' => $longitude,
                'client_location_updated_at' => now(),
            ]);
        } else {
            $command->update([
                'livreur_latitude' => $latitude,
                'livreur_longitude' => $longitude,
                'livreur_location_updated_at' => now(),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Location updated successfully']);
    }

    /**
     * Get the current location data for a command.
     */
    public function getLocationData(Request $request, Command $command)
    {
        $user = Auth::user();

        // Security check: Only the client who created the command or the livreur assigned to it can access location data
        if ($user->id !== $command->client_id && $user->id !== $command->livreur_id) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        return response()->json([
            'success' => true,
            'command' => [
                'id' => $command->id,
                'status' => $command->status,
                'pickup' => [
                    'lat' => $command->pickup_latitude,
                    'lng' => $command->pickup_longitude,
                    'address' => $command->pickup_address
                ],
                'delivery' => [
                    'lat' => $command->delivery_latitude,
                    'lng' => $command->delivery_longitude,
                    'address' => $command->delivery_address
                ],
                'livreur' => [
                    'lat' => $command->livreur_latitude,
                    'lng' => $command->livreur_longitude,
                    'updated_at' => $command->livreur_location_updated_at ? $command->livreur_location_updated_at->diffForHumans() : null
                ],
                'client' => [
                    'lat' => $command->client_latitude,
                    'lng' => $command->client_longitude,
                    'updated_at' => $command->client_location_updated_at ? $command->client_location_updated_at->diffForHumans() : null
                ]
            ]
        ]);
    }

    /**
     * Display the tracking view for a specific command.
     */
    public function showTrackingView(Command $command)
    {
        $user = Auth::user();

        // Security check
        if ($user->id !== $command->client_id && $user->id !== $command->livreur_id) {
            return redirect()->route('dashboard')->with('error', 'Vous n\'êtes pas autorisé à accéder à cette page.');
        }

        $isClient = $user->id === $command->client_id;
        $mapboxToken = config('services.mapbox.public_token');
        $firebaseConfig = [
            'apiKey' => config('services.firebase.api_key'),
            'authDomain' => config('services.firebase.auth_domain'),
            'databaseURL' => config('services.firebase.database_url'),
            'projectId' => config('services.firebase.project_id'),
            'storageBucket' => config('services.firebase.storage_bucket'),
            'messagingSenderId' => config('services.firebase.messaging_sender_id'),
            'appId' => config('services.firebase.app_id')
        ];

        return view('tracking.show', compact('command', 'isClient', 'mapboxToken', 'firebaseConfig'));
    }
}
