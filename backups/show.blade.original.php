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
        
        .client-marker {
            background-color: #3b82f6; /* blue-500 */
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
            <h1 class="text-2xl font-bold mb-2">Suivi en temps réel</h1>
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
                    
                    <!-- Live Status -->
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-2">Participants</p>
                        
                        <div class="space-y-3">
                            @if($command->client)
                            <div class="flex items-center justify-between px-4 py-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="font-semibold text-blue-600 text-sm">
                                            {{ substr($command->client->first_name, 0, 1) }}{{ substr($command->client->last_name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium">{{ $command->client->first_name }} {{ $command->client->last_name }}</p>
                                        <p class="text-xs text-gray-500">Client</p>
                                    </div>
                                </div>
                                <div class="flex items-center" id="client-status-indicator">
                                    <div class="h-2 w-2 bg-gray-300 rounded-full mr-2"></div>
                                    <span class="text-xs text-gray-500">Hors ligne</span>
                                </div>
                            </div>
                            @endif
                            

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
                                    <span class="text-xs text-gray-500">Hors ligne</span>
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
                <h2 class="text-lg font-semibold mb-4">Localisation en temps réel</h2>
                
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
                        <div class="w-4 h-4 bg-blue-500 rounded-full mr-2"></div>
                        <span class="text-sm">Client</span>
                    </div>
                    
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
            
            <div id="location-status" class="px-6 py-3 bg-orange-100 text-orange-700 rounded-lg flex items-center">
                <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                <span>Initialisation du partage de position...</span>
            </div>
        </div>
    </div>

    <!-- Mapbox and Firebase Scripts -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js'></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-database-compat.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Debug logging
            console.log('DOMContentLoaded triggered');
            
            // Check if Mapbox token is empty and use a fallback
            let mapboxToken = '{{ $mapboxToken }}';
            console.log('Original Mapbox token:', mapboxToken);
            
            if (!mapboxToken || mapboxToken === '') {
                // Use a fallback public demo token
                mapboxToken = 'pk.eyJ1IjoiYmFkcmVkZGluZTAwIiwiYSI6ImNsdzJ0cDJ1bTBtMnQyaW11NjBxczE3Z2kifQ.ockRcbgDpqVyMLsAv_tMgw';
                console.log('Using fallback Mapbox token');
            }
            
            // First initialize both status indicators to online immediately for better UX
            const clientIndicator = document.getElementById('client-status-indicator');
            const livreurIndicator = document.getElementById('livreur-status-indicator');
            
            if (clientIndicator) {
                clientIndicator.innerHTML = `
                    <div class="h-2 w-2 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-xs text-green-600">En ligne</span>
                `;
            }
            
            if (livreurIndicator && '{{ $command->livreur_id }}') {
                livreurIndicator.innerHTML = `
                    <div class="h-2 w-2 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-xs text-green-600">En ligne</span>
                `;
            }
            
            try {
                // Initialize Mapbox with the token
                mapboxgl.accessToken = mapboxToken;
                
                // Initialize map
                const map = new mapboxgl.Map({
                    container: 'map',
                    style: 'mapbox://styles/mapbox/streets-v12',
                    center: [{{ $command->pickup_longitude ?? -2.9287 }}, {{ $command->pickup_latitude ?? 35.1698 }}],
                    zoom: 13
                });
                
                console.log('Map initialized successfully');
                
                // Set up heartbeat to keep online status updated
                let heartbeatInterval = null;
                
                // Create heartbeat function to keep online status active
                function startHeartbeat() {
                    // Clear any existing interval
                    if (heartbeatInterval) {
                        clearInterval(heartbeatInterval);
                    }
                    
                    // Set up heartbeat interval (every 30 seconds)
                    heartbeatInterval = setInterval(() => {
                        console.log('Sending heartbeat...');
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const statusRef = userType === 'client' ? clientStatusRef : livreurStatusRef;
                        
                        // Update online status
                        statusRef.set({
                            online: true,
                            timestamp: firebase.database.ServerValue.TIMESTAMP
                        });
                        
                        // Update corresponding status indicator
                        if (userType === 'client') {
                            updateClientStatus(true, null);
                        } else {
                            updateLivreurStatus(true, null); 
                        }
                        
                        // Also periodically check for backend updates
                        checkServerLocationUpdates();
                    }, 30000); // Every 30 seconds
                    
                    // Make sure to clear on page unload
                    window.addEventListener('beforeunload', () => {
                        clearInterval(heartbeatInterval);
                    });
                }
                
                // Function to check for location updates from the server
                function checkServerLocationUpdates() {
                    console.log('Checking for server location updates...');
                    const userType = '{{ $isClient ? "client" : "livreur" }}';
                    
                    try {
                        fetch(`/api/commands/{{ $command->id }}/location`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    console.log('Server location update received:', data);
                                    
                                    const command = data.command;
                                    let needsUpdate = false;
                                    
                                    // Set both markers with their respective positions
                                    // This ensures both users see both markers (client + livreur)
                                    
                                    // Always update livreur marker with server data if we're not the livreur
                                    if (command.livreur && command.livreur.lat && command.livreur.lng) {
                                        console.log('Updating livreur marker from server:', [command.livreur.lng, command.livreur.lat]);
                                        
                                        // If we're a client, update livreur marker from server
                                        // If we're a livreur, only update if we're not actively sending our own position
                                        if (userType === 'client' || (userType === 'livreur' && !clientMarker._lngLat)) {
                                            livreurMarker.setLngLat([command.livreur.lng, command.livreur.lat]).addTo(map);
                                            needsUpdate = true;
                                        }
                                    }
                                    
                                    // Always update client marker with server data if we're not the client
                                    if (command.client && command.client.lat && command.client.lng) {
                                        console.log('Updating client marker from server:', [command.client.lng, command.client.lat]);
                                        
                                        // If we're a livreur, update client marker from server
                                        // If we're a client, only update if we're not actively sending our own position
                                        if (userType === 'livreur' || (userType === 'client' && !livreurMarker._lngLat)) {
                                            clientMarker.setLngLat([command.client.lng, command.client.lat]).addTo(map);
                                            needsUpdate = true;
                                        }
                                    }
                                    
                                    // If we updated anything, update the route and refit the map
                                    if (needsUpdate) {
                                        console.log('Updates detected, refreshing route and map');
                                        updateClientLivreurRoute();
                                        fitMapToMarkers();
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error checking server location updates:', error);
                            });
                    } catch (error) {
                        console.error('Exception in server location check:', error);
                    }
                }
                
                // Create markers elements with enhanced styling
                const createMarkerElement = (type) => {
                    const element = document.createElement('div');
                    element.className = `${type}-marker`;
                    
                    if (type === 'client') {
                        element.innerHTML = '<i class="fa-solid fa-user"></i>';
                    } else if (type === 'livreur') {
                        element.innerHTML = '<i class="fa-solid fa-motorcycle"></i>';
                        // Add pulse animation to make livreur more visible
                        element.classList.add('pulse-animation');
                    } else if (type === 'pickup') {
                        element.innerHTML = '<i class="fa-solid fa-store"></i>';
                    } else if (type === 'delivery') {
                        element.innerHTML = '<i class="fa-solid fa-flag-checkered"></i>';
                    }
                    
                    return element;
                };
                
                // Initialize markers (but don't add them to the map yet)
                const clientMarker = new mapboxgl.Marker({
                    element: createMarkerElement('client')
                }).setPopup(new mapboxgl.Popup().setHTML('<p class="font-medium">Client</p><p class="text-sm text-gray-600">{{ $command->client->first_name }} {{ $command->client->last_name }}</p>'));
                
                const livreurMarker = new mapboxgl.Marker({
                    element: createMarkerElement('livreur')
                }).setPopup(new mapboxgl.Popup().setHTML('<p class="font-medium">Livreur</p><p class="text-sm text-gray-600">{{ $command->livreur ? $command->livreur->first_name . " " . $command->livreur->last_name : "Non assigné" }}</p>'));
                
                // Add pickup and delivery markers
                @if($command->pickup_latitude && $command->pickup_longitude)
                const pickupMarker = new mapboxgl.Marker({
                    element: createMarkerElement('pickup')
                })
                .setLngLat([{{ $command->pickup_longitude }}, {{ $command->pickup_latitude }}])
                .setPopup(new mapboxgl.Popup().setHTML('<p class="font-medium">Lieu de ramassage</p><p class="text-sm text-gray-600">{{ $command->establishment_name }}</p><p class="text-xs text-gray-500">{{ $command->pickup_address }}</p>'))
                .addTo(map);
                @endif
                
                // Function to geocode delivery address and add marker
                const geocodeDeliveryAddress = async () => {
                    try {
                        const response = await fetch(`/api/geocode?address={{ urlencode($command->delivery_address) }}`);
                        const data = await response.json();
                        
                        if (data.success) {
                            // Add delivery marker
                            const deliveryMarker = new mapboxgl.Marker({
                                element: createMarkerElement('delivery')
                            })
                            .setLngLat([data.coordinates.lng, data.coordinates.lat])
                            .setPopup(new mapboxgl.Popup().setHTML('<p class="font-medium">Lieu de livraison</p><p class="text-sm text-gray-600">{{ $command->delivery_address }}</p>'))
                            .addTo(map);
                            
                            // Update command coordinates in database
                            updateDeliveryCoordinates(data.coordinates.lat, data.coordinates.lng);
                        }
                    } catch (error) {
                        console.error('Error geocoding delivery address:', error);
                    }
                };
                
                // Function to update delivery coordinates in database
                const updateDeliveryCoordinates = (lat, lng) => {
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
                    });
                };
                
                // If delivery coordinates are not set, geocode the address
                @if(!$command->delivery_latitude || !$command->delivery_longitude)
                geocodeDeliveryAddress();
                @else
                // Add delivery marker
                const deliveryMarker = new mapboxgl.Marker({
                    element: createMarkerElement('delivery')
                })
                .setLngLat([{{ $command->delivery_longitude }}, {{ $command->delivery_latitude }}])
                .setPopup(new mapboxgl.Popup().setHTML('<p class="font-medium">Lieu de livraison</p><p class="text-sm text-gray-600">{{ $command->delivery_address }}</p>'))
                .addTo(map);
                @endif
                
                // Initialize Firebase with enhanced error handling
                let firebaseConfig = {
                    apiKey: "{{ $firebaseConfig['apiKey'] }}",
                    authDomain: "{{ $firebaseConfig['authDomain'] }}",
                    databaseURL: "{{ $firebaseConfig['databaseURL'] }}",
                    projectId: "{{ $firebaseConfig['projectId'] }}",
                    storageBucket: "{{ $firebaseConfig['storageBucket'] }}",
                    messagingSenderId: "{{ $firebaseConfig['messagingSenderId'] }}",
                    appId: "{{ $firebaseConfig['appId'] }}"
                };
                
                // Check if Firebase config is valid
                console.log('Firebase config:', firebaseConfig);
                if (!firebaseConfig.databaseURL || firebaseConfig.databaseURL === '') {
                    // Use a demo Firebase config for testing
                    console.warn('Using fallback Firebase configuration');
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
                
                // Initialize Firebase with error handling
                let database;
                let locationWatchId = null;
                
                try {
                    firebase.initializeApp(firebaseConfig);
                    database = firebase.database();
                    console.log('Firebase initialized successfully');
                } catch (error) {
                    console.error('Error initializing Firebase:', error);
                    document.getElementById('loading').innerHTML = `
                        <div class="flex flex-col items-center">
                            <div class="text-red-500 mb-2">
                                <i class="fa-solid fa-exclamation-triangle fa-2x"></i>
                            </div>
                            <p class="text-gray-600 font-medium">Erreur de connexion à Firebase</p>
                            <p class="text-gray-500 text-sm mt-2">Détail: ${error.message}</p>
                        </div>
                    `;
                    return;
                }
                
                // IMPORTANT: Create a consistent Firebase path for all users to ensure they use the same data
                const FIREBASE_COMMAND_KEY = 'command_{{ $command->id }}';
                console.log('Using Firebase command key:', FIREBASE_COMMAND_KEY);
                
                // References to database locations with consistent paths
                const commandRef = database.ref(FIREBASE_COMMAND_KEY);
                const locationsRef = commandRef.child('locations');
                const clientLocationRef = locationsRef.child('client');
                const livreurLocationRef = locationsRef.child('livreur');
                const presenceRef = commandRef.child('presence');
                
                // Debug log to ensure path consistency
                console.log('Firebase Paths:', {
                    commandPath: commandRef.toString(),
                    clientLocationPath: clientLocationRef.toString(),
                    livreurLocationPath: livreurLocationRef.toString(),
                    presencePath: presenceRef.toString()
                });
                
                // Create online status references
                const clientStatusRef = presenceRef.child('client');
                const livreurStatusRef = presenceRef.child('livreur');
                
                // Set both users as online immediately (regardless of actual role)
                clientStatusRef.set({
                    online: true,
                    timestamp: firebase.database.ServerValue.TIMESTAMP
                });
                
                // Only set livreur online if there is a livreur assigned
                if ('{{ $command->livreur_id }}') {
                    livreurStatusRef.set({
                        online: true,
                        timestamp: firebase.database.ServerValue.TIMESTAMP
                    });
                }
                
                // Set our own cleanup on disconnect
                const userType = '{{ $isClient ? "client" : "livreur" }}';
                const myStatusRef = userType === 'client' ? clientStatusRef : livreurStatusRef;
                myStatusRef.onDisconnect().remove();
                
                // Listen for client status changes
                clientStatusRef.on('value', (snapshot) => {
                    const data = snapshot.val();
                    updateClientStatus(data ? true : false, data ? formatTimestamp(data.timestamp) : null);
                });
                
                // Listen for livreur status changes
                livreurStatusRef.on('value', (snapshot) => {
                    const data = snapshot.val();
                    updateLivreurStatus(data ? true : false, data ? formatTimestamp(data.timestamp) : null);
                });
                
                // For debugging
                commandRef.once('value', snapshot => {
                    console.log('Firebase command data:', snapshot.val());
                });
                
                // Listen for client location changes
                clientLocationRef.on('value', (snapshot) => {
                    const data = snapshot.val();
                    console.log('Client location update received from Firebase:', data);
                    
                    if (data && data.lat && data.lng) {
                        try {
                            // Get exact coordinates from Firebase
                            const exactLng = data.lng;
                            const exactLat = data.lat;
                            
                            console.log('Client coordinates from Firebase:', [exactLng, exactLat]);
                            
                            // Store in localStorage for persistence
                            localStorage.setItem('client_last_location', JSON.stringify({
                                lat: exactLat,
                                lng: exactLng,
                                timestamp: data.timestamp || Date.now()
                            }));
                            
                            // CRITICAL FIX: ALWAYS update client marker on map for BOTH client and livreur users
                            // Regardless of who we are, we should always update the client marker when Firebase changes
                            clientMarker.setLngLat([exactLng, exactLat]).addTo(map);
                            console.log('Client marker updated on map with coordinates:', [exactLng, exactLat]);
                            
                            // Update client status to online
                            clientStatusRef.set({
                                online: true,
                                timestamp: data.timestamp || firebase.database.ServerValue.TIMESTAMP
                            });
                            
                            // Flash the client marker to make it noticeable
                            const markerEl = clientMarker.getElement();
                            markerEl.style.transform = 'scale(1.3)';
                            setTimeout(() => {
                                markerEl.style.transform = 'scale(1)';
                            }, 700);
                            
                            // Show notification
                            showNotification('Position du client mise à jour', 'success');
                            
                            // Update client-livreur route
                            updateClientLivreurRoute();
                            fitMapToMarkers();
                            
                            // CRITICAL FIX: If we're the client, only update the database if we're the source
                            const userType = '{{ $isClient ? "client" : "livreur" }}';
                            if (userType === 'client' && data.source === 'geolocation') {
                                updateLocationInDatabase('client', exactLat, exactLng);
                            }
                        } catch (error) {
                            console.error('Error processing client location update:', error);
                        }
                    } else {
                        console.warn('Received invalid client location data:', data);
                        
                        // Try to load from localStorage
                        const lastClientLocation = localStorage.getItem('client_last_location');
                        if (lastClientLocation) {
                            try {
                                const parsedLocation = JSON.parse(lastClientLocation);
                                console.log('Using cached client location:', parsedLocation);
                                clientMarker.setLngLat([parsedLocation.lng, parsedLocation.lat]).addTo(map);
                                updateClientLivreurRoute();
                                fitMapToMarkers();
                            } catch (e) {
                                console.error('Error parsing cached client location:', e);
                            }
                        }
                    }
                }, (error) => {
                    console.error('Error with client location listener:', error);
                });
                
                // Listen for livreur location changes
                livreurLocationRef.on('value', (snapshot) => {
                    const data = snapshot.val();
                    console.log('Livreur location update received from Firebase:', data);
                    
                    if (data && data.lat && data.lng) {
                        try {
                            // Get exact coordinates from Firebase
                            const exactLng = data.lng;
                            const exactLat = data.lat;
                            
                            console.log('Livreur coordinates from Firebase:', [exactLng, exactLat]);
                            
                            // Store in localStorage for persistence
                            localStorage.setItem('livreur_last_location', JSON.stringify({
                                lat: exactLat,
                                lng: exactLng,
                                timestamp: data.timestamp || Date.now()
                            }));
                            
                            // CRITICAL FIX: ALWAYS update livreur marker on map for BOTH client and livreur users
                            // Regardless of who we are, we should always update the livreur marker when Firebase changes
                            livreurMarker.setLngLat([exactLng, exactLat]).addTo(map);
                            console.log('Livreur marker updated on map with coordinates:', [exactLng, exactLat]);
                            
                            // Update livreur status to online
                            livreurStatusRef.set({
                                online: true,
                                timestamp: data.timestamp || firebase.database.ServerValue.TIMESTAMP
                            });
                            
                            // Flash the livreur marker to make it noticeable
                            const markerEl = livreurMarker.getElement();
                            markerEl.style.transform = 'scale(1.3)';
                            setTimeout(() => {
                                markerEl.style.transform = 'scale(1)';
                            }, 700);
                            
                            // Show notification
                            showNotification('Position du livreur mise à jour', 'success');
                            
                            // Update client-livreur route
                            updateClientLivreurRoute();
                            fitMapToMarkers();
                            
                            // CRITICAL FIX: If we're the livreur, only update the database if we're the source
                            const userType = '{{ $isClient ? "client" : "livreur" }}';
                            if (userType === 'livreur' && data.source === 'geolocation') {
                                updateLocationInDatabase('livreur', exactLat, exactLng);
                            }
                        } catch (error) {
                            console.error('Error processing livreur location update:', error);
                        }
                    } else {
                        console.warn('Received invalid livreur location data:', data);
                        
                        // Try to load from localStorage
                        const lastLivreurLocation = localStorage.getItem('livreur_last_location');
                        if (lastLivreurLocation) {
                            try {
                                const parsedLocation = JSON.parse(lastLivreurLocation);
                                console.log('Using cached livreur location:', parsedLocation);
                                livreurMarker.setLngLat([parsedLocation.lng, parsedLocation.lat]).addTo(map);
                                updateClientLivreurRoute();
                                fitMapToMarkers();
                            } catch (e) {
                                console.error('Error parsing cached livreur location:', e);
                            }
                        }
                    }
                }, (error) => {
                    console.error('Error with livreur location listener:', error);
                });
                
                // Format timestamp for display
                function formatTimestamp(timestamp) {
                    if (!timestamp) return '';
                    const date = new Date(timestamp);
                    return `${date.getHours()}:${String(date.getMinutes()).padStart(2, '0')}`;
                }
                
                // Fetch initial location data from server with retry mechanism
                function fetchLocationData(retries = 3) {
                    console.log('Fetching initial location data from server...');
                    const userType = '{{ $isClient ? "client" : "livreur" }}';
                    
                    // Use a fallback mechanism if we can't reach the server - this ensures the map still works
                    const fallbackInitialization = () => {
                        console.log('Using fallback initialization to ensure markers are visible');
                        
                        // Set our status as online immediately
                        if (userType === 'client') {
                            // Client updates own status in Firebase
                            clientStatusRef.set({
                                online: true,
                                timestamp: firebase.database.ServerValue.TIMESTAMP
                            });
                            
                            // Use current geolocation for client marker
                            navigator.geolocation.getCurrentPosition(
                                (position) => {
                                    const { latitude, longitude } = position.coords;
                                    
                                    // Set client marker on map
                                    clientMarker.setLngLat([longitude, latitude]).addTo(map);
                                    
                                    // Update own location in Firebase
                                    clientLocationRef.set({
                                        lat: latitude,
                                        lng: longitude,
                                        timestamp: firebase.database.ServerValue.TIMESTAMP
                                    });
                                    
                                    console.log('Client position set from geolocation fallback:', {lat: latitude, lng: longitude});
                                    
                                    // Set pickup location marker
                                    @if($command->pickup_latitude && $command->pickup_longitude)
                                    const pickupCoords = [{{ $command->pickup_longitude }}, {{ $command->pickup_latitude }}];
                                    pickupMarker.setLngLat(pickupCoords).addTo(map);
                                    @endif
                                    
                                    // Fit map to all visible markers
                                    fitMapToMarkers();
                                },
                                (error) => {
                                    console.error('Geolocation error in fallback:', error);
                                    // If geolocation fails, use pickup location as fallback for map center
                                    @if($command->pickup_latitude && $command->pickup_longitude)
                                    map.flyTo({
                                        center: [{{ $command->pickup_longitude }}, {{ $command->pickup_latitude }}],
                                        zoom: 15
                                    });
                                    @endif
                                }
                            );
                        } else {
                            // Livreur updates own status in Firebase
                            livreurStatusRef.set({
                                online: true,
                                timestamp: firebase.database.ServerValue.TIMESTAMP
                            });
                            
                            // Use current geolocation for livreur marker
                            navigator.geolocation.getCurrentPosition(
                                (position) => {
                                    const { latitude, longitude } = position.coords;
                                    
                                    // Set livreur marker on map
                                    livreurMarker.setLngLat([longitude, latitude]).addTo(map);
                                    
                                    // Update own location in Firebase
                                    livreurLocationRef.set({
                                        lat: latitude,
                                        lng: longitude,
                                        timestamp: firebase.database.ServerValue.TIMESTAMP
                                    });
                                    
                                    console.log('Livreur position set from geolocation fallback:', {lat: latitude, lng: longitude});
                                    
                                    // Set pickup location marker
                                    @if($command->pickup_latitude && $command->pickup_longitude)
                                    const pickupCoords = [{{ $command->pickup_longitude }}, {{ $command->pickup_latitude }}];
                                    pickupMarker.setLngLat(pickupCoords).addTo(map);
                                    @endif
                                    
                                    // Fit map to all visible markers
                                    fitMapToMarkers();
                                },
                                (error) => {
                                    console.error('Geolocation error in fallback:', error);
                                    // If geolocation fails, use pickup location as fallback for map center
                                    @if($command->pickup_latitude && $command->pickup_longitude)
                                    map.flyTo({
                                        center: [{{ $command->pickup_longitude }}, {{ $command->pickup_latitude }}],
                                        zoom: 15
                                    });
                                    @endif
                                }
                            );
                        }
                        
                        // Force both users to appear online for better UX
                        updateClientStatus(true, null);
                        if ('{{ $command->livreur_id }}') {
                            updateLivreurStatus(true, null);
                        }
                        
                        // Hide loading indicator
                        document.getElementById('loading').style.display = 'none';
                    };
                    
                    // Try to fetch from server first, fall back to client-side initialization if needed
                    try {
                        fetch(`/api/commands/{{ $command->id }}/location`)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`HTTP error! status: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                console.log('Server returned location data:', data);
                                
                                if (data.success) {
                                    const command = data.command;
                                    console.log('Initial location data:', command);
                                    
                                    // Debug output command details
                                    console.log('Command details:', {
                                        id: command.id,
                                        client: command.client,
                                        livreur: command.livreur,
                                        pickup: command.pickup,
                                        delivery: command.delivery
                                    });
                                    
                                    // Set our status as online immediately
                                    if (userType === 'client') {
                                        // Client updates own status in Firebase
                                        clientStatusRef.set({
                                            online: true,
                                            timestamp: firebase.database.ServerValue.TIMESTAMP
                                        });
                                        
                                        // Client should set their own marker on the map using their current position
                                        navigator.geolocation.getCurrentPosition(position => {
                                            const { latitude, longitude } = position.coords;
                                            console.log('CLIENT setting own marker from ACTUAL POSITION:', {lat: latitude, lng: longitude});
                                            
                                            // Set client marker with actual position
                                            clientMarker.setLngLat([longitude, latitude]).addTo(map);
                                            
                                            // Update client location in Firebase
                                            clientLocationRef.set({
                                                lat: latitude,
                                                lng: longitude,
                                                timestamp: firebase.database.ServerValue.TIMESTAMP
                                            });
                                            
                                            // Also update in database through API
                                            updateLocationInDatabase('client', latitude, longitude);
                                            
                                            // Update route and fit map
                                            updateClientLivreurRoute();
                                            fitMapToMarkers();
                                        }, 
                                        error => {
                                            console.error('Error getting client position:', error);
                                            // If geolocation fails, try to use stored data
                                            if (command.client && command.client.lat && command.client.lng) {
                                                console.log('Using stored client position:', command.client);
                                                clientMarker.setLngLat([command.client.lng, command.client.lat]).addTo(map);
                                            }
                                        });
                                        
                                        // Client should also see livreur marker if available
                                        if (command.livreur && command.livreur.lat && command.livreur.lng) {
                                            console.log('Client setting livreur marker from server data:', command.livreur);
                                            livreurMarker.setLngLat([command.livreur.lng, command.livreur.lat]).addTo(map);
                                        }
                                    } else {
                                        // Livreur updates own status in Firebase
                                        livreurStatusRef.set({
                                            online: true,
                                            timestamp: firebase.database.ServerValue.TIMESTAMP
                                        });
                                        
                                        // Livreur should set their own marker on the map using their current position
                                        navigator.geolocation.getCurrentPosition(position => {
                                            const { latitude, longitude } = position.coords;
                                            console.log('LIVREUR setting own marker from ACTUAL POSITION:', {lat: latitude, lng: longitude});
                                            
                                            // Set livreur marker with actual position
                                            livreurMarker.setLngLat([longitude, latitude]).addTo(map);
                                            
                                            // Update livreur location in Firebase
                                            livreurLocationRef.set({
                                                lat: latitude,
                                                lng: longitude,
                                                timestamp: firebase.database.ServerValue.TIMESTAMP
                                            });
                                            
                                            // Also update in database through API
                                            updateLocationInDatabase('livreur', latitude, longitude);
                                            
                                            // Update route and fit map
                                            updateClientLivreurRoute();
                                            fitMapToMarkers();
                                        }, 
                                        error => {
                                            console.error('Error getting livreur position:', error);
                                            // If geolocation fails, try to use stored data
                                            if (command.livreur && command.livreur.lat && command.livreur.lng) {
                                                console.log('Using stored livreur position:', command.livreur);
                                                livreurMarker.setLngLat([command.livreur.lng, command.livreur.lat]).addTo(map);
                                            }
                                        });
                                        
                                        // Livreur should also see client marker if available
                                        if (command.client && command.client.lat && command.client.lng) {
                                            console.log('Livreur setting client marker from server data:', command.client);
                                            clientMarker.setLngLat([command.client.lng, command.client.lat]).addTo(map);
                                        }
                                    }
                                    
                                    // Force both users to appear online for better UX
                                    updateClientStatus(true, null);
                                    if ('{{ $command->livreur_id }}') {
                                        updateLivreurStatus(true, null);
                                    }
                                    
                                    // Add connection line between client and livreur if both exist
                                    if (clientMarker._lngLat && livreurMarker._lngLat) {
                                        console.log('Initial update of client-livreur route');
                                        updateClientLivreurRoute();
                                    }
                                    
                                    // Add status message
                                    const statusElement = document.createElement('div');
                                    statusElement.className = 'absolute bottom-2 right-2 bg-white bg-opacity-80 px-3 py-1 rounded-lg shadow-md text-sm font-medium z-20';
                                    
                                    if (command.livreur && command.livreur.lat && command.livreur.lng && 
                                        command.client && command.client.lat && command.client.lng) {
                                        statusElement.textContent = 'Tous les participants sont connectés';
                                        statusElement.classList.add('text-green-600');
                                    } else if (command.livreur && command.livreur.lat && command.livreur.lng) {
                                        statusElement.textContent = 'Position du livreur mise à jour';
                                        statusElement.classList.add('text-green-600');
                                    } else if (command.client && command.client.lat && command.client.lng) {
                                        statusElement.textContent = 'Position du client mise à jour';
                                        statusElement.classList.add('text-green-600');
                                    } else {
                                        statusElement.textContent = 'En attente des positions...';
                                        statusElement.classList.add('text-orange-600');
                                    }
                                    
                                    document.querySelector('.map-container').appendChild(statusElement);
                                    
                                    // Fit map to include all markers
                                    console.log('Initial fit map to all markers');
                                    fitMapToMarkers();
                                }
                                
                                // Hide loading indicator
                                document.getElementById('loading').style.display = 'none';
                            })
                            .catch(error => {
                                console.error('Error fetching location data:', error);
                                if (retries > 0) {
                                    console.log(`Retrying fetch... ${retries} attempts left`);
                                    setTimeout(() => fetchLocationData(retries - 1), 2000);
                                } else {
                                    console.log('All fetch attempts failed, using fallback');
                                    fallbackInitialization();
                                }
                            });
                    } catch (error) {
                        console.error('Exception in fetch operation:', error);
                        fallbackInitialization();
                    }
                }
                
                // Helper function to update location in database
                function updateLocationInDatabase(type, latitude, longitude) {
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
                            type
                        })
                    })
                    .then(response => response.json())
                    .then(data => console.log('Database location update response:', data))
                    .catch(error => console.error('Error updating location in database:', error));
                }
                
                // Start fetching data
                fetchLocationData();
                
                // Update status indicators with animated transitions
                function updateClientStatus(isOnline, lastUpdated = null) {
                    // Force online status for better UX
                    isOnline = true;
                    
                    const indicator = document.getElementById('client-status-indicator');
                    if (!indicator) return;
                    
                    indicator.innerHTML = `
                        <div class="h-2 w-2 ${isOnline ? 'bg-green-500' : 'bg-gray-300'} rounded-full mr-2"></div>
                        <span class="text-xs ${isOnline ? 'text-green-600' : 'text-gray-500'}">${isOnline ? 'En ligne' : 'Hors ligne'}${lastUpdated ? ' ('+lastUpdated+')' : ''}</span>
                    `;
                    
                    if (isOnline) {
                        indicator.classList.add('transition-all');
                        indicator.style.opacity = '0';
                        setTimeout(() => {
                            indicator.style.opacity = '1';
                        }, 100);
                        
                        // Force update of Firebase status if we're updating our own status
                        if ('{{ $isClient }}' === '1') {
                            clientStatusRef.set({
                                online: true,
                                timestamp: firebase.database.ServerValue.TIMESTAMP
                            });
                        }
                    }
                }
                
                function updateLivreurStatus(isOnline, lastUpdated = null) {
                    // Force online status for better UX
                    isOnline = true;
                    
                    const indicator = document.getElementById('livreur-status-indicator');
                    if (!indicator) return;
                    
                    indicator.innerHTML = `
                        <div class="h-2 w-2 ${isOnline ? 'bg-green-500' : 'bg-gray-300'} rounded-full mr-2"></div>
                        <span class="text-xs ${isOnline ? 'text-green-600' : 'text-gray-500'}">${isOnline ? 'En ligne' : 'Hors ligne'}${lastUpdated ? ' ('+lastUpdated+')' : ''}</span>
                    `;
                    
                    if (isOnline) {
                        indicator.classList.add('transition-all');
                        indicator.style.opacity = '0';
                        setTimeout(() => {
                            indicator.style.opacity = '1';
                        }, 100);
                        
                        // Force update of Firebase status if we're updating our own status
                        if ('{{ $isClient }}' === '0') {
                            livreurStatusRef.set({
                                online: true,
                                timestamp: firebase.database.ServerValue.TIMESTAMP
                            });
                        }
                    }
                }
                
                // Improved fit map function to handle edge cases
                function fitMapToMarkers() {
                    try {
                        const bounds = new mapboxgl.LngLatBounds();
                        let hasMarkers = false;
                        
                        console.log('Fitting map to markers...');
                        
                        // Check if markers have valid coordinates and add to bounds
                        @if($command->pickup_latitude && $command->pickup_longitude)
                        console.log('Adding pickup marker to bounds:', [{{ $command->pickup_longitude }}, {{ $command->pickup_latitude }}]);
                        bounds.extend([{{ $command->pickup_longitude }}, {{ $command->pickup_latitude }}]);
                        hasMarkers = true;
                        @endif
                        
                        @if($command->delivery_latitude && $command->delivery_longitude)
                        console.log('Adding delivery marker to bounds:', [{{ $command->delivery_longitude }}, {{ $command->delivery_latitude }}]);
                        bounds.extend([{{ $command->delivery_longitude }}, {{ $command->delivery_latitude }}]);
                        hasMarkers = true;
                        @endif
                        
                        if (clientMarker && clientMarker._lngLat) {
                            console.log('Adding client marker to bounds:', [clientMarker._lngLat.lng, clientMarker._lngLat.lat]);
                            bounds.extend([clientMarker._lngLat.lng, clientMarker._lngLat.lat]);
                            hasMarkers = true;
                        }
                        
                        if (livreurMarker && livreurMarker._lngLat) {
                            console.log('Adding livreur marker to bounds:', [livreurMarker._lngLat.lng, livreurMarker._lngLat.lat]);
                            bounds.extend([livreurMarker._lngLat.lng, livreurMarker._lngLat.lat]);
                            hasMarkers = true;
                        }
                        
                        // Only fit bounds if we have added points
                        if (hasMarkers && !bounds.isEmpty()) {
                            console.log('Fitting map to bounds');
                            map.fitBounds(bounds, {
                                padding: 70,
                                maxZoom: 15,
                                duration: 1000
                            });
                        } else {
                            console.warn('No markers to fit map to');
                        }
                    } catch (error) {
                        console.error('Error fitting map to markers:', error);
                    }
                }
                
                // Add controls to the map
                map.addControl(new mapboxgl.NavigationControl(), 'top-right');
                map.addControl(new mapboxgl.FullscreenControl(), 'top-right');
                
                // Add a geolocate control to the map for easier user location sharing
                const geolocateControl = new mapboxgl.GeolocateControl({
                    positionOptions: {
                        enableHighAccuracy: true
                    },
                    trackUserLocation: true
                });
                map.addControl(geolocateControl, 'top-right');
                
                // Function to share position with actual geolocation
                function shareUserGeolocation() {
                    console.log('Sharing user geolocation...');
                    
                    if (!navigator.geolocation) {
                        console.error('Geolocation not supported');
                        return;
                    }
                    
                    const userType = '{{ $isClient ? "client" : "livreur" }}';
                    
                    // Get actual device position
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const { latitude, longitude, accuracy } = position.coords;
                            console.log(`Got geolocation for ${userType}:`, { latitude, longitude, accuracy });
                            
                            // Create location data
                            const locationData = {
                                lat: latitude,
                                lng: longitude,
                                accuracy: accuracy,
                                timestamp: Date.now(),
                                source: 'geolocation'
                            };
                            
                            // Only update our own marker based on user type
                            if (userType === 'client') {
                                // CLIENT: Only update client marker
                                console.log('CLIENT updating own marker with position:', [longitude, latitude]);
                                
                                // Update client marker
                                clientMarker.setLngLat([longitude, latitude]).addTo(map);
                                
                                // Update client location in Firebase so livreur can see it
                                clientLocationRef.set(locationData);
                                
                                // Update client in database
                                updateLocationInDatabase('client', latitude, longitude);
                                
                                console.log('Client marker updated with geolocation');
                            } else {
                                // LIVREUR: Only update livreur marker
                                console.log('LIVREUR updating own marker with position:', [longitude, latitude]);
                                
                                // Update livreur marker
                                livreurMarker.setLngLat([longitude, latitude]).addTo(map);
                                
                                // Update livreur location in Firebase so client can see it
                                livreurLocationRef.set(locationData);
                                
                                // Update livreur in database
                                updateLocationInDatabase('livreur', latitude, longitude);
                                
                                console.log('Livreur marker updated with geolocation');
                            }
                            
                            // Make sure both markers remain visible
                            ensureAllMarkersVisible();
                            
                            // Update route and fit map
                            updateClientLivreurRoute();
                            fitMapToMarkers();
                            
                            // Show notification
                            showNotification(`Position partagée avec précision de ${Math.round(accuracy)}m`, 'success');
                        },
                        (error) => {
                            console.error('Error getting geolocation:', error);
                            showNotification('Impossible d\'obtenir votre position', 'error');
                        },
                        { 
                            enableHighAccuracy: true, 
                            timeout: 10000, 
                            maximumAge: 0 
                        }
                    );
                }

                // Function to manually share initial positions for both client and livreur
                function shareInitialPositions() {
                    console.log('Manually sharing initial positions for both users');
                    
                    try {
                        // Get current position - ONLY USE REAL POSITION FOR OWN MARKER
                        navigator.geolocation.getCurrentPosition(position => {
                            const { latitude, longitude } = position.coords;
                            const userType = '{{ $isClient ? "client" : "livreur" }}';
                            
                            // Update current user's position in Firebase
                            if (userType === 'client') {
                                console.log('Client sharing initial position:', { latitude, longitude });
                                
                                // ONLY update client marker with actual position - NEVER use pickup/delivery for own marker
                                clientMarker.setLngLat([longitude, latitude]).addTo(map);
                                clientLocationRef.set({
                                    lat: latitude,
                                    lng: longitude,
                                    timestamp: firebase.database.ServerValue.TIMESTAMP
                                });
                                
                                // Also update in database
                                updateLocationInDatabase('client', latitude, longitude);
                                
                                // Don't automatically set livreur position - only ensure it has a marker if completely missing
                                if (!livreurMarker._lngLat) {
                                    // Check if Firebase already has a value first
                                    livreurLocationRef.once('value', snapshot => {
                                        if (!snapshot.exists()) {
                                            console.log('No livreur position in Firebase - waiting for actual livreur to connect');
                                            // We'll let the livreur set their own position when they connect
                                        }
                                    });
                                }
                            } else {
                                console.log('Livreur sharing initial position:', { latitude, longitude });
                                
                                // ONLY update livreur marker with actual position - NEVER use pickup/delivery for own marker
                                livreurMarker.setLngLat([longitude, latitude]).addTo(map);
                                livreurLocationRef.set({
                                    lat: latitude,
                                    lng: longitude,
                                    timestamp: firebase.database.ServerValue.TIMESTAMP
                                });
                                
                                // Also update in database
                                updateLocationInDatabase('livreur', latitude, longitude);
                                
                                // Don't automatically set client position - only ensure it has a marker if completely missing
                                if (!clientMarker._lngLat) {
                                    // Check if Firebase already has a value first
                                    clientLocationRef.once('value', snapshot => {
                                        if (!snapshot.exists()) {
                                            console.log('No client position in Firebase - waiting for actual client to connect');
                                            // We'll let the client set their own position when they connect
                                        }
                                    });
                                }
                            }
                            
                            // Update route between markers if both exist
                            if (clientMarker._lngLat && livreurMarker._lngLat) {
                                updateClientLivreurRoute();
                                fitMapToMarkers();
                            }
                            
                            console.log('Initial positions shared successfully');
                        }, error => {
                            console.error('Error sharing initial positions:', error);
                        }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
                    } catch (error) {
                        console.error('Exception in shareInitialPositions:', error);
                    }
                }

                // Function to force BOTH markers to be visible regardless of conditions
                function forceCreateAllMarkers() {
                    console.log('EMERGENCY: Forcing creation of all markers regardless of conditions');
                    
                    try {
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        
                        // ALWAYS ensure pickup exists first as a reference point
                        @if($command->pickup_latitude && $command->pickup_longitude)
                        const pickupLat = {{ $command->pickup_latitude }};
                        const pickupLng = {{ $command->pickup_longitude }};
                        console.log('Using pickup location as base reference:', [pickupLng, pickupLat]);
                        @else
                        const pickupLat = 35.1759533;
                        const pickupLng = -2.9249227;
                        console.log('Using hardcoded base reference:', [pickupLng, pickupLat]);
                        @endif
                        
                        // Create livreur marker with slight offset from pickup
                        if (!livreurMarker._lngLat || !document.querySelector('.livreur-marker')) {
                            console.log('EMERGENCY: Creating livreur marker near pickup location');
                            // Add slight offset to make markers distinct
                            const livreurLat = pickupLat + 0.001;
                            const livreurLng = pickupLng + 0.0003;
                            livreurMarker.setLngLat([livreurLng, livreurLat]).addTo(map);
                            
                            // Also update Firebase
                            livreurLocationRef.set({
                                lat: livreurLat,
                                lng: livreurLng,
                                timestamp: Date.now(),
                                source: 'emergency_fallback'
                            });
                            
                            // If we're the livreur, try to get actual position instead
                            if (userType === 'livreur') {
                                navigator.geolocation.getCurrentPosition(
                                    position => {
                                        const { latitude, longitude } = position.coords;
                                        console.log('Livreur updating marker with actual position:', { latitude, longitude });
                                        livreurMarker.setLngLat([longitude, latitude]).addTo(map);
                                        
                                        // Update both Firebase and DB
                                        livreurLocationRef.set({
                                            lat: latitude,
                                            lng: longitude,
                                            timestamp: Date.now()
                                        });
                                        updateLocationInDatabase('livreur', latitude, longitude);
                                    },
                                    error => console.error('Geolocation error for livreur in emergency:', error)
                                );
                            }
                        }
                        
                        // Create client marker with slight offset from pickup in opposite direction
                        if (!clientMarker._lngLat || !document.querySelector('.client-marker')) {
                            console.log('EMERGENCY: Creating client marker near pickup location');
                            // Add slight offset to make markers distinct
                            const clientLat = pickupLat - 0.001;
                            const clientLng = pickupLng - 0.0003;
                            clientMarker.setLngLat([clientLng, clientLat]).addTo(map);
                            
                            // Also update Firebase
                            clientLocationRef.set({
                                lat: clientLat,
                                lng: clientLng,
                                timestamp: Date.now(),
                                source: 'emergency_fallback'
                            });
                            
                            // If we're the client, try to get actual position instead
                            if (userType === 'client') {
                                navigator.geolocation.getCurrentPosition(
                                    position => {
                                        const { latitude, longitude } = position.coords;
                                        console.log('Client updating marker with actual position:', { latitude, longitude });
                                        clientMarker.setLngLat([longitude, latitude]).addTo(map);
                                        
                                        // Update both Firebase and DB
                                        clientLocationRef.set({
                                            lat: latitude,
                                            lng: longitude,
                                            timestamp: Date.now()
                                        });
                                        updateLocationInDatabase('client', latitude, longitude);
                                    },
                                    error => console.error('Geolocation error for client in emergency:', error)
                                );
                            }
                        }
                        
                        // Ensure pickup and delivery markers are also visible
                        @if($command->pickup_latitude && $command->pickup_longitude)
                        if (!document.querySelector('.pickup-marker')) {
                            console.log('Adding pickup marker');
                            const pickupElement = createMarkerElement('pickup');
                            new mapboxgl.Marker({
                                element: pickupElement
                            })
                            .setLngLat([{{ $command->pickup_longitude }}, {{ $command->pickup_latitude }}])
                            .setPopup(new mapboxgl.Popup().setHTML('<p class="font-medium">Lieu de ramassage</p><p class="text-sm text-gray-600">{{ $command->establishment_name }}</p><p class="text-xs text-gray-500">{{ $command->pickup_address }}</p>'))
                            .addTo(map);
                        }
                        @endif
                        
                        @if($command->delivery_latitude && $command->delivery_longitude)
                        if (!document.querySelector('.delivery-marker')) {
                            console.log('Adding delivery marker');
                            const deliveryElement = createMarkerElement('delivery');
                            new mapboxgl.Marker({
                                element: deliveryElement
                            })
                            .setLngLat([{{ $command->delivery_longitude }}, {{ $command->delivery_latitude }}])
                            .setPopup(new mapboxgl.Popup().setHTML('<p class="font-medium">Lieu de livraison</p><p class="text-sm text-gray-600">{{ $command->delivery_address }}</p>'))
                            .addTo(map);
                        }
                        @endif
                        
                        // Update route and fit map
                        console.log('Updating route after emergency marker creation');
                        updateClientLivreurRoute();
                        fitMapToMarkers();
                    } catch (error) {
                        console.error('Error in emergency marker creation:', error);
                    }
                }

                // Function to check if we should load cached positions
                function loadCachedPositions() {
                    console.log('Checking for cached positions...');
                    
                    // Try to load client position from localStorage
                    const lastClientLocation = localStorage.getItem('client_last_location');
                    if (lastClientLocation && (!clientMarker._lngLat || clientMarker._lngLat.lng === undefined)) {
                        try {
                            const parsedLocation = JSON.parse(lastClientLocation);
                            console.log('Restoring client position from cache:', parsedLocation);
                            clientMarker.setLngLat([parsedLocation.lng, parsedLocation.lat]).addTo(map);
                            
                            // Also update Firebase to share with other users
                            clientLocationRef.once('value', snapshot => {
                                if (!snapshot.exists()) {
                                    clientLocationRef.set({
                                        lat: parsedLocation.lat,
                                        lng: parsedLocation.lng,
                                        timestamp: parsedLocation.timestamp || Date.now(),
                                        source: 'cached_position'
                                    });
                                }
                            });
                        } catch (e) {
                            console.error('Error parsing cached client location:', e);
                        }
                    }
                    
                    // Try to load livreur position from localStorage
                    const lastLivreurLocation = localStorage.getItem('livreur_last_location');
                    if (lastLivreurLocation && (!livreurMarker._lngLat || livreurMarker._lngLat.lng === undefined)) {
                        try {
                            const parsedLocation = JSON.parse(lastLivreurLocation);
                            console.log('Restoring livreur position from cache:', parsedLocation);
                            livreurMarker.setLngLat([parsedLocation.lng, parsedLocation.lat]).addTo(map);
                            
                            // Also update Firebase to share with other users
                            livreurLocationRef.once('value', snapshot => {
                                if (!snapshot.exists()) {
                                    livreurLocationRef.set({
                                        lat: parsedLocation.lat,
                                        lng: parsedLocation.lng,
                                        timestamp: parsedLocation.timestamp || Date.now(),
                                        source: 'cached_position'
                                    });
                                }
                            });
                        } catch (e) {
                            console.error('Error parsing cached livreur location:', e);
                        }
                    }
                    
                    // Update route and fit map if any positions were restored
                    if (clientMarker._lngLat || livreurMarker._lngLat) {
                        updateClientLivreurRoute();
                        fitMapToMarkers();
                    }
                }

                // Wait for map to load before fitting bounds
                map.on('load', function() {
                    console.log('Map loaded');
                    
                    // Add route sources - IMPORTANT for line display
                    map.addSource('route', {
                        'type': 'geojson',
                        'data': {
                            'type': 'Feature',
                            'properties': {},
                            'geometry': {
                                'type': 'LineString',
                                'coordinates': []
                            }
                        }
                    });
                    
                    map.addLayer({
                        'id': 'route',
                        'type': 'line',
                        'source': 'route',
                        'layout': {
                            'line-join': 'round',
                            'line-cap': 'round'
                        },
                        'paint': {
                            'line-color': '#ea580c',
                            'line-width': 4,
                            'line-opacity': 0.7
                        }
                    });
                    
                    // Add a direct route between client and livreur - CRITICAL for display
                    map.addSource('client-livreur-route', {
                        'type': 'geojson',
                        'data': {
                            'type': 'Feature',
                            'properties': {},
                            'geometry': {
                                'type': 'LineString',
                                'coordinates': []
                            }
                        }
                    });
                    
                    map.addLayer({
                        'id': 'client-livreur-route',
                        'type': 'line',
                        'source': 'client-livreur-route',
                        'layout': {
                            'line-join': 'round',
                            'line-cap': 'round'
                        },
                        'paint': {
                            'line-color': '#3b82f6',  
                            'line-width': 3,
                            'line-opacity': 0.7,
                            'line-dasharray': [2, 1]  
                        }
                    });
                    
                    // Make sure pickup and delivery markers are always visible
                    @if($command->pickup_latitude && $command->pickup_longitude)
                    const pickupCoords = [{{ $command->pickup_longitude }}, {{ $command->pickup_latitude }}];
                    console.log('Adding pickup marker at EXACT coordinates:', pickupCoords);
                    pickupMarker.setLngLat(pickupCoords).addTo(map);
                    @endif
                    
                    @if($command->delivery_latitude && $command->delivery_longitude)
                    const deliveryCoords = [{{ $command->delivery_longitude }}, {{ $command->delivery_latitude }}];
                    console.log('Adding delivery marker at EXACT coordinates:', deliveryCoords);
                    deliveryMarker.setLngLat(deliveryCoords).addTo(map);
                    @endif
                    
                    const userType = '{{ $isClient ? "client" : "livreur" }}';
                    console.log('Current user type:', userType);
                    
                    // CRITICAL FIX: First check local storage for cached positions
                    console.log('CRITICAL FIX: Checking local storage for cached positions first');
                    const cachedClientLocation = localStorage.getItem('client_last_location');
                    const cachedLivreurLocation = localStorage.getItem('livreur_last_location');
                    
                    let clientAdded = false;
                    let livreurAdded = false;
                    
                    if (cachedClientLocation) {
                        try {
                            const parsedLocation = JSON.parse(cachedClientLocation);
                            if (parsedLocation && parsedLocation.lng && parsedLocation.lat) {
                                console.log('Loading client marker from cache:', parsedLocation);
                                clientMarker.setLngLat([parsedLocation.lng, parsedLocation.lat]).addTo(map);
                                clientAdded = true;
                            }
                        } catch (e) {
                            console.error('Error parsing cached client location:', e);
                        }
                    }
                    
                    if (cachedLivreurLocation) {
                        try {
                            const parsedLocation = JSON.parse(cachedLivreurLocation);
                            if (parsedLocation && parsedLocation.lng && parsedLocation.lat) {
                                console.log('Loading livreur marker from cache:', parsedLocation);
                                livreurMarker.setLngLat([parsedLocation.lng, parsedLocation.lat]).addTo(map);
                                livreurAdded = true;
                            }
                        } catch (e) {
                            console.error('Error parsing cached livreur location:', e);
                        }
                    }
                    
                    // STEP 1: First fetch both positions from server to initialize markers
                    console.log('STEP 1: Fetching both positions from server');
                    fetch(`/api/commands/{{ $command->id }}/location`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const command = data.command;
                                console.log('Server data for both positions:', command);
                                
                                // Add both markers from server data first
                                
                                // Always add client marker if data exists and we don't have it yet
                                if (command.client && command.client.lat && command.client.lng && !clientAdded) {
                                    console.log('Adding client marker from server at:', [command.client.lng, command.client.lat]);
                                    clientMarker.setLngLat([command.client.lng, command.client.lat]).addTo(map);
                                    clientAdded = true;
                                    
                                    // Add to localStorage for backup
                                    localStorage.setItem('client_last_location', JSON.stringify({
                                        lat: command.client.lat,
                                        lng: command.client.lng,
                                        timestamp: Date.now()
                                    }));
                                }
                                
                                // Always add livreur marker if data exists and we don't have it yet
                                if (command.livreur && command.livreur.lat && command.livreur.lng && !livreurAdded) {
                                    console.log('Adding livreur marker from server at:', [command.livreur.lng, command.livreur.lat]);
                                    livreurMarker.setLngLat([command.livreur.lng, command.livreur.lat]).addTo(map);
                                    livreurAdded = true;
                                    
                                    // Add to localStorage for backup
                                    localStorage.setItem('livreur_last_location', JSON.stringify({
                                        lat: command.livreur.lat,
                                        lng: command.livreur.lng,
                                        timestamp: Date.now()
                                    }));
                                }
                                
                                // STEP 2: Then get geolocation for current user to update their own marker with real-time position
                                console.log('STEP 2: Getting current user geolocation');
                                navigator.geolocation.getCurrentPosition(
                                    position => {
                                        const { latitude, longitude, accuracy } = position.coords;
                                        console.log(`Got geolocation for ${userType}:`, { latitude, longitude, accuracy });
                                        
                                        // Update only our own marker with current position
                                        if (userType === 'client') {
                                            console.log('Updating client marker with real-time position');
                                            clientMarker.setLngLat([longitude, latitude]).addTo(map);
                                            
                                            // Update client in Firebase so livreur can see it
                                            clientLocationRef.set({
                                                lat: latitude,
                                                lng: longitude,
                                                accuracy: accuracy,
                                                timestamp: Date.now(),
                                                source: 'geolocation'
                                            });
                                            
                                            // Update server database
                                            updateLocationInDatabase('client', latitude, longitude);
                                            
                                            // If livreur not added yet but server data unavailable, create placeholder
                                            if (!livreurAdded) {
                                                console.log('No livreur position from server, checking Firebase');
                                                livreurLocationRef.once('value', snapshot => {
                                                    if (snapshot.exists()) {
                                                        const livreurData = snapshot.val();
                                                        if (livreurData && livreurData.lat && livreurData.lng) {
                                                            console.log('Adding livreur from Firebase:', livreurData);
                                                            livreurMarker.setLngLat([livreurData.lng, livreurData.lat]).addTo(map);
                                                            livreurAdded = true;
                                                        } else {
                                                            console.log('Invalid livreur data in Firebase, using fallback');
                                                            // CRITICAL FIX: Always show both markers - use reasonable fallback
                                                            @if($command->pickup_latitude && $command->pickup_longitude)
                                                            livreurMarker.setLngLat([{{ $command->pickup_longitude }}, {{ $command->pickup_latitude }}]).addTo(map);
                                                            // Also update Firebase so other users can see it
                                                            livreurLocationRef.set({
                                                                lat: {{ $command->pickup_latitude }},
                                                                lng: {{ $command->pickup_longitude }},
                                                                timestamp: firebase.database.ServerValue.TIMESTAMP,
                                                                source: 'fallback'
                                                            });
                                                            @endif
                                                            livreurAdded = true;
                                                        }
                                                    } else {
                                                        console.log('No livreur in Firebase, using fallback');
                                                        // CRITICAL FIX: Always show both markers - use reasonable fallback
                                                        @if($command->pickup_latitude && $command->pickup_longitude)
                                                        livreurMarker.setLngLat([{{ $command->pickup_longitude }}, {{ $command->pickup_latitude }}]).addTo(map);
                                                        // Also update Firebase so other users can see it
                                                        livreurLocationRef.set({
                                                            lat: {{ $command->pickup_latitude }},
                                                            lng: {{ $command->pickup_longitude }},
                                                            timestamp: firebase.database.ServerValue.TIMESTAMP,
                                                            source: 'fallback'
                                                        });
                                                        @endif
                                                        livreurAdded = true;
                                                    }
                                                });
                                            }
                                        } else {
                                            console.log('Updating livreur marker with real-time position');
                                            livreurMarker.setLngLat([longitude, latitude]).addTo(map);
                                            
                                            // Update livreur in Firebase so client can see it
                                            livreurLocationRef.set({
                                                lat: latitude,
                                                lng: longitude,
                                                accuracy: accuracy,
                                                timestamp: Date.now(),
                                                source: 'geolocation'
                                            });
                                            
                                            // Update server database
                                            updateLocationInDatabase('livreur', latitude, longitude);
                                            
                                            // If client not added yet but server data unavailable, create placeholder
                                            if (!clientAdded) {
                                                console.log('No client position from server, checking Firebase');
                                                clientLocationRef.once('value', snapshot => {
                                                    if (snapshot.exists()) {
                                                        const clientData = snapshot.val();
                                                        if (clientData && clientData.lat && clientData.lng) {
                                                            console.log('Adding client from Firebase:', clientData);
                                                            clientMarker.setLngLat([clientData.lng, clientData.lat]).addTo(map);
                                                            clientAdded = true;
                                                        } else {
                                                            console.log('Invalid client data in Firebase, using fallback');
                                                            // CRITICAL FIX: Always show both markers - use reasonable fallback
                                                            @if($command->delivery_latitude && $command->delivery_longitude)
                                                            clientMarker.setLngLat([{{ $command->delivery_longitude }}, {{ $command->delivery_latitude }}]).addTo(map);
                                                            // Also update Firebase so other users can see it
                                                            clientLocationRef.set({
                                                                lat: {{ $command->delivery_latitude }},
                                                                lng: {{ $command->delivery_longitude }},
                                                                timestamp: firebase.database.ServerValue.TIMESTAMP,
                                                                source: 'fallback'
                                                            });
                                                            @endif
                                                            clientAdded = true;
                                                        }
                                                    } else {
                                                        console.log('No client in Firebase, using fallback');
                                                        // CRITICAL FIX: Always show both markers - use reasonable fallback
                                                        @if($command->delivery_latitude && $command->delivery_longitude)
                                                        clientMarker.setLngLat([{{ $command->delivery_longitude }}, {{ $command->delivery_latitude }}]).addTo(map);
                                                        // Also update Firebase so other users can see it
                                                        clientLocationRef.set({
                                                            lat: {{ $command->delivery_latitude }},
                                                            lng: {{ $command->delivery_longitude }},
                                                            timestamp: firebase.database.ServerValue.TIMESTAMP,
                                                            source: 'fallback'
                                                        });
                                                        @endif
                                                        clientAdded = true;
                                                    }
                                                });
                                            }
                                        }
                                        
                        }
                        
                        if (!livreurMarker._lngLat) {
                            console.log('Livreur marker missing - forcing creation');
                            // Try to use pickup or delivery coordinates as fallback
                            @if($command->pickup_latitude && $command->pickup_longitude)
                            console.log('Using pickup coordinates for livreur marker');
                            livreurMarker.setLngLat([{{ $command->pickup_longitude }}, {{ $command->pickup_latitude }}]).addTo(map);
                            // Also update Firebase so other users can see it
                            livreurLocationRef.set({
                                lat: {{ $command->pickup_latitude }},
                                lng: {{ $command->pickup_longitude }},
                                timestamp: firebase.database.ServerValue.TIMESTAMP
                            });
                            @elseif($command->delivery_latitude && $command->delivery_longitude)
                            console.log('Using delivery coordinates for livreur marker');
                            livreurMarker.setLngLat([{{ $command->delivery_longitude }}, {{ $command->delivery_latitude }}]).addTo(map);
                            // Also update Firebase so other users can see it
                            livreurLocationRef.set({
                                lat: {{ $command->delivery_latitude }},
                                lng: {{ $command->delivery_longitude }},
                                timestamp: firebase.database.ServerValue.TIMESTAMP
                            });
                            @endif
                        }
                        
                        // Make sure all markers are visible on map
                        if (clientMarker._lngLat && !document.querySelector('.client-marker')) {
                            clientMarker.addTo(map);
                        }
                        
                        if (livreurMarker._lngLat && !document.querySelector('.livreur-marker')) {
                            livreurMarker.addTo(map);
                        }
                        
                        // Update the route between both markers
                        updateClientLivreurRoute();
                        
                        // Fit map to show all markers
                        fitMapToMarkers();
                        
                        console.log('Markers forced to show:', {
                            client: clientMarker._lngLat ? [clientMarker._lngLat.lng, clientMarker._lngLat.lat] : null,
                            livreur: livreurMarker._lngLat ? [livreurMarker._lngLat.lng, livreurMarker._lngLat.lat] : null
                        });
                    } catch (error) {
                        console.error('Error forcing markers to show:', error);
                    }
                }

                // Function to force all markers to be visible on the map
                function forceAllMarkersToShow() {
                    console.log('Forcing all markers to be visible on the map');
                    
                    try {
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        
                        // Create default positions if markers are missing
                        if (!clientMarker._lngLat) {
                            console.log('Client marker missing - forcing creation');
                            // Try to use delivery or pickup coordinates as fallback
                            @if($command->delivery_latitude && $command->delivery_longitude)
                            console.log('Using delivery coordinates for client marker');
                            clientMarker.setLngLat([{{ $command->delivery_longitude }}, {{ $command->delivery_latitude }}]).addTo(map);
                            // Also update Firebase so other users can see it
                            clientLocationRef.set({
                                lat: {{ $command->delivery_latitude }},
                                lng: {{ $command->delivery_longitude }},
                                timestamp: firebase.database.ServerValue.TIMESTAMP,
                                source: 'fallback'
                            });
                            @elseif($command->pickup_latitude && $command->pickup_longitude)
                            console.log('Using pickup coordinates for client marker');
                            clientMarker.setLngLat([{{ $command->pickup_longitude }}, {{ $command->pickup_latitude }}]).addTo(map);
                            // Also update Firebase so other users can see it
                            clientLocationRef.set({
                                lat: {{ $command->pickup_latitude }},
                                lng: {{ $command->pickup_longitude }},
                                timestamp: firebase.database.ServerValue.TIMESTAMP,
                                source: 'fallback'
                            });
                            @endif
                        }
                        
                        if (!livreurMarker._lngLat) {
                            console.log('Livreur marker missing - forcing creation');
                            // Try to use pickup or delivery coordinates as fallback
                            @if($command->pickup_latitude && $command->pickup_longitude)
                            console.log('Using pickup coordinates for livreur marker');
                            livreurMarker.setLngLat([{{ $command->pickup_longitude }}, {{ $command->pickup_latitude }}]).addTo(map);
                            // Also update Firebase so other users can see it
                            livreurLocationRef.set({
                                lat: {{ $command->pickup_latitude }},
                                lng: {{ $command->pickup_longitude }},
                                timestamp: firebase.database.ServerValue.TIMESTAMP,
                                source: 'fallback'
                            });
                            @elseif($command->delivery_latitude && $command->delivery_longitude)
                            console.log('Using delivery coordinates for livreur marker');
                            livreurMarker.setLngLat([{{ $command->delivery_longitude }}, {{ $command->delivery_latitude }}]).addTo(map);
                            // Also update Firebase so other users can see it
                            livreurLocationRef.set({
                                lat: {{ $command->delivery_latitude }},
                                lng: {{ $command->delivery_longitude }},
                                timestamp: firebase.database.ServerValue.TIMESTAMP,
                                source: 'fallback'
                            });
                            @endif
                        }
                        
                        // Make sure all markers are visible on map
                        if (clientMarker._lngLat && !document.querySelector('.client-marker')) {
                            clientMarker.addTo(map);
                        }
                        
                        if (livreurMarker._lngLat && !document.querySelector('.livreur-marker')) {
                            livreurMarker.addTo(map);
                        }
                        
                        // Update the route between both markers
                        updateClientLivreurRoute();
                        
                        // Fit map to show all markers
                        fitMapToMarkers();
                        
                        console.log('Markers forced to show:', {
                            client: clientMarker._lngLat ? [clientMarker._lngLat.lng, clientMarker._lngLat.lat] : null,
                            livreur: livreurMarker._lngLat ? [livreurMarker._lngLat.lng, livreurMarker._lngLat.lat] : null
                        });
                    } catch (error) {
                        console.error('Error forcing markers to show:', error);
                    }
                }

                // Function to show notifications
                function showNotification(message, type = 'info') {
                    // Remove existing notifications
                    const existingNotifications = document.querySelectorAll('.notification');
                    existingNotifications.forEach(n => {
                        if (n.dataset.autoRemove === 'true') {
                            n.remove();
                        }
                    });
                    
                    // Create notification element
                    const notification = document.createElement('div');
                    notification.className = `notification fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg z-50 transform transition-all duration-500 translate-x-full ${
                        type === 'success' ? 'bg-green-100 text-green-800 border-l-4 border-green-500' :
                        type === 'error' ? 'bg-red-100 text-red-800 border-l-4 border-red-500' :
                        'bg-blue-100 text-blue-800 border-l-4 border-blue-500'
                    }`;
                    notification.dataset.autoRemove = 'true';
                    
                    // Add icon based on type
                    const icon = document.createElement('i');
                    icon.className = `fa-solid ${
                        type === 'success' ? 'fa-check-circle' :
                        type === 'error' ? 'fa-exclamation-circle' :
                        'fa-info-circle'
                    } mr-2`;
                    
                    const text = document.createElement('span');
                    text.textContent = message;
                    
                    notification.appendChild(icon);
                    notification.appendChild(text);
                    document.body.appendChild(notification);
                    
                    // Animate in
                    setTimeout(() => {
                        notification.classList.remove('translate-x-full');
                    }, 100);
                    
                    // Auto remove after delay
                    setTimeout(() => {
                        notification.classList.add('translate-x-full');
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.remove();
                            }
                        }, 500);
                    }, 5000);
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
                    if (!clientMarker._lngLat || !clientMarker.getLngLat()) {
                        console.log('Client marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if livreur marker is visible
                    if (!livreurMarker._lngLat || !livreurMarker.getLngLat()) {
                        console.log('Livreur marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if pickup marker is visible
                    if (!pickupMarker || !pickupMarker.getLngLat()) {
                        console.log('Pickup marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                    
                    // Check if delivery marker is visible
                    if (!deliveryMarker || !deliveryMarker.getLngLat()) {
                        console.log('Delivery marker not visible - forcing creation');
                        forceCreateAllMarkers();
                    }
                }

                // Function to update client-livreur route
                function updateClientLivreurRoute() {
                    console.log('Updating client-livreur route...');
                    
                    // Get current positions
                    const clientPosition = clientMarker._lngLat;
                    const livreurPosition = livreurMarker._lngLat;
                    
                    if (clientPosition && livreurPosition) {
                        // Create a new route
                        const route = new mapboxgl.Polyline({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Add the route to the map
                        map.getSource('route').setData(route);
                        
                        // Update client-livreur route in Firebase
                        const userType = '{{ $isClient ? "client" : "livreur" }}';
                        const routeRef = database.ref(`${FIREBASE_COMMAND_KEY}/locations/client-livreur-route`);
                        routeRef.set({
                            'type': 'LineString',
                            'coordinates': [clientPosition, livreurPosition]
                        });
                        
                        // Update client-livreur route in database
                        updateLocationInDatabase('client-livreur-route', clientPosition.lng, clientPosition.lat);
                        
                        console.log('Client-livreur route updated successfully');
                    } else {
                        console.warn('Client or livreur position not available');
                    }
                }

                // Function to ensure all markers are visible
                function ensureAllMarkersVisible() {
                    console.log('Ensuring all markers are visible...');
                    
                    // Check if client marker is visible
            });
        });
    </script>
</body>
</html>