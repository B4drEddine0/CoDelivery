<?php

namespace App\Http\Controllers;

use App\Events\LivreurLocationUpdated;
use App\Models\Command;
use App\Models\LocationTracking;
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
        $user = Auth::user();
        
        // Check if the user is authorized to view this tracking page
        if (!$this->canViewTracking($user, $command)) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'êtes pas autorisé à suivre cette commande.');
        }
        
        // Get or create location tracking data
        $locationTracking = $command->locationTracking ?? $this->initializeLocationTracking($command);
        
        return view('tracking.track', [
            'command' => $command,
            'locationTracking' => $locationTracking
        ]);
    }
    
    /**
     * Update the livreur's location for a specific command.
     */
    public function updateLivreurLocation(Request $request, Command $command)
    {
        $user = Auth::user();
        
        // Only the assigned livreur can update their location
        if (!$user->isLivreur() || $command->livreur_id !== $user->id) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        
        $locationTracking = $command->locationTracking;
        
        if (!$locationTracking) {
            $locationTracking = $this->initializeLocationTracking($command);
        }
        
        $locationTracking->update([
            'livreur_latitude' => $request->latitude,
            'livreur_longitude' => $request->longitude,
            'location_updated_at' => now(),
        ]);
        
        // Broadcast the location update via Pusher
        event(new LivreurLocationUpdated(
            $command->id,
            $request->latitude,
            $request->longitude,
            $locationTracking->estimated_delivery_time
        ));
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Initialize location tracking for a command.
     */
    private function initializeLocationTracking(Command $command)
    {
        // Geocode the pickup address
        $pickupCoordinates = $this->geocodeAddress($command->pickup_address);
        $pickupLat = $pickupCoordinates['lat'];
        $pickupLng = $pickupCoordinates['lng'];
        
        // Geocode the delivery address
        $deliveryCoordinates = $this->geocodeAddress($command->delivery_address);
        $deliveryLat = $deliveryCoordinates['lat'];
        $deliveryLng = $deliveryCoordinates['lng'];
        
        // For initial setup, livreur starts at pickup location
        $livreurLat = $pickupLat;
        $livreurLng = $pickupLng;
        
        // Calculate estimated delivery time based on distance (rough estimate)
        $distance = $this->calculateDistance($pickupLat, $pickupLng, $deliveryLat, $deliveryLng);
        $estimatedTime = max(10, round($distance * 3)); // Roughly 20 km/h average speed
        
        // Create a simple route between pickup and delivery
        $route = $this->generateRoute($pickupLat, $pickupLng, $deliveryLat, $deliveryLng);
        
        return LocationTracking::create([
            'command_id' => $command->id,
            'pickup_latitude' => $pickupLat,
            'pickup_longitude' => $pickupLng,
            'delivery_latitude' => $deliveryLat,
            'delivery_longitude' => $deliveryLng,
            'livreur_latitude' => $livreurLat,
            'livreur_longitude' => $livreurLng,
            'delivery_route' => $route,
            'estimated_delivery_time' => $estimatedTime,
        ]);
    }
    
    /**
     * Geocode an address to get coordinates.
     */
    private function geocodeAddress($address)
    {
        // Default coordinates (Morocco center) in case geocoding fails
        $defaultLat = 31.7917;
        $defaultLng = -7.0926;
        
        // Try to geocode using Nominatim (OpenStreetMap) API
        $address = urlencode($address . ', Morocco'); // Append country to improve results
        $url = "https://nominatim.openstreetmap.org/search?format=json&limit=1&q={$address}";
        
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
            // Log::error('Geocoding error: ' . $e->getMessage());
        }
        
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
    
    /**
     * Generate a route between two points.
     */
    private function generateRoute($startLat, $startLng, $endLat, $endLng)
    {
        // Create a simple route with intermediate points
        $route = [
            ['lat' => $startLat, 'lng' => $startLng]
        ];
        
        // Add 2-3 intermediate points
        $steps = mt_rand(2, 3);
        for ($i = 1; $i <= $steps; $i++) {
            $ratio = $i / ($steps + 1);
            
            // Add some randomness to make the route more realistic
            $latOffset = (mt_rand(-15, 15) / 1000);
            $lngOffset = (mt_rand(-15, 15) / 1000);
            
            $lat = $startLat + ($endLat - $startLat) * $ratio + $latOffset;
            $lng = $startLng + ($endLng - $startLng) * $ratio + $lngOffset;
            
            $route[] = ['lat' => $lat, 'lng' => $lng];
        }
        
        // Add destination point
        $route[] = ['lat' => $endLat, 'lng' => $endLng];
        
        return $route;
    }
    
    /**
     * Check if the user can view the tracking page for a command.
     */
    private function canViewTracking($user, Command $command)
    {
        // Client can view tracking for their own commands
        if ($user->isClient() && $command->client_id === $user->id) {
            return true;
        }
        
        // Livreur can view tracking for commands assigned to them
        if ($user->isLivreur() && $command->livreur_id === $user->id) {
            return true;
        }
        
        return false;
    }
}
