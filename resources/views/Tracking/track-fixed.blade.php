@extends('layouts.app')

@section('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        #map {
            height: 70vh;
            width: 100%;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            border-radius: 0.5rem;
        }
        .map-container {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .location-info {
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 0.5rem;
            background-color: #f3f4f6;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .status-badge.online {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-badge.offline {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .status-badge.pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .tracking-controls {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        .tracking-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .info-card {
            background-color: white;
            border-radius: 0.5rem;
            padding: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            margin-bottom: 1rem;
        }
        .info-card h3 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .info-card p {
            margin-bottom: 0.25rem;
            color: #4b5563;
        }
        .pulse {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #22c55e;
            margin-right: 6px;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
            }
        }
        .message-container {
            max-height: 200px;
            overflow-y: auto;
            padding: 0.5rem;
            background-color: #f9fafb;
            border-radius: 0.375rem;
            margin-bottom: 0.5rem;
        }
        .message {
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            border-radius: 0.375rem;
        }
        .message.client {
            background-color: #e0f2fe;
            margin-right: 20%;
        }
        .message.livreur {
            background-color: #dcfce7;
            margin-left: 20%;
        }
        .message-form {
            display: flex;
            gap: 0.5rem;
        }
        .message-input {
            flex: 1;
            padding: 0.5rem;
            border-radius: 0.375rem;
            border: 1px solid #d1d5db;
        }
        .message-btn {
            background-color: #3b82f6;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            border: none;
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Suivi de commande #{{ $command->id }}</h1>
            <div>
                <span class="status-badge {{ $command->status === 'delivered' ? 'bg-green-100 text-green-800' : ($command->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                    {{ $command->status === 'delivered' ? 'Livré' : ($command->status === 'in_progress' ? 'En cours' : 'En attente') }}
                </span>
            </div>
        </div>
        
        <div class="tracking-controls">
            <div>
                <button id="centerMapBtn" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                    <i class="fas fa-crosshairs"></i> Centrer la carte
                </button>
            </div>
            <div>
                @if($userRole === 'livreur')
                <button id="shareLocationBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                    <i class="fas fa-location-arrow"></i> Partager ma position
                </button>
                @endif
            </div>
        </div>
        
        <div class="map-container">
            <div id="mapLoading" class="loading-overlay">
                <div class="text-center">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500 mb-2"></div>
                    <p>Chargement de la carte...</p>
                </div>
            </div>
            <div id="map"></div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div class="md:col-span-2">
                <div class="info-card">
                    <h3><i class="fas fa-info-circle text-blue-500"></i> Détails de la commande</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 text-sm">Service</p>
                            <p class="font-medium">{{ $command->service_type }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Établissement</p>
                            <p class="font-medium">{{ $command->establishment_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Adresse de retrait</p>
                            <p class="font-medium">{{ $command->pickup_address }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Adresse de livraison</p>
                            <p class="font-medium">{{ $command->delivery_address }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3><i class="fas fa-comments text-green-500"></i> Communication</h3>
                    <div class="message-container" id="messageContainer">
                        <!-- Messages will be added here dynamically -->
                        <div class="text-center text-gray-500 text-sm py-2">Aucun message pour le moment</div>
                    </div>
                    <div class="message-form">
                        <input type="text" id="messageInput" class="message-input" placeholder="Écrivez un message...">
                        <button id="sendMessageBtn" class="message-btn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="tracking-info">
                <div class="info-card">
                    <h3><i class="fas fa-truck text-blue-500"></i> Statut de livraison</h3>
                    <div class="flex items-center mb-3">
                        <span id="livreurStatusIndicator" class="pulse"></span>
                        <span id="livreurStatus" class="status-badge offline">Hors ligne</span>
                    </div>
                    <p><strong>Livreur:</strong> {{ $command->livreur ? $command->livreur->name : 'Non assigné' }}</p>
                    <p><strong>Client:</strong> {{ $command->client ? $command->client->name : 'Inconnu' }}</p>
                    <p><strong>Dernière mise à jour:</strong> <span id="lastUpdated">--:--:--</span></p>
                </div>
                
                <div class="info-card">
                    <h3><i class="fas fa-map-marker-alt text-red-500"></i> Points de passage</h3>
                    <div class="mb-2">
                        <p class="font-medium">Point de retrait:</p>
                        <p class="text-sm text-gray-600">{{ $command->pickup_address }}</p>
                    </div>
                    <div>
                        <p class="font-medium">Point de livraison:</p>
                        <p class="text-sm text-gray-600">{{ $command->delivery_address }}</p>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3><i class="fas fa-clock text-yellow-500"></i> Chronologie</h3>
                    <div class="relative pl-4 border-l-2 border-gray-200">
                        <div class="mb-3">
                            <div class="absolute -left-1.5 mt-1.5 h-3 w-3 rounded-full bg-blue-500"></div>
                            <p class="font-medium">Commande créée</p>
                            <p class="text-xs text-gray-500">{{ $command->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @if($command->accepted_at)
                        <div class="mb-3">
                            <div class="absolute -left-1.5 mt-1.5 h-3 w-3 rounded-full bg-yellow-500"></div>
                            <p class="font-medium">Commande acceptée</p>
                            <p class="text-xs text-gray-500">{{ $command->accepted_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                        @if($command->delivered_at)
                        <div>
                            <div class="absolute -left-1.5 mt-1.5 h-3 w-3 rounded-full bg-green-500"></div>
                            <p class="font-medium">Commande livrée</p>
                            <p class="text-xs text-gray-500">{{ $command->delivered_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <!-- Socket.IO -->
    <script src="https://cdn.socket.io/4.6.0/socket.io.min.js" integrity="sha384-c79GN5VsunZvi+Q/WObgk2in0CbZsHnjEqvFxC5DxHn9lTfNce2WW6h2pH6u/kF+" crossorigin=""></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Map variables
            let map, clientMarker, livreurMarker, pickupMarker, deliveryMarker;
            let watchId = null;
            let socket = null;
            let isConnected = false;
            
            // Command data
            const commandId = {{ $command->id }};
            const userRole = '{{ $userRole }}';
            const isLivreur = userRole === 'livreur';
            const isClient = userRole === 'client';
            
            // Debug info
            console.log('User role:', userRole);
            console.log('Command ID:', commandId);
            
            // Initial coordinates from controller
            const pickupCoords = [{{ $pickupCoords[0] }}, {{ $pickupCoords[1] }}];
            const deliveryCoords = [{{ $deliveryCoords[0] }}, {{ $deliveryCoords[1] }}];
            
            console.log('Pickup coordinates:', pickupCoords);
            console.log('Delivery coordinates:', deliveryCoords);
            
            // Icons
            const icons = {
                client: L.icon({
                    iconUrl: 'https://cdn.jsdelivr.net/gh/pointhi/leaflet-color-markers@master/img/marker-icon-2x-blue.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                    shadowSize: [41, 41]
                }),
                livreur: L.icon({
                    iconUrl: 'https://cdn.jsdelivr.net/gh/pointhi/leaflet-color-markers@master/img/marker-icon-2x-green.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                    shadowSize: [41, 41]
                }),
                pickup: L.icon({
                    iconUrl: 'https://cdn.jsdelivr.net/gh/pointhi/leaflet-color-markers@master/img/marker-icon-2x-gold.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                    shadowSize: [41, 41]
                }),
                delivery: L.icon({
                    iconUrl: 'https://cdn.jsdelivr.net/gh/pointhi/leaflet-color-markers@master/img/marker-icon-2x-red.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                    shadowSize: [41, 41]
                })
            };
            
            // Initialize map
            function initMap() {
                try {
                    console.log('Initializing map...');
                    
                    // Create map centered between pickup and delivery
                    const centerLat = (pickupCoords[0] + deliveryCoords[0]) / 2;
                    const centerLng = (pickupCoords[1] + deliveryCoords[1]) / 2;
                    
                    map = L.map('map').setView([centerLat, centerLng], 13);
                    
                    // Add tile layer
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);
                    
                    console.log('Map created successfully');
                    
                    // Add markers
                    addMarkers();
                    
                    // Hide loading overlay
                    document.getElementById('mapLoading').style.display = 'none';
                    
                    // Connect to Socket.IO
                    connectToSocket();
                    
                } catch (error) {
                    console.error('Error initializing map:', error);
                    document.getElementById('mapLoading').style.display = 'none';
                    document.getElementById('map').innerHTML = '<div class="p-4 bg-red-100 text-red-600 rounded">Erreur de chargement de la carte</div>';
                }
            }
            
            // Add all markers to the map
            function addMarkers() {
                console.log('Adding markers...');
                
                // Add client marker
                clientMarker = L.marker(pickupCoords, {icon: icons.client})
                    .addTo(map)
                    .bindPopup('Client');
                
                // Add livreur marker
                livreurMarker = L.marker(deliveryCoords, {
                    icon: icons.livreur,
                    zIndexOffset: 1000 // Ensure it's on top of other markers
                })
                .addTo(map)
                .bindPopup('Livreur');
                
                // Add pickup marker
                pickupMarker = L.marker(pickupCoords, {icon: icons.pickup})
                    .addTo(map)
                    .bindPopup('Point de retrait');
                
                // Add delivery marker
                deliveryMarker = L.marker(deliveryCoords, {icon: icons.delivery})
                    .addTo(map)
                    .bindPopup('Point de livraison');
                
                // Fit bounds to show all markers
                const bounds = L.latLngBounds([
                    pickupCoords,
                    deliveryCoords
                ]);
                map.fitBounds(bounds, {padding: [50, 50]});
            }
            
            // Connect to Socket.IO server
            function connectToSocket() {
                try {
                    console.log('Connecting to Socket.IO server...');
                    socket = io('http://localhost:3000');
                    
                    socket.on('connect', () => {
                        console.log('Connected to Socket.IO server with ID:', socket.id);
                        isConnected = true;
                        
                        // Join the command room
                        socket.emit('joinCommand', {
                            commandId: commandId,
                            userRole: userRole
                        });
                    });
                    
                    socket.on('joinedCommand', (data) => {
                        console.log('Joined command room:', data);
                    });
                    
                    socket.on('userJoined', (data) => {
                        console.log('User joined:', data);
                        
                        // Update status if livreur joined
                        if (data.userRole === 'livreur') {
                            updateLivreurStatus(true);
                        }
                    });
                    
                    socket.on('userLeft', (data) => {
                        console.log('User left:', data);
                        
                        // Update status if livreur left
                        if (data.userRole === 'livreur') {
                            updateLivreurStatus(false);
                        }
                    });
                    
                    socket.on('locationUpdate', (data) => {
                        console.log('Received location update:', data);
                        
                        if (data.userRole === 'livreur') {
                            updateLivreurMarker([data.latitude, data.longitude]);
                        } else if (data.userRole === 'client') {
                            updateClientMarker([data.latitude, data.longitude]);
                        }
                    });
                    
                    socket.on('newMessage', (data) => {
                        console.log('Received message:', data);
                        addMessage(data);
                    });
                    
                    socket.on('disconnect', () => {
                        console.log('Disconnected from Socket.IO server');
                        isConnected = false;
                        
                        // Update livreur status
                        updateLivreurStatus(false);
                    });
                    
                    // Setup message sending
                    document.getElementById('sendMessageBtn').addEventListener('click', sendMessage);
                    document.getElementById('messageInput').addEventListener('keypress', (e) => {
                        if (e.key === 'Enter') {
                            sendMessage();
                        }
                    });
                    
                } catch (error) {
                    console.error('Error connecting to Socket.IO:', error);
                }
            }
            
            // Update livreur marker position
            function updateLivreurMarker(coordinates) {
                if (livreurMarker && coordinates && coordinates.length === 2) {
                    console.log('Updating livreur marker to:', coordinates);
                    livreurMarker.setLatLng(coordinates);
                    
                    // Update status display
                    updateLivreurStatus(true);
                    
                    // Update last updated time
                    document.getElementById('lastUpdated').textContent = new Date().toLocaleTimeString();
                }
            }
            
            // Update client marker position
            function updateClientMarker(coordinates) {
                if (clientMarker && coordinates && coordinates.length === 2) {
                    console.log('Updating client marker to:', coordinates);
                    clientMarker.setLatLng(coordinates);
                }
            }
            
            // Update livreur status display
            function updateLivreurStatus(isOnline) {
                const statusElement = document.getElementById('livreurStatus');
                const indicatorElement = document.getElementById('livreurStatusIndicator');
                
                if (isOnline) {
                    statusElement.textContent = 'En ligne';
                    statusElement.className = 'status-badge online';
                    indicatorElement.style.display = 'inline-block';
                } else {
                    statusElement.textContent = 'Hors ligne';
                    statusElement.className = 'status-badge offline';
                    indicatorElement.style.display = 'none';
                }
            }
            
            // Start sharing location if user is livreur
            function startLocationSharing() {
                if (navigator.geolocation) {
                    console.log('Starting location sharing...');
                    
                    // Get location once
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const coordinates = [position.coords.latitude, position.coords.longitude];
                            console.log('Initial position:', coordinates);
                            sendLocationUpdate(coordinates);
                        },
                        (error) => {
                            console.error('Geolocation error:', error);
                        },
                        { enableHighAccuracy: true }
                    );
                    
                    // Watch position for changes
                    watchId = navigator.geolocation.watchPosition(
                        (position) => {
                            const coordinates = [position.coords.latitude, position.coords.longitude];
                            console.log('Position update:', coordinates);
                            sendLocationUpdate(coordinates);
                        },
                        (error) => {
                            console.error('Geolocation watch error:', error);
                        },
                        { enableHighAccuracy: true, maximumAge: 10000, timeout: 10000 }
                    );
                    
                    // Update share button
                    const shareBtn = document.getElementById('shareLocationBtn');
                    shareBtn.innerHTML = '<i class="fas fa-check"></i> Position partagée';
                    shareBtn.classList.remove('bg-blue-500', 'hover:bg-blue-600');
                    shareBtn.classList.add('bg-green-500', 'hover:bg-green-600');
                    
                } else {
                    console.error('Geolocation is not supported by this browser');
                }
            }
            
            // Send location update to server
            function sendLocationUpdate(coordinates) {
                if (coordinates && coordinates.length === 2 && socket && isConnected) {
                    // Update our own marker
                    if (userRole === 'livreur') {
                        updateLivreurMarker(coordinates);
                    } else if (userRole === 'client') {
                        updateClientMarker(coordinates);
                    }
                    
                    // Send to Socket.IO server
                    socket.emit('locationUpdate', {
                        commandId: commandId,
                        userRole: userRole,
                        latitude: coordinates[0],
                        longitude: coordinates[1]
                    });
                }
            }
            
            // Send message
            function sendMessage() {
                const messageInput = document.getElementById('messageInput');
                const message = messageInput.value.trim();
                
                if (message && socket && isConnected) {
                    socket.emit('sendMessage', {
                        commandId: commandId,
                        userRole: userRole,
                        message: message
                    });
                    
                    // Clear input
                    messageInput.value = '';
                }
            }
            
            // Add message to container
            function addMessage(data) {
                const container = document.getElementById('messageContainer');
                
                // Remove "no messages" placeholder if it exists
                const placeholder = container.querySelector('.text-center.text-gray-500');
                if (placeholder) {
                    placeholder.remove();
                }
                
                // Create message element
                const messageElement = document.createElement('div');
                messageElement.className = `message ${data.userRole}`;
                
                const timestamp = new Date(data.timestamp).toLocaleTimeString();
                const sender = data.userRole === 'client' ? 'Client' : 'Livreur';
                
                messageElement.innerHTML = `
                    <div class="text-xs text-gray-500 mb-1">${sender} - ${timestamp}</div>
                    <div>${data.message}</div>
                `;
                
                // Add to container
                container.appendChild(messageElement);
                
                // Scroll to bottom
                container.scrollTop = container.scrollHeight;
            }
            
            // Add event listener for share location button
            if (isLivreur) {
                document.getElementById('shareLocationBtn').addEventListener('click', startLocationSharing);
            }
            
            // Add center map button functionality
            document.getElementById('centerMapBtn').addEventListener('click', () => {
                if (map) {
                    const bounds = L.latLngBounds([
                        clientMarker.getLatLng(),
                        livreurMarker.getLatLng(),
                        pickupMarker.getLatLng(),
                        deliveryMarker.getLatLng()
                    ]);
                    map.fitBounds(bounds, {padding: [50, 50]});
                }
            });
            
            // Initialize map
            initMap();
        });
    </script>
@endsection
