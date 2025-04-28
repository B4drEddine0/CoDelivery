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
            
            <button id="share-location-btn" class="px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 flex items-center">
                <i class="fa-solid fa-location-dot mr-2"></i>
                <span>Partager ma localisation</span>
            </button>
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
                
                // References to database locations with enhanced structure
                const commandRef = database.ref(`commands/{{ $command->id }}`);
                const locationsRef = commandRef.child('locations');
                const clientLocationRef = locationsRef.child('client');
                const livreurLocationRef = locationsRef.child('livreur');
                
                // For debugging
                commandRef.once('value', snapshot => {
                    console.log('Firebase command data:', snapshot.val());
                });
                
                // Listen for livreur location changes with enhanced notification
                livreurLocationRef.on('value', (snapshot) => {
                    const data = snapshot.val();
                    console.log('Livreur location update received:', data);
                    
                    if (data && data.lat && data.lng) {
                        // Update livreur marker on the map
                        livreurMarker.setLngLat([data.lng, data.lat]).addTo(map);
                        
                        // Update status indicator
                        updateLivreurStatus(true, formatTimestamp(data.timestamp));
                        
                        // If client is viewing, add visual notification
                        if ('{{ $isClient }}' === '1') {
                            // Flash the livreur marker to make it noticeable
                            const markerEl = livreurMarker.getElement();
                            markerEl.style.transform = 'scale(1.3)';
                            setTimeout(() => {
                                markerEl.style.transform = 'scale(1)';
                            }, 700);
                            
                            // Show notification
                            showNotification('Position du livreur mise à jour', 'success');
                            
                            // Center map on livreur with smooth animation
                            map.flyTo({
                                center: [data.lng, data.lat],
                                zoom: 15,
                                duration: 1500
                            });
                            
                            // Draw route between pickup and livreur if map is loaded
                            if (map.isStyleLoaded() && map.getSource('route')) {
                                try {
                                    // Update the route coordinates 
                                    const routeData = {
                                        'type': 'Feature',
                                        'properties': {},
                                        'geometry': {
                                            'type': 'LineString',
                                            'coordinates': [
                                                [{{ $command->pickup_longitude ?? 'data.lng' }}, {{ $command->pickup_latitude ?? 'data.lat' }}],
                                                [data.lng, data.lat]
                                            ]
                                        }
                                    };
                                    map.getSource('route').setData(routeData);
                                } catch (error) {
                                    console.error('Error updating route:', error);
                                }
                            }
                        }
                        
                        // Fit all markers on the map
                        fitMapToMarkers();
                    }
                }, (error) => {
                    console.error('Error with livreur location listener:', error);
                });
                
                // Listen for client location changes
                clientLocationRef.on('value', (snapshot) => {
                    const data = snapshot.val();
                    console.log('Client location update received:', data);
                    
                    if (data && data.lat && data.lng) {
                        clientMarker.setLngLat([data.lng, data.lat]).addTo(map);
                        updateClientStatus(true, formatTimestamp(data.timestamp));
                        fitMapToMarkers();
                    }
                }, (error) => {
                    console.error('Error with client location listener:', error);
                });
                
                // Format timestamp for display
                function formatTimestamp(timestamp) {
                    if (!timestamp) return '';
                    const date = new Date(timestamp);
                    return `${date.getHours()}:${String(date.getMinutes()).padStart(2, '0')}`;
                }
                
                // Fetch initial location data from server with retry mechanism
                function fetchLocationData(retries = 3) {
                    fetch(`/api/commands/{{ $command->id }}/location`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                const command = data.command;
                                console.log('Initial location data:', command);
                                
                                // If client location is available, add marker
                                if (command.client.lat && command.client.lng) {
                                    clientMarker.setLngLat([command.client.lng, command.client.lat]).addTo(map);
                                    updateClientStatus(true, command.client.updated_at);
                                    
                                    // Also update Firebase
                                    clientLocationRef.set({
                                        lat: command.client.lat,
                                        lng: command.client.lng,
                                        timestamp: firebase.database.ServerValue.TIMESTAMP
                                    });
                                }
                                
                                // If livreur location is available, add marker
                                if (command.livreur.lat && command.livreur.lng) {
                                    livreurMarker.setLngLat([command.livreur.lng, command.livreur.lat]).addTo(map);
                                    updateLivreurStatus(true, command.livreur.updated_at);
                                    
                                    // Also update Firebase
                                    livreurLocationRef.set({
                                        lat: command.livreur.lat,
                                        lng: command.livreur.lng,
                                        timestamp: firebase.database.ServerValue.TIMESTAMP
                                    });
                                }
                                
                                // Add display text showing if livreur location is visible
                                const statusElement = document.createElement('div');
                                statusElement.className = 'absolute bottom-2 right-2 bg-white bg-opacity-80 px-3 py-1 rounded-lg shadow-md text-sm font-medium z-20';
                                if (command.livreur.lat && command.livreur.lng) {
                                    statusElement.textContent = 'Position du livreur mise à jour';
                                    statusElement.classList.add('text-green-600');
                                } else if ('{{ $isClient }}' === '1' && '{{ $command->livreur_id }}') {
                                    statusElement.textContent = 'En attente de la position du livreur...';
                                    statusElement.classList.add('text-orange-600');
                                }
                                document.querySelector('.map-container').appendChild(statusElement);
                                
                                // Fit map to include all markers
                                fitMapToMarkers();
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching location data:', error);
                            if (retries > 0) {
                                console.log(`Retrying fetch... ${retries} attempts left`);
                                setTimeout(() => fetchLocationData(retries - 1), 2000);
                            }
                        })
                        .finally(() => {
                            // Hide loading indicator
                            document.getElementById('loading').style.display = 'none';
                        });
                }
                
                // Start fetching data
                fetchLocationData();
                
                // Update status indicators with animated transitions
                function updateClientStatus(isOnline, lastUpdated = null) {
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
                    }
                }
                
                function updateLivreurStatus(isOnline, lastUpdated = null) {
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
                    }
                }
                
                // Improved fit map function to handle edge cases
                function fitMapToMarkers() {
                    try {
                        const bounds = new mapboxgl.LngLatBounds();
                        let hasMarkers = false;
                        
                        // Check if markers have valid coordinates and add to bounds
                        @if($command->pickup_latitude && $command->pickup_longitude)
                        bounds.extend([{{ $command->pickup_longitude }}, {{ $command->pickup_latitude }}]);
                        hasMarkers = true;
                        @endif
                        
                        @if($command->delivery_latitude && $command->delivery_longitude)
                        bounds.extend([{{ $command->delivery_longitude }}, {{ $command->delivery_latitude }}]);
                        hasMarkers = true;
                        @endif
                        
                        if (clientMarker._lngLat) {
                            bounds.extend([clientMarker._lngLat.lng, clientMarker._lngLat.lat]);
                            hasMarkers = true;
                        }
                        
                        if (livreurMarker._lngLat) {
                            bounds.extend([livreurMarker._lngLat.lng, livreurMarker._lngLat.lat]);
                            hasMarkers = true;
                        }
                        
                        // Only fit bounds if we have added points
                        if (hasMarkers && !bounds.isEmpty()) {
                            map.fitBounds(bounds, {
                                padding: 70,
                                maxZoom: 15,
                                duration: 1000
                            });
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
                
                // Enhanced share location button with automatic refresh
                let locationWatchId = null;
                document.getElementById('share-location-btn').addEventListener('click', function() {
                    if (navigator.geolocation) {
                        this.disabled = true;
                        this.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i><span>Partage en cours...</span>';
                        
                        // Stop previous location watching if it exists
                        if (locationWatchId) {
                            navigator.geolocation.clearWatch(locationWatchId);
                        }
                        
                        // Function to update location
                        const updateLocation = (position) => {
                            const { latitude, longitude } = position.coords;
                            const userType = '{{ $isClient ? "client" : "livreur" }}';
                            
                            // Update in Firebase
                            if (userType === 'client') {
                                clientLocationRef.set({
                                    lat: latitude,
                                    lng: longitude,
                                    timestamp: firebase.database.ServerValue.TIMESTAMP
                                });
                            } else {
                                livreurLocationRef.set({
                                    lat: latitude,
                                    lng: longitude,
                                    timestamp: firebase.database.ServerValue.TIMESTAMP
                                });
                                
                                // Also update status to indicate active sharing
                                commandRef.child('status').set({
                                    livreurSharingLocation: true,
                                    lastUpdated: firebase.database.ServerValue.TIMESTAMP
                                });
                            }
                            
                            // Also update in Laravel backend
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
                                    type: userType
                                })
                            })
                            .catch(error => {
                                console.error('Error updating location in backend:', error);
                            });
                        };
                        
                        // Get position once immediately
                        navigator.geolocation.getCurrentPosition(updateLocation, 
                        (error) => {
                            console.error('Geolocation error:', error);
                            this.innerHTML = '<i class="fa-solid fa-exclamation-triangle mr-2"></i><span>Accès refusé</span>';
                            setTimeout(() => {
                                this.innerHTML = '<i class="fa-solid fa-location-dot mr-2"></i><span>Partager ma localisation</span>';
                                this.disabled = false;
                            }, 2000);
                        }, 
                        { enableHighAccuracy: true });
                        
                        // Then watch position for continuous updates
                        locationWatchId = navigator.geolocation.watchPosition(updateLocation, 
                        (error) => {
                            console.error('Geolocation watch error:', error);
                            navigator.geolocation.clearWatch(locationWatchId);
                            locationWatchId = null;
                            this.innerHTML = '<i class="fa-solid fa-location-dot mr-2"></i><span>Partager ma localisation</span>';
                            this.disabled = false;
                        }, 
                        { enableHighAccuracy: true, maximumAge: 10000 });
                        
                        // Update button style to indicate active sharing
                        this.innerHTML = '<i class="fa-solid fa-broadcast-tower mr-2"></i><span>Position partagée en temps réel</span>';
                        this.classList.remove('bg-orange-500', 'hover:bg-orange-600');
                        this.classList.add('bg-green-500', 'hover:bg-green-600');
                        
                        // Create stop sharing button
                        if (!document.getElementById('stop-sharing-btn')) {
                            const stopSharingBtn = document.createElement('button');
                            stopSharingBtn.id = 'stop-sharing-btn';
                            stopSharingBtn.className = 'px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 flex items-center ml-2';
                            stopSharingBtn.innerHTML = '<i class="fa-solid fa-stop-circle mr-2"></i><span>Arrêter le partage</span>';
                            stopSharingBtn.addEventListener('click', function() {
                                if (locationWatchId) {
                                    navigator.geolocation.clearWatch(locationWatchId);
                                    locationWatchId = null;
                                    
                                    const shareBtn = document.getElementById('share-location-btn');
                                    shareBtn.innerHTML = '<i class="fa-solid fa-location-dot mr-2"></i><span>Partager ma localisation</span>';
                                    shareBtn.disabled = false;
                                    shareBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
                                    shareBtn.classList.add('bg-orange-500', 'hover:bg-orange-600');
                                    
                                    // Remove the stop sharing button
                                    this.remove();
                                }
                            });
                            
                            // Add the stop sharing button next to the share button
                            this.parentNode.appendChild(stopSharingBtn);
                        }
                    } else {
                        alert('La géolocalisation n\'est pas supportée par votre navigateur.');
                    }
                });
                
                // Wait for map to load before fitting bounds
                map.on('load', function() {
                    console.log('Map loaded');
                    fitMapToMarkers();
                    
                    // Add a route layer for display if we're showing livreur movement
                    if ('{{ $command->status }}' === 'in_progress' || '{{ $command->status }}' === 'accepted') {
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
                    }
                });
            } catch (error) {
                console.error('Error initializing map or Firebase:', error);
            }
        });
        
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
    </script>
</body>
</html>