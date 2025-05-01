<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Suivi de commande - CoDelivery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css' rel='stylesheet' />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        #map {
            width: 100%;
            height: 100%;
            border-radius: 0.5rem;
        }
        
        .map-container {
            position: relative;
            height: 500px;
        }
        
        .client-marker, .livreur-marker, .pickup-marker, .delivery-marker {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            border: 3px solid white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }
        
        .livreur-marker {
            background-color: #ea580c; /* orange-600 */
        }
        
        .pickup-marker {
            background-color: #10b981; /* green-500 */
        }
        
        .delivery-marker {
            background-color: #ef4444; /* red-500 */
        }

        .mapboxgl-popup {
            max-width: 200px;
        }

        .mapboxgl-popup-content {
            padding: 10px 15px;
            border-radius: 8px;
        }
        
        .pulse-animation {
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(234, 88, 12, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(234, 88, 12, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(234, 88, 12, 0);
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-gradient-to-r from-orange-800 to-orange-950 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-2">
                    <a href="{{ $isClient ? route('client.dashboard') : route('livreur.dashboard') }}" class="flex items-center space-x-2">
                        <svg class="w-10 h-10" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Location Pin -->
                            <path d="M32 4c-11 0-20 9-20 20 0 11 20 36 20 36s20-25 20-36c0-11-9-20-20-20z" fill="#EA580C"/>
                            <!-- Inner Circle -->
                            <circle cx="32" cy="24" r="12" fill="#FB923C"/>
                            <!-- Package Icon -->
                            <rect x="24" y="18" width="16" height="12" fill="#FFFFFF"/>
                            <path d="M24 22h16M32 18v12" stroke="#EA580C" stroke-width="1.5"/>
                            <!-- Motion Lines -->
                            <path d="M14 44l-6 6M50 44l6 6" stroke="#FB923C" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                        <span class="text-xl font-bold">CoDelivery</span>
                    </a>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ $isClient ? route('client.dashboard') : route('livreur.dashboard') }}" class="text-white hover:text-orange-300 transition-colors">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Retour au tableau de bord
                    </a>
                    
                    <div class="relative">
                        <span class="text-sm text-orange-300">Suivi de commande #{{ $command->id }}</span>
                    </div>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button class="text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold mb-2">Suivi de la commande</h1>
            <div class="flex flex-wrap items-center gap-2">
                <div class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-sm">
                    {{ ucfirst($command->service_type) }}
                </div>
                <div class="bg-{{ $command->status == 'in_progress' ? 'blue' : ($command->status == 'accepted' ? 'green' : 'gray') }}-100 text-{{ $command->status == 'in_progress' ? 'blue' : ($command->status == 'accepted' ? 'green' : 'gray') }}-600 px-3 py-1 rounded-full text-sm">
                    @if($command->status == 'accepted')
                        Commande acceptée
                    @elseif($command->status == 'in_progress')
                        En cours de livraison
                    @elseif($command->status == 'delivered')
                        Livrée
                    @elseif($command->status == 'cancelled')
                        Annulée
                    @else
                        En attente
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">{{ $command->title }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Command Details -->
                    <div>
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500 mb-1">Description</p>
                            <p>{{ $command->description ?: 'Aucune description' }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500 mb-1">Lieu de ramassage</p>
                            <div class="flex items-start">
                                <div class="mt-1 mr-2">
                                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-location-dot text-green-600 text-xs"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="font-medium">{{ $command->establishment_name }}</p>
                                    <p class="text-sm text-gray-600">{{ $command->pickup_address }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500 mb-1">Lieu de livraison</p>
                            <div class="flex items-start">
                                <div class="mt-1 mr-2">
                                    <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-flag-checkered text-red-600 text-xs"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ $command->delivery_address }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Livreur Info -->
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-2">Détails du livreur</p>
                        
                        <div class="space-y-3">
                            @if($command->livreur)
                            <div class="flex items-center justify-between px-4 py-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="font-semibold text-orange-600 text-sm">
                                            {{ substr($command->livreur->first_name, 0, 1) }}{{ substr($command->livreur->last_name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium">{{ $command->livreur->first_name }} {{ $command->livreur->last_name }}</p>
                                        <p class="text-xs text-gray-500">Livreur</p>
                                    </div>
                                </div>
                                <div class="flex items-center" id="livreur-status-indicator">
                                    <div class="h-2 w-2 bg-gray-300 rounded-full mr-2"></div>
                                    <span class="text-xs text-gray-500">Statut inconnu</span>
                                </div>
                            </div>
                            @else
                            <div class="flex items-center justify-between px-4 py-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                        <i class="fa-solid fa-user-slash text-gray-400 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium">Aucun livreur assigné</p>
                                        <p class="text-xs text-gray-500">En attente d'acceptation</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Container -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">Localisation</h2>
                
                <div class="map-container">
                    <div id="map"></div>
                    <div id="loading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 z-10">
                        <div class="flex flex-col items-center">
                            <div class="mb-2">
                                <svg class="animate-spin h-8 w-8 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-600">Chargement de la carte...</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 flex flex-wrap gap-4">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-orange-600 rounded-full mr-2"></div>
                        <span class="text-sm">Livreur</span>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-sm">Lieu de ramassage</span>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-red-500 rounded-full mr-2"></div>
                        <span class="text-sm">Lieu de livraison</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex justify-center gap-4 mb-10">
            <a href="{{ $isClient ? route('client.commands') : route('livreur.commands') }}" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                Retour aux commandes
            </a>
            
            @if(!$isClient)
            <div id="location-status" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg flex items-center">
                <i class="fa-solid fa-circle-notch fa-spin mr-2 text-orange-500"></i>
                <span>Localisation en cours...</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Mapbox and Firebase Scripts -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js'></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-database-compat.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if Mapbox token exists or use a fallback
            let mapboxToken = '{{ $mapboxToken }}';
            
            if (!mapboxToken || mapboxToken === '') {
                // Use a fallback public demo token
                mapboxToken = 'pk.eyJ1IjoiYmFkcmVkZGluZTAwIiwiYSI6ImNsdzJ0cDJ1bTBtMnQyaW11NjBxczE3Z2kifQ.ockRcbgDpqVyMLsAv_tMgw';
            }
            
            // Declare important variables at the top level
            let map;
            let database;
            let commandRef;
            let livreurLocationRef;
            let locationWatchId = null;
            
            try {
                // Initialize Mapbox
                mapboxgl.accessToken = mapboxToken;
                
                // Initialize map
                map = new mapboxgl.Map({
                    container: 'map',
                    style: 'mapbox://styles/mapbox/streets-v12',
                    // If delivery coordinates exist, center on delivery location, otherwise use pickup or default
                    @if($command->delivery_latitude && $command->delivery_longitude)
                    center: [{{ $command->delivery_longitude }}, {{ $command->delivery_latitude }}],
                    @elseif($command->pickup_latitude && $command->pickup_longitude)
                    center: [{{ $command->pickup_longitude }}, {{ $command->pickup_latitude }}],
                    @else
                    center: [-2.9287, 35.1698], // Nador center default
                    @endif
                    zoom: 13
                });
                
                // Create markers elements
                const createMarkerElement = (type) => {
                    const element = document.createElement('div');
                    element.className = `${type}-marker`;
                    
                    if (type === 'livreur') {
                        element.innerHTML = '<i class="fa-solid fa-motorcycle"></i>';
                        element.classList.add('pulse-animation');
                    } else if (type === 'pickup') {
                        element.innerHTML = '<i class="fa-solid fa-store"></i>';
                    } else if (type === 'delivery') {
                        element.innerHTML = '<i class="fa-solid fa-flag-checkered"></i>';
                    }
                    
                    return element;
                };
                
                // Initialize livreur marker
                const livreurMarker = new mapboxgl.Marker({
                    element: createMarkerElement('livreur')
                }).setPopup(new mapboxgl.Popup().setHTML('<p class="font-medium">Livreur</p><p class="text-sm text-gray-600">{{ $command->livreur ? $command->livreur->first_name . " " . $command->livreur->last_name : "Non assigné" }}</p>'));
                
                // Add pickup marker
                @if($command->pickup_latitude && $command->pickup_longitude)
                const pickupMarker = new mapboxgl.Marker({
                    element: createMarkerElement('pickup')
                })
                .setLngLat([{{ $command->pickup_longitude }}, {{ $command->pickup_latitude }}])
                .setPopup(new mapboxgl.Popup().setHTML('<p class="font-medium">Lieu de ramassage</p><p class="text-sm text-gray-600">{{ $command->establishment_name }}</p><p class="text-xs text-gray-500">{{ $command->pickup_address }}</p>'))
                .addTo(map);
                @endif
                
                // Add delivery marker
                @if($command->delivery_latitude && $command->delivery_longitude)
                // Add detailed logging to verify exact coordinates are being used
                console.log('Using exact delivery coordinates from database:', {{ $command->delivery_longitude }}, {{ $command->delivery_latitude }});
                console.log('Original delivery address:', '{{ $command->delivery_address }}');

                const deliveryMarker = new mapboxgl.Marker({
                    element: createMarkerElement('delivery')
                })
                .setLngLat([{{ $command->delivery_longitude }}, {{ $command->delivery_latitude }}])
                .setPopup(new mapboxgl.Popup().setHTML('<p class="font-medium">Lieu de livraison</p><p class="text-sm text-gray-600">{{ $command->delivery_address }}</p><p class="text-xs">Coordonnées exactes: {{ $command->delivery_latitude }}, {{ $command->delivery_longitude }}</p>'))
                .addTo(map);
                @else
                // If we don't have exact coordinates, try to geocode the address
                console.log('No delivery coordinates found, attempting to geocode:', '{{ $command->delivery_address }}');
                const geocodeDeliveryAddress = async () => {
                    try {
                        // Skip geocoding if we already have exact coordinates from the database
                        @if($command->delivery_latitude && $command->delivery_longitude)
                        console.log('Skipping geocoding - exact coordinates already exist:', {{ $command->delivery_latitude }}, {{ $command->delivery_longitude }});
                        return;
                        @endif
                        
                        // Show geocoding status
                        showNotification('Recherche de l\'adresse de livraison...', 'info');
                        
                        const geocodeUrl = `https://api.mapbox.com/geocoding/v5/mapbox.places/{{ urlencode($command->delivery_address) }}.json?access_token=${mapboxgl.accessToken}&country=ma&limit=1`;
                        console.log('Geocoding URL:', geocodeUrl);
                        
                        const response = await fetch(geocodeUrl);
                        if (!response.ok) {
                            throw new Error(`Geocoding error: ${response.status} ${response.statusText}`);
                        }
                        
                        const data = await response.json();
                        console.log('Geocoding response:', data);
                        
                        if (data.features && data.features.length > 0) {
                            const coordinates = data.features[0].center; // [lng, lat]
                            console.log('Found coordinates:', coordinates);
                            
                            // Add delivery marker with geocoded coordinates
                            const deliveryMarker = new mapboxgl.Marker({
                                element: createMarkerElement('delivery')
                            })
                            .setLngLat(coordinates)
                            .setPopup(new mapboxgl.Popup().setHTML('<p class="font-medium">Lieu de livraison</p><p class="text-sm text-gray-600">{{ $command->delivery_address }}</p><p class="text-xs text-gray-500">(Coordonnées approximatives)</p>'))
                            .addTo(map);
                            
                            // Show success message
                            showNotification('Adresse de livraison localisée', 'success');
                            
                            // Update the fit map function to include this marker
                            setTimeout(fitMapToMarkers, 500);
                            
                            // Save these coordinates to database only if we don't have exact coordinates
                            @if(!$command->delivery_latitude || !$command->delivery_longitude)
                            console.log('Saving geocoded coordinates to database');
                            saveDeliveryCoordinates(coordinates[1], coordinates[0]);
                            @else
                            console.log('Not saving geocoded coordinates - exact coordinates already exist');
                            @endif
                        } else {
                            console.error('No features found in geocoding response');
                            showNotification('Impossible de localiser l\'adresse de livraison', 'error');
                            
                            // Try with a more generic search
                            fallbackGeocoding();
                        }
                    } catch (error) {
                        console.error('Error geocoding delivery address:', error);
                        showNotification('Erreur lors de la localisation de l\'adresse', 'error');
                        
                        // Try with a more generic search
                        fallbackGeocoding();
                    }
                };
                
                // Fallback geocoding with just the city name
                const fallbackGeocoding = async () => {
                    try {
                        console.log('Using fallback geocoding with city name');
                        
                        // Try to extract a city name from the address or use Nador as fallback
                        let cityName = '{{ $command->delivery_address }}'.match(/Nador|Beni\s*Ensar|Selouane|Zeghanghane|Al\s*Aroui/i);
                        cityName = cityName ? cityName[0] : 'Nador';
                        
                        const geocodeUrl = `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(cityName)}.json?access_token=${mapboxgl.accessToken}&country=ma&limit=1`;
                        
                        const response = await fetch(geocodeUrl);
                        const data = await response.json();
                        
                        if (data.features && data.features.length > 0) {
                            const coordinates = data.features[0].center; // [lng, lat]
                            console.log('Found city coordinates:', coordinates);
                            
                            // Add delivery marker with geocoded coordinates
                            const deliveryMarker = new mapboxgl.Marker({
                                element: createMarkerElement('delivery')
                            })
                            .setLngLat(coordinates)
                            .setPopup(new mapboxgl.Popup().setHTML('<p class="font-medium">Lieu de livraison</p><p class="text-sm text-gray-600">{{ $command->delivery_address }}</p><p class="text-xs text-gray-500">(Position approximative)</p>'))
                            .addTo(map);
                            
                            // Don't save these coordinates as they're too approximate
                            setTimeout(fitMapToMarkers, 500);
                        } else {
                            console.error('Fallback geocoding failed');
                            
                            // Use fixed Nador city center coordinates as last resort
                            const nadorCoords = [-2.9287, 35.1698];
                            const deliveryMarker = new mapboxgl.Marker({
                                element: createMarkerElement('delivery')
                            })
                            .setLngLat(nadorCoords)
                            .setPopup(new mapboxgl.Popup().setHTML('<p class="font-medium">Lieu de livraison</p><p class="text-sm text-gray-600">{{ $command->delivery_address }}</p><p class="text-xs text-gray-500">(Position approximative - Centre ville)</p>'))
                            .addTo(map);
                            
                            setTimeout(fitMapToMarkers, 500);
                        }
                    } catch (error) {
                        console.error('Error in fallback geocoding:', error);
                    }
                };
                
                // Function to save delivery coordinates to database
                const saveDeliveryCoordinates = (lat, lng) => {
                    try {
                    fetch('/api/update-delivery-coordinates', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            command_id: {{ $command->id }},
                            latitude: lat,
                            longitude: lng
                        })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Delivery coordinates saved:', data);
                        })
                        .catch(error => {
                            console.error('Error saving delivery coordinates:', error);
                        });
                    } catch (error) {
                        console.error('Error in saveDeliveryCoordinates:', error);
                    }
                };
                
                // Try to geocode the delivery address
                geocodeDeliveryAddress();
                @endif
                
                // Initialize Firebase
                let firebaseConfig = {
                    apiKey: "{{ $firebaseConfig['apiKey'] }}",
                    authDomain: "{{ $firebaseConfig['authDomain'] }}",
                    databaseURL: "{{ $firebaseConfig['databaseURL'] }}",
                    projectId: "{{ $firebaseConfig['projectId'] }}",
                    storageBucket: "{{ $firebaseConfig['storageBucket'] }}",
                    messagingSenderId: "{{ $firebaseConfig['messagingSenderId'] }}",
                    appId: "{{ $firebaseConfig['appId'] }}"
                };
                
                // Fallback if Firebase config is invalid
                if (!firebaseConfig.databaseURL || firebaseConfig.databaseURL === '') {
                    firebaseConfig = {
                        apiKey: "AIzaSyA8TUEx1t77QwuILMnRErUCCnQ9J2DyAYY",
                        authDomain: "codelivery-demo.firebaseapp.com",
                        databaseURL: "https://codelivery-demo-default-rtdb.firebaseio.com",
                        projectId: "codelivery-demo",
                        storageBucket: "codelivery-demo.appspot.com",
                        messagingSenderId: "123456789012",
                        appId: "1:123456789012:web:0123456789abcdef"
                    };
                }
                
                // Initialize Firebase
                try {
                    firebase.initializeApp(firebaseConfig);
                    database = firebase.database();
                    
                    // Reference to livreur location
                    commandRef = database.ref(`commands/{{ $command->id }}`);
                    livreurLocationRef = commandRef.child('locations').child('livreur');
                
                    // Listen for livreur location updates
                livreurLocationRef.on('value', (snapshot) => {
                    const data = snapshot.val();
                    
                    if (data && data.lat && data.lng) {
                            // Update livreur marker
                        livreurMarker.setLngLat([data.lng, data.lat]).addTo(map);
                        
                        // Update status indicator
                        updateLivreurStatus(true, formatTimestamp(data.timestamp));
                        
                            // Fit map to show all markers
                            fitMapToMarkers();
                        }
                    }, (error) => {
                        console.error('Error with livreur location listener:', error);
                    });
                            
                    // Initialize livreur location from database if available
                    @if($command->livreur_id && $command->livreur_latitude && $command->livreur_longitude)
                    livreurMarker.setLngLat([{{ $command->livreur_longitude }}, {{ $command->livreur_latitude }}]).addTo(map);
                    updateLivreurStatus(true, '{{ $command->livreur_location_updated_at ? $command->livreur_location_updated_at->format("H:i") : "Initial" }}');
                    @endif
                    
                    // If user is livreur, start automatic location updating
                    if (!{{ $isClient ? 'true' : 'false' }}) {
                        startLocationTracking();
                    }
                    
                                } catch (error) {
                    console.error('Error initializing Firebase:', error);
                    document.getElementById('loading').innerHTML = `
                        <div class="flex flex-col items-center">
                            <div class="text-red-500 mb-2">
                                <i class="fa-solid fa-exclamation-triangle fa-2x"></i>
                            </div>
                            <p class="text-gray-600 font-medium">Erreur de connexion</p>
                            <p class="text-gray-500 text-sm mt-2">Détail: ${error.message}</p>
                        </div>
                    `;
                }
                
                // Hide loading indicator
                map.on('load', function() {
                    document.getElementById('loading').style.display = 'none';
                        fitMapToMarkers();
                });
                
                // Add map controls
                map.addControl(new mapboxgl.NavigationControl(), 'top-right');
                map.addControl(new mapboxgl.FullscreenControl(), 'top-right');
                
                // Function to start automatic location tracking for livreur
                function startLocationTracking() {
                    if (!navigator.geolocation) {
                        updateLocationStatus('error', 'Géolocalisation non prise en charge par votre navigateur');
                        return;
                    }
                    
                    // Stop any existing tracking
                    if (locationWatchId !== null) {
                        navigator.geolocation.clearWatch(locationWatchId);
                    }
                    
                    // Get location once immediately
                    navigator.geolocation.getCurrentPosition(
                        updateLivreurLocation, 
                        (error) => {
                            console.error('Geolocation error:', error);
                            updateLocationStatus('error', 'Impossible d\'obtenir votre position');
                        }, 
                        { enableHighAccuracy: true }
                    );
                    
                    // Then start watching position (updates periodically)
                    locationWatchId = navigator.geolocation.watchPosition(
                        updateLivreurLocation,
                        (error) => {
                            console.error('Geolocation watch error:', error);
                            updateLocationStatus('error', 'Erreur de suivi de position');
                        },
                        { enableHighAccuracy: true, maximumAge: 15000 }
                    );
                    
                    // Update status to indicate tracking is active
                    updateLocationStatus('active', 'Localisation active');
                                }
                                
                // Function to update livreur location
                function updateLivreurLocation(position) {
                    const { latitude, longitude } = position.coords;
                                    
                    // Update in Firebase
                    if (livreurLocationRef) {
                                    livreurLocationRef.set({
                            lat: latitude,
                            lng: longitude,
                                        timestamp: firebase.database.ServerValue.TIMESTAMP
                                    });
                                }
                                
                    // Update in database
                    fetch('/api/update-location', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            command_id: {{ $command->id }},
                            latitude,
                            longitude,
                            type: 'livreur'
                        })
                    }).catch(error => {
                        console.error('Error updating location in database:', error);
                    });
                    
                    // Update status indicator
                    updateLocationStatus('updated', 'Position mise à jour');
                }
                
                // Update the location status indicator
                function updateLocationStatus(status, message) {
                    const statusElement = document.getElementById('location-status');
                    if (!statusElement) return;
                    
                    switch (status) {
                        case 'active':
                            statusElement.className = 'px-6 py-3 bg-green-100 text-green-700 rounded-lg flex items-center';
                            statusElement.innerHTML = '<i class="fa-solid fa-location-crosshairs mr-2"></i><span>' + message + '</span>';
                            break;
                        case 'updated':
                            statusElement.className = 'px-6 py-3 bg-green-100 text-green-700 rounded-lg flex items-center';
                            statusElement.innerHTML = '<i class="fa-solid fa-check-circle mr-2"></i><span>' + message + '</span>';
                            // Reset to active after 2 seconds
                        setTimeout(() => {
                                updateLocationStatus('active', 'Localisation active');
                            }, 2000);
                            break;
                        case 'error':
                            statusElement.className = 'px-6 py-3 bg-red-100 text-red-700 rounded-lg flex items-center';
                            statusElement.innerHTML = '<i class="fa-solid fa-exclamation-triangle mr-2"></i><span>' + message + '</span>';
                            break;
                        default:
                            statusElement.className = 'px-6 py-3 bg-gray-100 text-gray-700 rounded-lg flex items-center';
                            statusElement.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin mr-2 text-orange-500"></i><span>' + message + '</span>';
                    }
                }
                
                // Helper functions
                function updateLivreurStatus(isOnline, lastUpdated = null) {
                    const indicator = document.getElementById('livreur-status-indicator');
                    if (!indicator) return;
                    
                    indicator.innerHTML = `
                        <div class="h-2 w-2 ${isOnline ? 'bg-green-500' : 'bg-gray-300'} rounded-full mr-2"></div>
                        <span class="text-xs ${isOnline ? 'text-green-600' : 'text-gray-500'}">${isOnline ? 'En ligne' : 'Hors ligne'}${lastUpdated ? ' ('+lastUpdated+')' : ''}</span>
                    `;
                }
                
                function formatTimestamp(timestamp) {
                    if (!timestamp) return '';
                    const date = new Date(timestamp);
                    return `${date.getHours()}:${String(date.getMinutes()).padStart(2, '0')}`;
                    }
                
                function fitMapToMarkers() {
                    try {
                        const bounds = new mapboxgl.LngLatBounds();
                        let hasMarkers = false;
                        
                        @if($command->pickup_latitude && $command->pickup_longitude)
                        // Add pickup marker to bounds
                        console.log('Including pickup marker in bounds:', {{ $command->pickup_longitude }}, {{ $command->pickup_latitude }});
                        bounds.extend([{{ $command->pickup_longitude }}, {{ $command->pickup_latitude }}]);
                        hasMarkers = true;
                        @endif
                        
                        @if($command->delivery_latitude && $command->delivery_longitude)
                        // Add delivery marker to bounds - these are the exact coordinates selected by the client
                        console.log('Including delivery marker in bounds:', {{ $command->delivery_longitude }}, {{ $command->delivery_latitude }});
                        bounds.extend([{{ $command->delivery_longitude }}, {{ $command->delivery_latitude }}]);
                        hasMarkers = true;
                        @endif
                        
                        if (livreurMarker._lngLat) {
                            // Add livreur marker to bounds
                            console.log('Including livreur marker in bounds:', livreurMarker._lngLat.lng, livreurMarker._lngLat.lat);
                            bounds.extend([livreurMarker._lngLat.lng, livreurMarker._lngLat.lat]);
                            hasMarkers = true;
                        }
                        
                        if (hasMarkers && !bounds.isEmpty()) {
                            map.fitBounds(bounds, {
                                padding: 70,
                                maxZoom: 15,
                                duration: 1000
                            });
                            console.log('Map fitted to all markers');
                        } else {
                            console.warn('No markers to fit map to');
                        }
                    } catch (error) {
                        console.error('Error fitting map to markers:', error);
                    }
                }
                
                // Show a notification
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
                    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg z-50 transition-all duration-500 ${
                type === 'success' ? 'bg-green-100 text-green-800 border-l-4 border-green-500' :
                type === 'error' ? 'bg-red-100 text-red-800 border-l-4 border-red-500' :
                'bg-blue-100 text-blue-800 border-l-4 border-blue-500'
            }`;
                    
                    notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                        notification.classList.add('opacity-0');
            setTimeout(() => {
                        notification.remove();
                }, 500);
                    }, 3000);
        }
                
            } catch (error) {
                console.error('Error initializing map:', error);
                document.getElementById('loading').innerHTML = `
                    <div class="flex flex-col items-center">
                        <div class="text-red-500 mb-2">
                            <i class="fa-solid fa-exclamation-triangle fa-2x"></i>
                        </div>
                        <p class="text-gray-600 font-medium">Erreur de chargement</p>
                        <p class="text-gray-500 text-sm mt-2">Détail: ${error.message}</p>
                    </div>
                `;
            }
        });
    </script>
</body>
</html>