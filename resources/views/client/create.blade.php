<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoDelivery - Nouvelle Commande</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css' rel='stylesheet' />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .step-active {
            color: #ea580c;
        }
        
        .step-active .step-number {
            background-color: #ea580c;
            color: white;
        }
        
        .step-active .step-label {
            color: #ea580c;
            font-weight: 500;
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .card-selected {
            border-color: #ea580c;
            background-color: #ffedd5;
            box-shadow: 0 4px 6px -1px rgba(234, 88, 12, 0.2);
        }
        
        .progress-bar {
            height: 4px;
            background: linear-gradient(to right, #ea580c 0%, #ea580c 0%, #e5e7eb 0%, #e5e7eb 100%);
            background-size: 100% 100%;
            transition: all 0.6s ease;
        }
        
        .step-1 { background: linear-gradient(to right, #ea580c 33.3%, #e5e7eb 33.3%, #e5e7eb 100%); }
        .step-2 { background: linear-gradient(to right, #ea580c 66.6%, #e5e7eb 66.6%, #e5e7eb 100%); }
        .step-3 { background: linear-gradient(to right, #ea580c 100%, #e5e7eb 100%, #e5e7eb 100%); }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(10px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        
        #map {
            width: 100%;
            height: 250px;
            border-radius: 0.5rem;
        }
        
        .map-marker {
            width: 30px;
            height: 30px;
            background-color: #ea580c;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            cursor: pointer;
        }
        
        .mapboxgl-popup-content {
            padding: 12px;
            border-radius: 8px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-gradient-to-r from-orange-800 to-orange-950 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-2">
                    <a href="{{ route('client.dashboard') }}" class="flex items-center space-x-2">
                        <svg class="w-10 h-10" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Location Pin -->
                            <path d="M32 4c-11 0-20 9-20 20 0 11 20 36 20 36s20-25 20-36c0-11-9-20-20-20z" fill="#EA580C"/>
                            <!-- Inner Circle -->
                            <circle cx="32" cy="24" r="12" fill="#FB923C"/>
                            <!-- Package Icon -->
                            <rect x="24" y="18" width="16" height="12" fill="#FFFFFF"/>
                            <path d="M24 22h16M32 18v12" stroke="#EA580C" stroke-width="1.5"/>
                        </svg>
                        <span class="text-xl font-bold">CoDelivery</span>
                    </a>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('client.dashboard') }}" class="text-white hover:text-orange-300 transition-colors">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Dashboard
                    </a>
                    
                    <!-- User Profile -->
                    <div class="relative">
                        <button class="flex items-center space-x-2 focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center">
                                <span class="font-semibold text-sm">{{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}</span>
                            </div>
                        </button>
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

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8 pb-20">
        <!-- Order Process Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold mb-2">Créer une commande</h1>
            <p class="text-gray-600">Commandez ce dont vous avez besoin et faites-vous livrer rapidement</p>
        </div>
        
        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="progress-bar step-1 mb-6"></div>
            <div class="flex justify-between">
                <div class="step-active">
                    <div class="flex flex-col items-center">
                        <div class="step-number w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center font-semibold mb-1">1</div>
                        <span class="step-label text-sm">Service</span>
                    </div>
                </div>
                <div>
                    <div class="flex flex-col items-center">
                        <div class="step-number w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center font-semibold text-gray-600 mb-1">2</div>
                        <span class="step-label text-sm text-gray-600">Adresse</span>
                    </div>
                </div>
                <div>
                    <div class="flex flex-col items-center">
                        <div class="step-number w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center font-semibold text-gray-600 mb-1">3</div>
                        <span class="step-label text-sm text-gray-600">Finalisation</span>
                    </div>
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('client.commands.store') }}" id="commandForm">
            @csrf
            
            <!-- Step 1: Service Selection -->
            <div class="animate-fade-in" id="step1">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Main Selection Area -->
                    <div class="md:col-span-2">
                        <h2 class="text-xl font-semibold mb-6">Que souhaitez-vous commander?</h2>
                        
                        <!-- Service Types -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                            <!-- Restaurant Option -->
                            <div class="card-hover card-selected rounded-xl p-4 border-2 border-gray-200 cursor-pointer flex flex-col items-center" onclick="selectServiceType('restaurant', this)">
                                <input type="radio" name="service_type_radio" value="restaurant" class="hidden" checked>
                                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-2">
                                    <i class="fa-solid fa-utensils text-xl text-orange-600"></i>
                                </div>
                                <span class="text-sm font-medium">Restaurant</span>
                            </div>
                            
                            <!-- Pharmacy Option -->
                            <div class="card-hover rounded-xl p-4 border-2 border-gray-200 cursor-pointer flex flex-col items-center" onclick="selectServiceType('pharmacy', this)">
                                <input type="radio" name="service_type_radio" value="pharmacy" class="hidden">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-2">
                                    <i class="fa-solid fa-prescription-bottle-medical text-xl text-green-600"></i>
                                </div>
                                <span class="text-sm font-medium">Pharmacie</span>
                            </div>
                            
                            <!-- Market Option -->
                            <div class="card-hover rounded-xl p-4 border-2 border-gray-200 cursor-pointer flex flex-col items-center" onclick="selectServiceType('market', this)">
                                <input type="radio" name="service_type_radio" value="market" class="hidden">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-2">
                                    <i class="fa-solid fa-cart-shopping text-xl text-blue-600"></i>
                                </div>
                                <span class="text-sm font-medium">Courses</span>
                            </div>
                            
                            <!-- Delivery Option -->
                            <div class="card-hover rounded-xl p-4 border-2 border-gray-200 cursor-pointer flex flex-col items-center" onclick="selectServiceType('package', this)">
                                <input type="radio" name="service_type_radio" value="package" class="hidden">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-2">
                                    <i class="fa-solid fa-box text-xl text-purple-600"></i>
                                </div>
                                <span class="text-sm font-medium">Colis</span>
                            </div>
                        </div>
                        
                        <!-- Pickup Location Form -->
                        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 mt-6">
                            <h3 class="font-medium text-lg mb-4">Informations du lieu de ramassage</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="establishment_name" class="block text-sm font-medium text-gray-700 mb-1">Nom de l'établissement</label>
                                    <input type="text" id="establishment_name" name="establishment_name" 
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500"
                                           placeholder="Ex: McDonald's, Pharmacie Centrale, etc.">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Sélectionnez un lieu de ramassage sur la carte</label>
                                    <div id="map" class="mb-3"></div>
                                    <div class="flex items-center text-sm text-gray-600 mb-2">
                                        <i class="fas fa-info-circle mr-2 text-orange-500"></i>
                                        <span>Cliquez sur la carte pour sélectionner le lieu de ramassage</span>
                                    </div>
                                    
                                    <!-- Hidden input that will store the coordinates -->
                                    <input type="hidden" id="pickup_coordinates" name="pickup_coordinates" value="">
                                    
                                    <!-- Hidden input for the original pickup_address field (for compatibility) -->
                                    <input type="hidden" id="pickup_address" name="pickup_address" value="">
                                    
                                    <!-- Display the selected address -->
                                    <div class="mt-2 p-3 bg-gray-50 rounded-lg">
                                        <p class="text-sm font-medium mb-1">Adresse sélectionnée:</p>
                                        <p id="selected_address_display" class="text-sm text-gray-700">Aucune adresse sélectionnée</p>
                                    </div>
                                    
                                    <!-- Pickup coordinates warning -->
                                    <div id="pickup_coords_warning" class="mt-2 p-3 bg-red-100 text-red-700 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            <span>Important: Veuillez sélectionner un lieu de ramassage précis sur la carte!</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Step 1 Navigation -->
                <div class="mt-6 flex justify-end">
                    <button type="button" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg" onclick="goToStep(2)">
                        Continuer
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Step 2: Order Details and Delivery Address -->
        <div class="hidden animate-fade-in" id="step2">
            <div class="bg-white rounded-xl shadow-sm p-8 mb-8">
                <h2 class="text-xl font-semibold mb-6">Détails de la commande</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Nom de la commande</label>
                        <input type="text" id="title" name="title" 
                               class="w-full px-3 py-2 rounded-lg border @error('title') border-red-500 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-orange-500"
                               placeholder="Ex: Livraison de repas" value="{{ old('title') }}" required>
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description de la commande</label>
                        <textarea id="description" name="description" rows="3"
                                  class="w-full px-3 py-2 rounded-lg border @error('description') border-red-500 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-orange-500"
                                  placeholder="Détails de ce que vous souhaitez commander">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Hidden input to store establishment_name from step 1 -->
                    <input type="hidden" id="establishment_name_hidden" name="establishment_name">
                    
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priorité</label>
                        <select id="priority" name="priority"
                                class="w-full px-3 py-2 rounded-lg border @error('priority') border-red-500 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Basse</option>
                            <option value="medium" {{ old('priority') == 'medium' || !old('priority') ? 'selected' : '' }}>Moyenne</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Haute</option>
                        </select>
                        @error('priority')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Hidden input to store pickup_address from step 1 -->
                    <input type="hidden" id="pickup_address_hidden" name="pickup_address">
                    
                    <div>
                        <label for="delivery_address" class="block text-sm font-medium text-gray-700 mb-1">Adresse de livraison</label>
                        <input type="text" id="delivery_address" name="delivery_address" disabled 
                               class="w-full px-3 py-2 rounded-lg border @error('delivery_address') border-red-500 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-orange-500"
                               placeholder="Où livrer votre commande" value="{{ old('delivery_address') }}" required>
                        @error('delivery_address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        
                        <!-- Add delivery location map -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sélectionnez l'adresse de livraison sur la carte</label>
                            <div id="delivery_map" class="w-full h-[250px] rounded-lg mb-3"></div>
                            <div id="delivery_coords_warning" class="hidden p-3 bg-red-100 text-red-700 rounded-lg mb-2">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <span>Important: Veuillez sélectionner un lieu de livraison précis sur la carte!</span>
                                </div>
                            </div>
                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                <i class="fas fa-info-circle mr-2 text-orange-500"></i>
                                <span>Cliquez sur la carte pour sélectionner le lieu de livraison</span>
                            </div>
                            
                            <!-- Hidden input to store the delivery coordinates -->
                            <input type="hidden" id="delivery_coordinates" name="delivery_coordinates" value="">
                            
                            <!-- Display the selected delivery address -->
                            <div class="mt-2 p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm font-medium mb-1">Adresse de livraison sélectionnée:</p>
                                <p id="selected_delivery_address_display" class="text-sm text-gray-700">Aucune adresse sélectionnée</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Prix de la commande</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">DH</span>
                            </div>
                            <input type="number" id="price" min="0" step="0.01"
                                   class="w-full pl-10 pr-3 py-2 rounded-lg border @error('price') border-red-500 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-orange-500"
                                   placeholder="0.00" value="{{ old('price') }}" required onchange="updateTotalPrice()">
                        </div>
                        @error('price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">+ 10 DH frais de livraison</p>
                        
                        <!-- Display the total price -->
                        <div class="mt-3 p-2 bg-orange-50 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium">Total avec livraison:</span>
                                <span class="text-sm font-bold text-orange-600" id="displayTotalPrice">10.00 DH</span>
                            </div>
                        </div>
                        
                        <!-- Hidden field for the total price that will be saved to the database -->
                        <input type="hidden" id="total_price" name="price" value="10">
                    </div>
                </div>
            </div>
            
            <!-- Step 2 Navigation -->
            <div class="mt-6 flex justify-between">
                <button type="button" class="border border-gray-300 bg-white text-gray-700 px-6 py-2 rounded-lg" onclick="goToStep(1)">Retour</button>
                <button type="button" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg" onclick="goToStep(3)">Continuer</button>
            </div>
        </div>
        
        <!-- Step 3: Confirmation -->
        <div class="hidden animate-fade-in" id="step3">
            <div class="bg-white rounded-xl shadow-sm p-8 mb-8">
                <h2 class="text-xl font-semibold mb-6">Confirmation de la commande</h2>
                
                <div class="space-y-6">
                    <div>
                        <h3 class="font-medium text-gray-900">Détails du service</h3>
                        <div class="mt-2 flex items-center">
                            <div id="serviceTypeIcon" class="w-10 h-10 rounded-full flex items-center justify-center mr-3">
                                <i class="fa-solid fa-utensils text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium" id="serviceTypeName">Restaurant</p>
                                <p class="text-sm text-gray-500" id="serviceTypeDescription">Livraison de repas</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <h3 class="font-medium text-gray-900">Adresses</h3>
                        <div class="mt-2 space-y-3">
                            <div class="flex">
                                <div class="flex-shrink-0 w-6">
                                    <i class="fa-solid fa-location-dot text-orange-500"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Lieu de ramassage</p>
                                    <p class="text-sm text-gray-700" id="confirmEstablishmentName">...</p>
                                    <p class="text-sm text-gray-500" id="confirmPickupAddress">...</p>
                                </div>
                            </div>
                            <div class="flex">
                                <div class="flex-shrink-0 w-6">
                                    <i class="fa-solid fa-flag-checkered text-orange-500"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Adresse de livraison</p>
                                    <p class="text-sm text-gray-500" id="confirmDeliveryAddress">...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <h3 class="font-medium text-gray-900">Détails de la commande</h3>
                        <div class="mt-2 space-y-2">
                            <div>
                                <p class="text-sm font-medium">Nom de la commande</p>
                                <p class="text-sm text-gray-700" id="confirmTitle">...</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium">Description</p>
                                <p class="text-sm text-gray-500" id="confirmDescription">...</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium">Priorité</p>
                                <p class="text-sm text-gray-500" id="confirmPriority">...</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <h3 class="font-medium text-gray-900">Résumé des coûts</h3>
                        <div class="mt-2">
                            <div class="flex justify-between">
                                <p class="text-sm text-gray-500">Sous-total</p>
                                <p class="text-sm font-medium" id="confirmSubtotal">0.00 DH</p>
                            </div>
                            <div class="flex justify-between mt-1">
                                <p class="text-sm text-gray-500">Frais de livraison</p>
                                <p class="text-sm font-medium">10.00 DH</p>
                            </div>
                            <div class="flex justify-between mt-3 pt-3 border-t border-gray-100">
                                <p class="font-medium">Total</p>
                                <p class="font-medium text-orange-600" id="confirmTotal">10.00 DH</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Step 3 Navigation -->
            <div class="mt-6 flex justify-between">
                <button type="button" class="border border-gray-300 bg-white text-gray-700 px-6 py-2 rounded-lg" onclick="goToStep(2)">
                    <i class="fa-solid fa-arrow-left mr-2"></i>Retour
                </button>
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg">
                    Finaliser la commande<i class="fa-solid fa-check ml-2"></i>
                </button>
            </div>
        </div>
        </form>
        

    </main>
    
    <!-- Bottom Navigation (Fixed on small screens) -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t p-4 flex justify-between">
        <button type="button" id="mobileBackBtn" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700">
            <i class="fa-solid fa-arrow-left mr-1"></i> Retour
        </button>
        <button type="button" id="mobileNextBtn" class="px-4 py-2 bg-orange-500 text-white rounded-lg">
            Continuer <i class="fa-solid fa-arrow-right ml-1"></i>
        </button>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('commandForm');
            const serviceTypeInput = document.querySelector('input[name="service_type"]') || document.createElement('input');
            
            // Initialize hidden service type input if it doesn't exist
            if (!document.querySelector('input[name="service_type"]')) {
                serviceTypeInput.type = 'hidden';
                serviceTypeInput.name = 'service_type';
                serviceTypeInput.value = 'restaurant'; // Default value
                form.appendChild(serviceTypeInput);
            }
            
            // Initialize summary sections
            updateSummary();
            
            // Add event listeners to form fields for real-time summary updates
            document.getElementById('establishment_name').addEventListener('input', updateSummary);
            document.getElementById('pickup_address').addEventListener('input', updateSummary);
            document.getElementById('title')?.addEventListener('input', updateSummary);
            document.getElementById('description')?.addEventListener('input', updateSummary);
            document.getElementById('delivery_address')?.addEventListener('input', updateSummary);
            document.getElementById('price')?.addEventListener('input', updateSummary);
            document.getElementById('priority')?.addEventListener('change', updateSummary);
            
            // Add event listeners to radio buttons
            document.querySelectorAll('input[name="service_type_radio"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const type = this.value;
                    selectServiceType(type, this.parentElement);
                });
            });
            
            // Initialize continue buttons
            document.querySelectorAll('.btn-continue').forEach(btn => {
                btn.addEventListener('click', function() {
                    const nextStep = parseInt(this.dataset.step);
                    goToStep(nextStep);
                });
            });
            
            // Initialize mobile buttons
            updateMobileButtons(1);
            
            // Add form submit handler
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validate all required fields
                const requiredFields = [
                    'establishment_name', 'pickup_address', 'pickup_coordinates', 
                    'title', 'delivery_address', 'delivery_coordinates', 'price'
                ];
                
                let valid = true;
                requiredFields.forEach(field => {
                    const input = document.getElementById(field);
                    if (input && !input.value.trim()) {
                        valid = false;
                        input.classList.add('border-red-500');
                    } else if (input) {
                        input.classList.remove('border-red-500');
                    }
                });
                
                if (!valid) {
                    alert('Veuillez remplir tous les champs obligatoires et sélectionner les emplacements sur les cartes.');
                    return false;
                }
                
                // Enhanced validation for coordinates
                const pickupCoords = document.getElementById('pickup_coordinates').value;
                const deliveryCoords = document.getElementById('delivery_coordinates').value;
                
                if (!pickupCoords || !deliveryCoords) {
                    alert('Veuillez sélectionner les emplacements précis sur les cartes de ramassage et de livraison.');
                    return false;
                }
                
                // Validate coordinate format
                if (!pickupCoords.match(/^-?\d+\.\d+,-?\d+\.\d+$/) || !deliveryCoords.match(/^-?\d+\.\d+,-?\d+\.\d+$/)) {
                    alert('Format des coordonnées invalide. Veuillez sélectionner à nouveau les emplacements sur les cartes.');
                    return false;
                }
                
                // Process the coordinates for database storage
                if (pickupCoords) {
                    const [pickupLng, pickupLat] = pickupCoords.split(',');
                    
                    // Add hidden fields for individual coordinates
                    const pickupLatField = document.createElement('input');
                    pickupLatField.type = 'hidden';
                    pickupLatField.name = 'pickup_latitude';
                    pickupLatField.value = pickupLat;
                    this.appendChild(pickupLatField);
                    
                    const pickupLngField = document.createElement('input');
                    pickupLngField.type = 'hidden';
                    pickupLngField.name = 'pickup_longitude';
                    pickupLngField.value = pickupLng;
                    this.appendChild(pickupLngField);
                    
                    console.log('Submitting pickup coordinates:', pickupLng, pickupLat);
                }
                
                if (deliveryCoords) {
                    const [deliveryLng, deliveryLat] = deliveryCoords.split(',');
                    
                    // Log exact coordinates before submission
                    console.log('Submitting delivery coordinates:', deliveryLng, deliveryLat);
                    
                    // Add hidden fields for individual coordinates
                    const deliveryLatField = document.createElement('input');
                    deliveryLatField.type = 'hidden';
                    deliveryLatField.name = 'delivery_latitude';
                    deliveryLatField.value = deliveryLat;
                    this.appendChild(deliveryLatField);
                    
                    const deliveryLngField = document.createElement('input');
                    deliveryLngField.type = 'hidden';
                    deliveryLngField.name = 'delivery_longitude';
                    deliveryLngField.value = deliveryLng;
                    this.appendChild(deliveryLngField);
                }
                
                console.log('Form validation successful, submitting with coordinates');
                // Submit the form
                this.submit();
            });
        });
        
        // Function to select service type
        function selectServiceType(type, element) {
            // Update hidden input
            document.querySelector('input[name="service_type"]').value = type;
            
            // Update radio buttons
            document.querySelectorAll('input[name="service_type_radio"]').forEach(radio => {
                if (radio.value === type) {
                    radio.checked = true;
                } else {
                    radio.checked = false;
                }
            });
            
            // Update UI
            document.querySelectorAll('.card-selected').forEach(card => {
                card.classList.remove('card-selected');
            });
            element.classList.add('card-selected');
            
            // Update service type icons and text
            updateServiceTypeUI(type);
            
            // Update summary
            updateSummary();
        }
        
        function updateServiceTypeUI(type) {
            const iconMap = {
                'restaurant': '<i class="fa-solid fa-utensils text-lg text-orange-600"></i>',
                'pharmacy': '<i class="fa-solid fa-prescription-bottle-medical text-lg text-green-600"></i>',
                'market': '<i class="fa-solid fa-cart-shopping text-lg text-blue-600"></i>',
                'package': '<i class="fa-solid fa-box text-lg text-purple-600"></i>'
            };
            
            const nameMap = {
                'restaurant': 'Restaurant',
                'pharmacy': 'Pharmacie',
                'market': 'Courses',
                'package': 'Colis'
            };
            
            const descMap = {
                'restaurant': 'Livraison de repas',
                'pharmacy': 'Livraison de médicaments',
                'market': 'Livraison de courses',
                'package': 'Livraison de colis'
            };
            
            const bgMap = {
                'restaurant': 'bg-orange-100',
                'pharmacy': 'bg-green-100',
                'market': 'bg-blue-100',
                'package': 'bg-purple-100'
            };
            
            // Update confirmation page
            const iconElement = document.getElementById('serviceTypeIcon');
            if (iconElement) {
                iconElement.className = `w-10 h-10 rounded-full flex items-center justify-center mr-3 ${bgMap[type] || 'bg-orange-100'}`;
                iconElement.innerHTML = iconMap[type] || iconMap['restaurant'];
            }
            
            // Update summary section
            const summaryIconElement = document.getElementById('summaryServiceIcon');
            if (summaryIconElement) {
                summaryIconElement.className = `w-10 h-10 rounded-full flex items-center justify-center mr-3 ${bgMap[type] || 'bg-orange-100'}`;
                summaryIconElement.innerHTML = iconMap[type] || iconMap['restaurant'];
            }
            
            // Update text elements
            if (document.getElementById('serviceTypeName')) {
                document.getElementById('serviceTypeName').textContent = nameMap[type] || 'Restaurant';
            }
            if (document.getElementById('serviceTypeDescription')) {
                document.getElementById('serviceTypeDescription').textContent = descMap[type] || 'Livraison de repas';
            }
            if (document.getElementById('summaryServiceType')) {
                document.getElementById('summaryServiceType').textContent = nameMap[type] || 'Restaurant';
            }
            if (document.getElementById('summaryServiceDesc')) {
                document.getElementById('summaryServiceDesc').textContent = descMap[type] || 'Livraison de repas';
            }
        }
        
        // Function to update the total price (base price + delivery fee)
        function updateTotalPrice() {
            const basePrice = parseFloat(document.getElementById('price').value) || 0;
            const deliveryFee = 10;
            const totalPrice = basePrice + deliveryFee;
            
            // Update the display
            document.getElementById('displayTotalPrice').textContent = `${totalPrice.toFixed(2)} DH`;
            
            // Update the hidden field that will be submitted
            document.getElementById('total_price').value = totalPrice.toFixed(2);
            
            // Also update the confirmation page
            document.getElementById('confirmSubtotal').textContent = `${basePrice.toFixed(2)} DH`;
            document.getElementById('confirmTotal').textContent = `${totalPrice.toFixed(2)} DH`;
        }
        
        // Function to update the summary section
        function updateSummary() {
            // Update service type
            const serviceType = document.querySelector('input[name="service_type"]')?.value || 'restaurant';
            updateServiceTypeUI(serviceType);
            
            // Update establishment name
            const establishmentName = document.getElementById('establishment_name').value;
            if (establishmentName) {
                document.getElementById('confirmEstablishmentName').textContent = establishmentName;
            }
            
            // Update pickup address
            const pickupAddress = document.getElementById('pickup_address').value;
            if (pickupAddress) {
                document.getElementById('confirmPickupAddress').textContent = pickupAddress;
            }
            
            // Update delivery address (step 2)
            const deliveryAddress = document.getElementById('delivery_address')?.value;
            if (deliveryAddress) {
                document.getElementById('confirmDeliveryAddress').textContent = deliveryAddress;
            }
            
            // Update order details (step 2)
            const title = document.getElementById('title')?.value;
            const description = document.getElementById('description')?.value;
            if (title) {
                document.getElementById('confirmTitle').textContent = title;
            }
            if (description) {
                document.getElementById('confirmDescription').textContent = description;
            }
            
            // Update priority
            const prioritySelect = document.getElementById('priority');
            if (prioritySelect) {
                const priorityText = prioritySelect.options[prioritySelect.selectedIndex].text;
                document.getElementById('confirmPriority').textContent = priorityText;
            }
            
            // Update price
            if (document.getElementById('price')) {
                updateTotalPrice();
            }
            
            const priorityMap = {
                'low': 'Basse',
                'medium': 'Moyenne',
                'high': 'Haute'
            };
            const priority = document.getElementById('priority')?.value || 'medium';
            if (document.getElementById('confirmPriority')) {
                document.getElementById('confirmPriority').textContent = priorityMap[priority] || 'Moyenne';
            }
            
            const price = parseFloat(document.getElementById('price')?.value) || 0;
            if (document.getElementById('confirmSubtotal')) {
                document.getElementById('confirmSubtotal').textContent = `${price.toFixed(2)} DH`;
            }
            if (document.getElementById('confirmTotal')) {
                document.getElementById('confirmTotal').textContent = `${(price + 10).toFixed(2)} DH`;
            }
        }
        
        // Step navigation
        let currentStep = 1;
        
        function goToStep(step) {
            // Validate current step before proceeding
            if (currentStep === 1 && step > currentStep) {
                const establishmentName = document.getElementById('establishment_name').value;
                const pickupCoordinates = document.getElementById('pickup_coordinates').value;
                const pickupAddress = document.getElementById('pickup_address').value;
                
                // Check if the user has selected a location on the map
                if (!establishmentName) {
                    alert('Veuillez remplir le nom de l\'établissement avant de continuer.');
                    return;
                }
                
                if (!pickupCoordinates || !pickupAddress) {
                    alert('Veuillez sélectionner un lieu de ramassage sur la carte avant de continuer.');
                    // Show warning
                    const warning = document.getElementById('pickup_coords_warning');
                    if (warning) warning.classList.remove('hidden');
                    // Scroll to the map
                    document.getElementById('map').scrollIntoView({ behavior: 'smooth' });
                    return;
                }
                
                // Use the selected address for display but store both address and coordinates
                document.getElementById('establishment_name_hidden').value = establishmentName;
                document.getElementById('pickup_address_hidden').value = pickupAddress;
                
                console.log('Moving to step 2 with pickup address:', pickupAddress);
                console.log('Pickup coordinates:', pickupCoordinates);
                
                // Initialize the total price calculation when entering step 2
                setTimeout(() => {
                    if (document.getElementById('price')) {
                        document.getElementById('price').dispatchEvent(new Event('change'));
                    }
                    
                    // Check for delivery coordinates warning visibility 
                    const warning = document.getElementById('delivery_coords_warning');
                    if (warning) {
                        // Show warning if no coordinates are set yet
                        if (!document.getElementById('delivery_coordinates').value) {
                            warning.classList.remove('hidden');
                        } else {
                            warning.classList.add('hidden');
                        }
                    }
                }, 100);
            }
            
            if (currentStep === 2 && step > currentStep) {
                const title = document.getElementById('title').value;
                const deliveryAddress = document.getElementById('delivery_address').value;
                const deliveryCoordinates = document.getElementById('delivery_coordinates').value;
                const price = document.getElementById('price').value;
                
                if (!title || !deliveryAddress || !price) {
                    alert('Veuillez remplir le nom de la commande, l\'adresse de livraison et le prix avant de continuer.');
                    return;
                }
                
                if (!deliveryCoordinates) {
                    alert('Veuillez sélectionner le lieu de livraison exact sur la carte avant de continuer.');
                    // Show warning
                    const warning = document.getElementById('delivery_coords_warning');
                    if (warning) warning.classList.remove('hidden');
                    // Scroll to the map
                    document.getElementById('delivery_map').scrollIntoView({ behavior: 'smooth' });
                    return;
                }
                
                console.log('Moving to step 3 with delivery coordinates:', deliveryCoordinates);
            }
            
            // We no longer need to handle showing/hiding the side summary
            
            // Update summary before showing the next step
            updateSummary();
            
            // Hide all steps
            document.querySelectorAll('[id^="step"]').forEach(step => {
                step.classList.add('hidden');
            });
            
            // Show the target step
            document.getElementById(`step${step}`).classList.remove('hidden');
            
            // Update progress bar
            document.querySelector('.progress-bar').className = `progress-bar step-${step} mb-6`;
            
            // Update step indicators
            document.querySelectorAll('.step-active').forEach(s => s.classList.remove('step-active'));
            document.querySelectorAll('.flex.justify-between > div')[step - 1].classList.add('step-active');
            
            // Update current step
            currentStep = step;
            
            // Update mobile buttons
            updateMobileButtons(step);
        }
        
        // Mobile navigation buttons
        function updateMobileButtons(step) {
            const backBtn = document.getElementById('mobileBackBtn');
            const nextBtn = document.getElementById('mobileNextBtn');
            
            if (!backBtn || !nextBtn) return;
            
            if (step === 1) {
                backBtn.classList.add('invisible');
            } else {
                backBtn.classList.remove('invisible');
                backBtn.onclick = () => goToStep(step - 1);
            }
            
            if (step === 3) {
                nextBtn.textContent = 'Finaliser';
                nextBtn.onclick = () => document.getElementById('commandForm').submit();
            } else {
                nextBtn.textContent = 'Suivant';
                nextBtn.onclick = () => goToStep(step + 1);
            }
        }
        
        // This function has been removed as we no longer need the side summary
        
        // Add event listeners to the buttons
        document.querySelectorAll('[onclick^="goToStep"]').forEach(button => {
            const stepMatch = button.getAttribute('onclick').match(/goToStep\((\d+)\)/);
            if (stepMatch && stepMatch[1]) {
                const targetStep = parseInt(stepMatch[1]);
                button.addEventListener('click', function() {
                    goToStep(targetStep);
                });
            }
        });
        
        // Initialize mobile buttons
        updateMobileButtons(1);
    </script>
    
    <!-- Footer -->
    <footer class="bg-gradient-to-r from-orange-800 to-orange-950 text-white py-8 mt-4">
        <!-- Same footer as other pages -->
    </footer>
    
    <!-- Mapbox Script -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js'></script>
    <script>
        // Initialize maps when the DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Mapbox token
            mapboxgl.accessToken = 'pk.eyJ1IjoiYmFkcmVkZGluZTAwIiwiYSI6ImNsdzJ0cDJ1bTBtMnQyaW11NjBxczE3Z2kifQ.ockRcbgDpqVyMLsAv_tMgw';
            
            // Nador city center coordinates
            const nadorCenter = [-2.9287, 35.1698];
            
            // Initialize pickup map centered on Nador
            const pickupMap = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/streets-v12',
                center: nadorCenter,
                zoom: 13
            });
            
            // Create pickup marker element
            const pickupMarkerElement = document.createElement('div');
            pickupMarkerElement.className = 'map-marker';
            
            // Create pickup marker instance (initially not added to map)
            const pickupMarker = new mapboxgl.Marker({
                element: pickupMarkerElement,
                draggable: true
            });
            
            // Initialize delivery map (when step 2 is loaded)
            let deliveryMap = null;
            let deliveryMarker = null;
            
            // Function to initialize the delivery map
            function initDeliveryMap() {
                if (deliveryMap) return; // Already initialized
                
                // Check if the delivery map container exists
                const deliveryMapContainer = document.getElementById('delivery_map');
                if (!deliveryMapContainer) return;
                
                // Create the delivery map
                deliveryMap = new mapboxgl.Map({
                    container: 'delivery_map',
                    style: 'mapbox://styles/mapbox/streets-v12',
                    center: nadorCenter,
                    zoom: 13
                });
                
                // Create delivery marker element
                const deliveryMarkerElement = document.createElement('div');
                deliveryMarkerElement.className = 'map-marker';
                deliveryMarkerElement.style.backgroundColor = '#ef4444'; // Red for delivery
                
                // Create delivery marker instance
                deliveryMarker = new mapboxgl.Marker({
                    element: deliveryMarkerElement,
                    draggable: true
                });
                
                // Add click event to the delivery map to place marker
                deliveryMap.on('click', function(e) {
                    // Set marker position
                    deliveryMarker.setLngLat(e.lngLat).addTo(deliveryMap);
                    
                    // Update delivery address display
                    updateDeliveryAddressDisplay(e.lngLat);
                });
                
                // Update address when delivery marker is dragged
                deliveryMarker.on('dragend', function() {
                    const lngLat = deliveryMarker.getLngLat();
                    updateDeliveryAddressDisplay(lngLat);
                });
                
                // Add controls to the delivery map
                deliveryMap.addControl(new mapboxgl.NavigationControl(), 'top-right');
                
                // Add locate user control to delivery map
                deliveryMap.addControl(
                    new mapboxgl.GeolocateControl({
                        positionOptions: {
                            enableHighAccuracy: true
                        },
                        trackUserLocation: true,
                        showUserHeading: true
                    }),
                    'top-right'
                );
                
                // Restrict map view to Nador region
                const nadorBounds = [
                    [-3.0500, 35.1300], // Southwest coordinates
                    [-2.8800, 35.1900]  // Northeast coordinates
                ];
                deliveryMap.setMaxBounds(nadorBounds);
            }
            
            // Simple function to set a default pickup address while waiting for geocoding
            function setDefaultAddress(lngLat) {
                const defaultAddress = `Point sélectionné à Nador (${lngLat.lng.toFixed(6)}, ${lngLat.lat.toFixed(6)})`;
                document.getElementById('pickup_address').value = defaultAddress;
                document.getElementById('pickup_coordinates').value = `${lngLat.lng},${lngLat.lat}`;
                document.getElementById('selected_address_display').textContent = defaultAddress;
                
                // Add visual feedback that coordinates are selected
                const displayElement = document.getElementById('selected_address_display');
                if (displayElement) {
                    displayElement.classList.add('font-semibold', 'text-green-600');
                }
                
                // Hide the warning since coordinates are now set
                const warning = document.getElementById('pickup_coords_warning');
                if (warning) warning.classList.add('hidden');
                
                // Also update summary if it exists
                if (document.getElementById('confirmPickupAddress')) {
                    document.getElementById('confirmPickupAddress').textContent = defaultAddress;
                }
                
                console.log("Pickup coordinates saved:", lngLat.lng, lngLat.lat);
            }
            
            // Function to update the address display using reverse geocoding
            function updateAddressDisplay(lngLat) {
                // First update with default value immediately so user sees something
                setDefaultAddress(lngLat);
                
                // Then try to get a better address with geocoding
                const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${lngLat.lng},${lngLat.lat}.json?access_token=${mapboxgl.accessToken}&language=fr&limit=1`;
                
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.features && data.features.length > 0) {
                            // Use place_name as it's the most complete address
                            const address = data.features[0].place_name;
                            
                            // Format for display
                            const displayAddress = address.split(', ').slice(0, 2).join(', ') + ', Nador';
                            
                            // Update display and form values
                            document.getElementById('pickup_address').value = displayAddress;
                            document.getElementById('selected_address_display').textContent = displayAddress;
                            
                            // Keep coordinates value - very important!
                            document.getElementById('pickup_coordinates').value = `${lngLat.lng},${lngLat.lat}`;
                            
                            console.log("Address updated to:", displayAddress);
                            console.log("Pickup coordinates confirmed:", lngLat.lng, lngLat.lat);
                            
                            // Also update summary if it exists
                            if (document.getElementById('confirmPickupAddress')) {
                                document.getElementById('confirmPickupAddress').textContent = displayAddress;
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error during reverse geocoding:', error);
                        // Default address was already set, so we don't need to do anything here
                    });
            }
            
            // Simple function to set a default delivery address while waiting for geocoding
            function setDefaultDeliveryAddress(lngLat) {
                const defaultAddress = `Point sélectionné à Nador (${lngLat.lng.toFixed(6)}, ${lngLat.lat.toFixed(6)})`;
                document.getElementById('delivery_address').value = defaultAddress;
                document.getElementById('delivery_coordinates').value = `${lngLat.lng},${lngLat.lat}`;
                document.getElementById('selected_delivery_address_display').textContent = defaultAddress;
                
                // Add visual feedback that coordinates are selected
                const displayElement = document.getElementById('selected_delivery_address_display');
                if (displayElement) {
                    displayElement.classList.add('font-semibold', 'text-green-600');
                }
                
                // Also update summary
                if (document.getElementById('confirmDeliveryAddress')) {
                    document.getElementById('confirmDeliveryAddress').textContent = defaultAddress;
                }
                
                console.log("Delivery coordinates saved:", lngLat.lng, lngLat.lat);
            }
            
            // Function to update the delivery address display using reverse geocoding
            function updateDeliveryAddressDisplay(lngLat) {
                // First update with default value immediately so user sees something
                setDefaultDeliveryAddress(lngLat);
                
                // Store the exact marker coordinates as the most important data
                document.getElementById('delivery_coordinates').value = `${lngLat.lng},${lngLat.lat}`;
                console.log("Exact delivery coordinates saved:", lngLat.lng, lngLat.lat);
                
                // Hide the warning since coordinates are now set
                const warning = document.getElementById('delivery_coords_warning');
                if (warning) warning.classList.add('hidden');
                
                // Then try to get a human-readable address with geocoding, but only for display
                const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${lngLat.lng},${lngLat.lat}.json?access_token=${mapboxgl.accessToken}&language=fr&limit=1`;
                
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.features && data.features.length > 0) {
                            // Use place_name as it's the most complete address
                            const address = data.features[0].place_name;
                            
                            // Format for display
                            const displayAddress = address.split(', ').slice(0, 2).join(', ') + ', Nador';
                            
                            // Update display and form values, but keep the exact coordinates as the main data
                            document.getElementById('delivery_address').value = displayAddress;
                            document.getElementById('selected_delivery_address_display').textContent = displayAddress;
                            
                            console.log("Display address updated to:", displayAddress);
                            console.log("But keeping exact coordinates:", lngLat.lng, lngLat.lat);
                            
                            // Also update summary if it exists
                            if (document.getElementById('confirmDeliveryAddress')) {
                                document.getElementById('confirmDeliveryAddress').textContent = displayAddress;
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error during reverse geocoding for delivery:', error);
                        // Even if geocoding fails, we already have the coordinates saved
                        console.log("Geocoding failed, but coordinates are saved:", lngLat.lng, lngLat.lat);
                    });
            }
            
            // Add click event to the pickup map to place marker
            pickupMap.on('click', function(e) {
                // Set marker position
                pickupMarker.setLngLat(e.lngLat).addTo(pickupMap);
                
                // Update address display
                updateAddressDisplay(e.lngLat);
            });
            
            // Update address when pickup marker is dragged
            pickupMarker.on('dragend', function() {
                const lngLat = pickupMarker.getLngLat();
                updateAddressDisplay(lngLat);
            });
            
            // Add controls to the pickup map
            pickupMap.addControl(new mapboxgl.NavigationControl(), 'top-right');
            
            // Restrict pickup map view to Nador region
            const nadorBounds = [
                [-3.0500, 35.1300], // Southwest coordinates
                [-2.8800, 35.1900]  // Northeast coordinates
            ];
            pickupMap.setMaxBounds(nadorBounds);
            
            // Add locate user control to pickup map
            pickupMap.addControl(
                new mapboxgl.GeolocateControl({
                    positionOptions: {
                        enableHighAccuracy: true
                    },
                    trackUserLocation: true,
                    showUserHeading: true
                }),
                'top-right'
            );
            
            // Check if we need to show/hide the pickup coordinates warning
            setTimeout(() => {
                const pickupCoordinates = document.getElementById('pickup_coordinates').value;
                const warning = document.getElementById('pickup_coords_warning');
                
                if (warning) {
                    if (pickupCoordinates) {
                        warning.classList.add('hidden');
                        console.log('Pickup coordinates already set, hiding warning');
                    } else {
                        warning.classList.remove('hidden');
                        console.log('No pickup coordinates set, showing warning');
                    }
                }
            }, 500);
            
            // Initialize delivery map when going to step 2
            const originalGoToStep = window.goToStep;
            window.goToStep = function(step) {
                originalGoToStep(step);
                if (step === 2) {
                    // After DOM is updated, initialize the delivery map
                    setTimeout(function() {
                        initDeliveryMap();
                    }, 100);
                }
            };
        });
    </script>
    
    <script>
        // Simple transitions between steps could be implemented here
    </script>
</body>
</html>
