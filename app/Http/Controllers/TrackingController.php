<?php

namespace App\Http\Controllers;

use App\Models\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TrackingController extends Controller
{
    /**
     * Display the tracking page for a specific command.
     */
    public function show(Command $command)
    {
        // Check if user is authorized to view this tracking page
        if (!$this->canViewTracking(auth()->user(), $command)) {
            abort(403, 'You are not authorized to view this tracking page');
        }
        
        $user = auth()->user();
        $userRole = $user->role;
        
        // Get default coordinates for Nador
        $defaultCoords = [35.1681, -2.9330];
        
        // Geocode pickup and delivery addresses
        $pickupCoords = $this->geocodeAddress($command->pickup_address) ?: ['lat' => $defaultCoords[0], 'lng' => $defaultCoords[1]];
        $deliveryCoords = $this->geocodeAddress($command->delivery_address) ?: ['lat' => $defaultCoords[0], 'lng' => $defaultCoords[1]];
        
        return view('tracking.track', [
            'command' => $command,
            'userRole' => $userRole,
            'pickupCoords' => [$pickupCoords['lat'], $pickupCoords['lng']],
            'deliveryCoords' => [$deliveryCoords['lat'], $deliveryCoords['lng']]
        ]);
    }
    
    /**
     * Update the livreur's location for a command.
     */
    public function updateLivreurLocation(Request $request, Command $command)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        
        // Check if user is the livreur for this command
        $user = auth()->user();
        if (!$user->isLivreur() || $command->livreur_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Store the location in user metadata
        $metadata = $user->metadata ?? [];
        $metadata['location'] = [
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'updated_at' => now()->toIso8601String()
        ];
        $user->metadata = $metadata;
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Location updated'
        ]);
    }
    
    /**
     * Update the client's location for a command.
     */
    public function updateClientLocation(Request $request, Command $command)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        
        // Check if user is the client for this command
        $user = auth()->user();
        if (!$user->isClient() || $command->client_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Store the location in user metadata
        $metadata = $user->metadata ?? [];
        $metadata['location'] = [
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'updated_at' => now()->toIso8601String()
        ];
        $user->metadata = $metadata;
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Location updated'
        ]);
    }
    
    /**
     * Check if the user is authorized to view the tracking page
     */
    private function canViewTracking($user, Command $command)
    {
        // Client can only view their own commands
        if ($user->isClient()) {
            return $command->client_id === $user->id;
        }
        
        // Livreur can only view commands assigned to them
        if ($user->isLivreur()) {
            return $command->livreur_id === $user->id;
        }
        
        // Admin can view all commands
        return $user->isAdmin();
    }
    
    /**
     * Geocode an address to get coordinates.
     */
    private function geocodeAddress($address)
    {
        // Default coordinates (Morocco center) in case geocoding fails
        $defaultLat = 31.7917;
        $defaultLng = -7.0926;
        
        // If geocoding fails or returns empty results, check for known cities
        $cities = [
            'nador' => ['lat' => 35.1681, 'lng' => -2.9330],
            'casablanca' => ['lat' => 33.5731, 'lng' => -7.5898],
            'rabat' => ['lat' => 34.0209, 'lng' => -6.8416],
            'marrakech' => ['lat' => 31.6295, 'lng' => -7.9811],
            'fes' => ['lat' => 34.0181, 'lng' => -5.0078],
            'tanger' => ['lat' => 35.7595, 'lng' => -5.8340],
            'agadir' => ['lat' => 30.4278, 'lng' => -9.5981],
            'meknes' => ['lat' => 33.8731, 'lng' => -5.5407],
            'oujda' => ['lat' => 34.6805, 'lng' => -1.9005],
            'kenitra' => ['lat' => 34.2610, 'lng' => -6.5802],
            'tetouan' => ['lat' => 35.5889, 'lng' => -5.3626],
            'safi' => ['lat' => 32.2994, 'lng' => -9.2372],
            'mohammedia' => ['lat' => 33.6861, 'lng' => -7.3850],
            'el jadida' => ['lat' => 33.2316, 'lng' => -8.5007],
            'taza' => ['lat' => 34.2100, 'lng' => -4.0100],
        ];
        
        // Special case for testing - if we're in Nador, use precise coordinates
        if (strpos(strtolower($address), 'nador') !== false) {
            \Log::info('Using Nador coordinates for address: ' . $address);
            return [
                'lat' => 35.1681, 
                'lng' => -2.9330
            ];
        }
        
        // Check if address contains any known city
        $addressLower = strtolower($address);
        foreach ($cities as $city => $coordinates) {
            if (strpos($addressLower, $city) !== false) {
                // Add small random offset to avoid all markers being at exact same spot
                return [
                    'lat' => $coordinates['lat'] + (mt_rand(-20, 20) / 1000),
                    'lng' => $coordinates['lng'] + (mt_rand(-20, 20) / 1000)
                ];
            }
        }
        
        // Try to geocode using Nominatim (OpenStreetMap) API
        $encodedAddress = urlencode($address . ', Morocco'); // Append country to improve results
        $url = "https://nominatim.openstreetmap.org/search?format=json&limit=1&q={$encodedAddress}";
        
        $options = [
            'http' => [
                'header' => "User-Agent: CoDelivery-App/1.0\r\n",
                'method' => 'GET'
            ]
        ];
        
        $context = stream_context_create($options);
        
        try {
            $response = file_get_contents($url, false, $context);
            $data = json_decode($response, true);
            
            if (!empty($data)) {
                return [
                    'lat' => (float) $data[0]['lat'],
                    'lng' => (float) $data[0]['lon']
                ];
            }
        } catch (\Exception $e) {
            // Log the error but continue with default coordinates
            \Log::error('Geocoding error: ' . $e->getMessage());
        }
        
        // Return default coordinates with random offset
        return [
            'lat' => $defaultLat + (mt_rand(-100, 100) / 1000),
            'lng' => $defaultLng + (mt_rand(-100, 100) / 1000)
        ];
    }
    
    /**
     * Calculate distance between two points using Haversine formula.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the earth in km
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c; // Distance in km
        
        return $distance;
    }
}
