<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CoDelivery - Suivre ma commande #{{ $command->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                        },
                        accent: {
                            50: '#fff1f2',
                            100: '#ffe4e6',
                            500: '#f43f5e',
                            600: '#e11d48',
                        },
                        orange: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                            950: '#431407',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .animate-pulse-slow {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        #map {
            width: 100%;
            height: 100%;
        }
        
        .leaflet-marker-icon {
            transition: all 0.3s ease-in-out;
        }
        
        .leaflet-popup-content p {
            margin: 0.5em 0;
        }
    </style>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="/"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-gradient-to-r from-orange-800 to-orange-950 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ auth()->user()->isClient() ? route('client.dashboard') : route('livreur.dashboard') }}" class="flex items-center space-x-2">
                    <svg class="w-10 h-10" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Logo SVG code -->
                        <path d="M32 4c-11 0-20 9-20 20 0 11 20 36 20 36s20-25 20-36c0-11-9-20-20-20z" fill="#EA580C"/>
                        <circle cx="32" cy="24" r="12" fill="#FB923C"/>
                        <rect x="24" y="18" width="16" height="12" fill="#FFFFFF"/>
                        <path d="M24 22h16M32 18v12" stroke="#EA580C" stroke-width="1.5"/>
                        <path d="M14 44l-6 6M50 44l6 6" stroke="#FB923C" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                    <span class="text-xl font-bold">CoDelivery</span>
                </a>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ auth()->user()->isClient() ? route('client.dashboard') : route('livreur.dashboard') }}" class="text-white hover:text-orange-300 transition-colors">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Retour au tableau de bord
                    </a>
                    @if(auth()->user()->isLivreur() && $command->livreur_id === auth()->id() && $command->isInProgress())
                    <button id="share-location-btn" class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-1.5 px-4 rounded-lg transition-colors flex items-center">
                        <i class="fa-solid fa-location-crosshairs mr-2"></i> Partager ma position
                    </button>
                    @endif
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
    
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Order Tracking Header -->
        <div class="bg-white rounded-xl p-6 shadow-md mb-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-2">Suivi de commande #{{ $command->id }}</h1>
                    <p class="text-gray-600">{{ ucfirst($command->service_type) }} - <span class="font-medium">{{ $command->establishment_name }}</span></p>
                </div>
                <div class="mt-4 md:mt-0 flex items-center space-x-2 {{ $command->status === 'in_progress' ? 'bg-green-100' : ($command->status === 'accepted' ? 'bg-blue-100' : 'bg-gray-100') }} px-4 py-2 rounded-full">
                    <div class="w-3 h-3 {{ $command->status === 'in_progress' ? 'bg-green-500' : ($command->status === 'accepted' ? 'bg-blue-500' : 'bg-gray-500') }} rounded-full animate-pulse-slow"></div>
                    <span class="{{ $command->status === 'in_progress' ? 'text-green-800' : ($command->status === 'accepted' ? 'text-blue-800' : 'text-gray-800') }} font-medium">
                        @if($command->status === 'in_progress')
                            En route - Arrivée dans {{ $locationTracking->estimated_delivery_time ?? '15' }} min
                        @elseif($command->status === 'accepted')
                            Acceptée - En préparation
                        @elseif($command->status === 'delivered')
                            Livrée
                        @else
                            {{ ucfirst($command->status) }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Map and Details Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Live Map (Takes 2/3 of the space on larger screens) -->
            <div class="lg:col-span-2 bg-white rounded-xl overflow-hidden shadow-md h-[400px] md:h-[500px] relative">
                <!-- Interactive Map -->
                <div id="map" class="w-full h-full"></div>
                
                <!-- Map Loading Overlay -->
                <div id="map-loading" class="absolute inset-0 bg-white bg-opacity-80 flex items-center justify-center z-10">
                    <div class="text-center">
                        <div class="w-16 h-16 border-4 border-orange-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                        <p class="text-gray-700 font-medium">Chargement de la carte...</p>
                    </div>
                </div>
            </div>
            
            <!-- Order Details Sidebar -->
            <div class="bg-white rounded-xl p-6 shadow-md">
                <h2 class="text-xl font-bold mb-6">Détails de la commande</h2>
                
                <!-- Delivery Progress -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4">Progression</h3>
                    <div class="relative">
                        <!-- Progress Line -->
                        <div class="absolute top-0 left-6 mt-2 h-full w-0.5 bg-gray-200"></div>
                        
                        <!-- Steps -->
                        <div class="space-y-6">
                            <!-- Step 1: Confirmed -->
                            <div class="flex items-start relative">
                                <div class="flex-shrink-0 w-12">
                                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center z-10 relative">
                                        <i class="fa-solid fa-check text-white"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-md font-semibold">Commande confirmée</h4>
                                    <p class="text-sm text-gray-500">{{ $command->created_at->format('H:i') }}</p>
                                </div>
                            </div>
                            
                            <!-- Step 2: Preparation -->
                            <div class="flex items-start relative">
                                <div class="flex-shrink-0 w-12">
                                    <div class="w-12 h-12 {{ $command->status === 'pending' ? 'bg-gray-300' : 'bg-green-500' }} rounded-full flex items-center justify-center z-10 relative">
                                        @if($command->service_type === 'restaurant')
                                            <i class="fa-solid fa-utensils text-white"></i>
                                        @elseif($command->service_type === 'pharmacy')
                                            <i class="fa-solid fa-prescription-bottle-medical text-white"></i>
                                        @elseif($command->service_type === 'market')
                                            <i class="fa-solid fa-shopping-basket text-white"></i>
                                        @else
                                            <i class="fa-solid fa-box text-white"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-md font-semibold">Préparation</h4>
                                    @if($command->accepted_at)
                                        <p class="text-sm text-gray-500">{{ $command->accepted_at->format('H:i') }}</p>
                                    @else
                                        <p class="text-sm text-gray-500">En attente</p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Step 3: On the way -->
                            <div class="flex items-start relative">
                                <div class="flex-shrink-0 w-12">
                                    <div class="w-12 h-12 {{ $command->status === 'in_progress' ? 'bg-orange-500 animate-pulse-slow' : ($command->status === 'delivered' ? 'bg-green-500' : 'bg-gray-300') }} rounded-full flex items-center justify-center z-10 relative">
                                        <i class="fa-solid fa-motorcycle text-white"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-md font-semibold">En route</h4>
                                    @if($command->status === 'in_progress')
                                        <p class="text-sm text-gray-500">En cours</p>
                                    @elseif($command->status === 'delivered')
                                        <p class="text-sm text-gray-500">Terminé</p>
                                    @else
                                        <p class="text-sm text-gray-500">En attente</p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Step 4: Delivered -->
                            <div class="flex items-start relative {{ $command->status !== 'delivered' ? 'opacity-50' : '' }}">
                                <div class="flex-shrink-0 w-12">
                                    <div class="w-12 h-12 {{ $command->status === 'delivered' ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center z-10 relative">
                                        <i class="fa-solid fa-flag-checkered text-white"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-md font-semibold">Livré</h4>
                                    @if($command->delivered_at)
                                        <p class="text-sm text-gray-500">{{ $command->delivered_at->format('H:i') }}</p>
                                    @elseif($command->status === 'in_progress' && isset($locationTracking->estimated_delivery_time))
                                        <p class="text-sm text-gray-500">Estimé dans {{ $locationTracking->estimated_delivery_time }} min</p>
                                    @else
                                        <p class="text-sm text-gray-500">En attente</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Delivery Person Info -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-3">Votre livreur</h3>
                    @if($command->livreur)
                    <div class="flex items-center">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mr-4 text-2xl font-bold text-orange-600">
                            {{ substr($command->livreur->first_name, 0, 1) }}{{ substr($command->livreur->last_name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="font-medium">{{ $command->livreur->first_name }} {{ $command->livreur->last_name }}</h4>
                            <div class="flex items-center mt-1">
                                <div class="flex text-yellow-400">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star-half-alt"></i>
                                </div>
                                <span class="ml-1 text-sm text-gray-600">4.5</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex space-x-2">
                        <button class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-phone mr-2"></i> Appeler
                        </button>
                        <button class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-comment mr-2"></i> Message
                        </button>
                    </div>
                    @else
                    <div class="bg-gray-100 rounded-lg p-4 text-center">
                        <p class="text-gray-600">Aucun livreur n'a encore accepté cette commande.</p>
                    </div>
                    @endif
                </div>
                
                <!-- Order Summary -->
                <div>
                    <h3 class="text-lg font-semibold mb-3">Résumé</h3>
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ ucfirst($command->service_type) }}</span>
                            <span>{{ $command->establishment_name }}</span>
                        </div>
                        
                        @if($command->description)
                        <div class="bg-gray-50 p-3 rounded-lg mt-2 mb-2">
                            <p class="text-sm text-gray-700">{{ $command->description }}</p>
                        </div>
                        @endif
                        
                        <div class="flex justify-between mt-2">
                            <span class="text-gray-600">Adresse de retrait</span>
                            <span class="text-right text-sm max-w-[60%]">{{ $command->pickup_address }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Adresse de livraison</span>
                            <span class="text-right text-sm max-w-[60%]">{{ $command->delivery_address }}</span>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-2 mt-2">
                            <div class="flex justify-between font-medium">
                                <span>Prix de livraison</span>
                                <span>{{ number_format($command->price, 2) }} DH</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600 mt-1">
                                <span>Priorité</span>
                                <span class="{{ $command->priority === 'high' ? 'text-red-600 font-medium' : ($command->priority === 'medium' ? 'text-orange-600' : 'text-blue-600') }}">
                                    {{ $command->priority === 'high' ? 'Urgent' : ($command->priority === 'medium' ? 'Standard' : 'Basse') }}
                                </span>
                            </div>
                            <div class="flex justify-between font-bold text-lg mt-2">
                                <span>Total</span>
                                <span>{{ number_format($command->price, 2) }} DH</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="bg-gradient-to-r from-orange-800 to-orange-950 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-2 mb-4 md:mb-0">
                    <svg class="w-10 h-10" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Logo SVG code -->
                        <path d="M32 4c-11 0-20 9-20 20 0 11 20 36 20 36s20-25 20-36c0-11-9-20-20-20z" fill="#EA580C"/>
                        <circle cx="32" cy="24" r="12" fill="#FB923C"/>
                        <rect x="24" y="18" width="16" height="12" fill="#FFFFFF"/>
                        <path d="M24 22h16M32 18v12" stroke="#EA580C" stroke-width="1.5"/>
                    </svg>
                    <span class="text-xl font-bold">CoDelivery</span>
                </div>
                <div class="text-sm text-gray-400">
                    © 2025 CoDelivery. Tous droits réservés.
                </div>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">Aide</a>
                    <a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">Confidentialité</a>
                    <a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">Conditions</a>
                </div>
            </div>
        </div>
    </footer>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="/"></script>
    
    <!-- Pusher JS -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Map initialization
            const mapElement = document.getElementById('map');
            const mapLoading = document.getElementById('map-loading');
            const shareLocationBtn = document.getElementById('share-location-btn');
            
            // Auto-detect user location on page load
            let userLocation = null;
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        userLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        console.log('User location detected:', userLocation);
                    },
                    function(error) {
                        console.log('Geolocation error:', error);
                    }
                );
            }
            
            // Initial coordinates (will be replaced with actual data)
            const initialCoordinates = {
                pickup: [{{ $locationTracking->pickup_latitude ?? 33.5731 }}, {{ $locationTracking->pickup_longitude ?? -7.5898 }}],
                delivery: [{{ $locationTracking->delivery_latitude ?? 33.5931 }}, {{ $locationTracking->delivery_longitude ?? -7.6098 }}],
                livreur: [{{ $locationTracking->livreur_latitude ?? 33.5731 }}, {{ $locationTracking->livreur_longitude ?? -7.5898 }}]
            };
            
            // Delivery route (will be replaced with actual data)
            const deliveryRoute = {!! $locationTracking && $locationTracking->delivery_route ? json_encode($locationTracking->delivery_route) : '[]' !!};
            
            // Initialize map
            const map = L.map('map').setView(initialCoordinates.livreur, 14);
            
            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);
            
            // Custom icons
            const pickupIcon = L.divIcon({
                className: 'custom-div-icon',
                html: `<div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center shadow-md">
                       <i class="fa-solid fa-store text-white text-xs"></i>
                       </div>`,
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });
            
            const deliveryIcon = L.divIcon({
                className: 'custom-div-icon',
                html: `<div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center shadow-md">
                       <i class="fa-solid fa-location-dot text-white text-xs"></i>
                       </div>`,
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });
            
            const livreurIcon = L.divIcon({
                className: 'custom-div-icon',
                html: `<div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center shadow-md ring-2 ring-orange-300">
                       <i class="fa-solid fa-motorcycle text-white"></i>
                       </div>`,
                iconSize: [40, 40],
                iconAnchor: [20, 20]
            });
            
            // Add markers
            const pickupMarker = L.marker(initialCoordinates.pickup, {icon: pickupIcon})
                .addTo(map)
                .bindPopup(`<b>Adresse de retrait</b><p>${'{{ $command->pickup_address }}'}</p>`);
            
            const deliveryMarker = L.marker(initialCoordinates.delivery, {icon: deliveryIcon})
                .addTo(map)
                .bindPopup(`<b>Adresse de livraison</b><p>${'{{ $command->delivery_address }}'}</p>`);
            
            const livreurMarker = L.marker(initialCoordinates.livreur, {icon: livreurIcon})
                .addTo(map)
                .bindPopup(`<b>Livreur</b><p>${'{{ $command->livreur ? $command->livreur->first_name . " " . $command->livreur->last_name : "En attente" }}'}</p>`);
            
            // Draw delivery route if available
            if (deliveryRoute.length > 0) {
                const routeCoordinates = deliveryRoute.map(point => [point.lat, point.lng]);
                const routeLine = L.polyline(routeCoordinates, {
                    color: '#EA580C',
                    weight: 4,
                    opacity: 0.7,
                    dashArray: '10, 10',
                    lineJoin: 'round'
                }).addTo(map);
                
                // Fit map to show all route points
                map.fitBounds(routeLine.getBounds(), { padding: [50, 50] });
            } else {
                // If no route, fit map to show pickup and delivery points
                const bounds = L.latLngBounds([initialCoordinates.pickup, initialCoordinates.delivery]);
                map.fitBounds(bounds, { padding: [50, 50] });
            }
            
            // Hide loading overlay once map is loaded
            map.whenReady(() => {
                mapLoading.style.display = 'none';
            });
            
            // Real-time updates with Pusher
            const pusher = new Pusher('042792aacd9df9f07e59', {
                cluster: 'eu',
                encrypted: true
            });
            
            const channel = pusher.subscribe('command-{{ $command->id }}');
            channel.bind('livreur-location-updated', function(data) {
                console.log('Received location update:', data);
                
                // Update livreur marker position
                const newLatLng = [data.latitude, data.longitude];
                livreurMarker.setLatLng(newLatLng);
                
                // Only follow the livreur if we're not the livreur (client view)
                if ('{{ auth()->user()->isClient() }}' === '1') {
                    map.panTo(newLatLng);
                }
                
                // Update estimated time if provided
                if (data.estimatedTime) {
                    const estimatedTimeElements = document.querySelectorAll('.estimated-time');
                    estimatedTimeElements.forEach(el => {
                        el.textContent = data.estimatedTime;
                    });
                }
                
                // Add a pulse animation to the marker to indicate movement
                const markerElement = livreurMarker.getElement();
                if (markerElement) {
                    markerElement.classList.add('animate-ping');
                    setTimeout(() => {
                        markerElement.classList.remove('animate-ping');
                    }, 1000);
                }
            });
            
            // Share location functionality for livreur
            if (shareLocationBtn) {
                let isSharing = false;
                let watchId = null;
                
                // If we already have user location, update livreur marker immediately
                if (userLocation && '{{ auth()->user()->isLivreur() && $command->livreur_id === auth()->id() }}' === '1') {
                    livreurMarker.setLatLng([userLocation.lat, userLocation.lng]);
                }
                
                shareLocationBtn.addEventListener('click', function() {
                    if (!isSharing) {
                        // Start sharing location
                        if (navigator.geolocation) {
                            shareLocationBtn.classList.remove('bg-orange-500', 'hover:bg-orange-600');
                            shareLocationBtn.classList.add('bg-green-500', 'hover:bg-green-600');
                            shareLocationBtn.innerHTML = '<i class="fa-solid fa-location-crosshairs mr-2"></i> Position partagée';
                            
                            watchId = navigator.geolocation.watchPosition(
                                function(position) {
                                    const lat = position.coords.latitude;
                                    const lng = position.coords.longitude;
                                    
                                    // Update marker on map
                                    livreurMarker.setLatLng([lat, lng]);
                                    map.panTo([lat, lng]); // Center map on livreur
                                    
                                    // Send location to server
                                    updateLocationOnServer(lat, lng);
                                },
                                function(error) {
                                    console.error('Error getting location:', error);
                                    alert('Impossible d\'obtenir votre position. Veuillez vérifier vos paramètres de localisation.');
                                    stopSharing();
                                },
                                {
                                    enableHighAccuracy: true,
                                    maximumAge: 10000,
                                    timeout: 10000
                                }
                            );
                            
                            isSharing = true;
                        } else {
                            alert('La géolocalisation n\'est pas prise en charge par votre navigateur.');
                        }
                    } else {
                        // Stop sharing location
                        stopSharing();
                    }
                });
                
                function stopSharing() {
                    if (watchId !== null) {
                        navigator.geolocation.clearWatch(watchId);
                        watchId = null;
                    }
                    
                    shareLocationBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
                    shareLocationBtn.classList.add('bg-orange-500', 'hover:bg-orange-600');
                    shareLocationBtn.innerHTML = '<i class="fa-solid fa-location-crosshairs mr-2"></i> Partager ma position';
                    
                    isSharing = false;
                }
                
                function updateLocationOnServer(lat, lng) {
                    // Send location update to server
                    fetch('{{ route("livreur.commands.update-location", $command) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            latitude: lat,
                            longitude: lng
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            console.error('Error updating location:', data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error sending location update:', error);
                    });
                }
            }
        });
    </script>
</body>
</html>
