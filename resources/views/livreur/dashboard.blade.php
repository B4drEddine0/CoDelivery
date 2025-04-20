<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoDelivery - Tableau de bord livreur</title>
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
        
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
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
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#" class="text-white hover:text-orange-300 transition-colors">Mes livraisons</a>
                    <a href="#" class="text-white hover:text-orange-300 transition-colors">Historique</a>
                    <a href="#" class="text-white hover:text-orange-300 transition-colors">Aide</a>
                    
                    <!-- User Profile -->
                    <div class="relative">
                        <button class="flex items-center space-x-2 focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center">
                                <span class="font-semibold text-sm">ML</span>
                            </div>
                            <span>Marc Lambert</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
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
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Status and Earnings Banner -->
        <div class="mb-8 bg-gradient-to-r from-orange-600 to-orange-800 rounded-2xl p-8 text-white">
            <div class="md:flex items-center justify-between">
                <div class="mb-6 md:mb-0">
                    <h1 class="text-2xl font-bold mb-2">Bonjour, Marc!</h1>
                    <div class="flex items-center space-x-3">
                        <div class="bg-green-500/20 px-3 py-1 rounded-full flex items-center">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                            <span class="text-sm font-medium">En ligne</span>
                        </div>
                        <button class="text-sm bg-white/20 hover:bg-white/30 px-3 py-1 rounded-full transition-colors">
                            Passer hors ligne
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <p class="text-sm text-white/70 mb-1">Gains aujourd'hui</p>
                        <p class="text-2xl font-bold">€42,50</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <p class="text-sm text-white/70 mb-1">Livraisons</p>
                        <p class="text-2xl font-bold">7</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Current Delivery -->
        <div class="mb-8 bg-white rounded-xl p-6 shadow-sm border-l-4 border-green-500">
            <h2 class="text-xl font-semibold mb-4">Livraison en cours</h2>
            <div class="flex flex-col md:flex-row md:items-center justify-between">
                <div class="mb-4 md:mb-0">
                    <div class="flex items-center mb-2">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fa-solid fa-utensils text-orange-600"></i>
                        </div>
                        <div>
                            <p class="font-medium">Restaurant - <span class="font-semibold">Burger King</span></p>
                            <p class="text-sm text-gray-500">Commande #BK-2345</p>
                        </div>
                    </div>
                    
                    <!-- Progress Steps -->
                    <div class="flex items-center space-x-2 mt-4">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-check text-green-600"></i>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 relative">
                            <div class="absolute inset-0 bg-green-500" style="width: 66%;"></div>
                        </div>
                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-truck text-orange-600"></i>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200"></div>
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-flag-checkered text-gray-400"></i>
                        </div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-1 px-2">
                        <span>Récupéré</span>
                        <span>En livraison</span>
                        <span>Livré</span>
                    </div>
                </div>
                
                <div class="flex flex-col space-y-3">
                    <button class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg flex items-center justify-center space-x-2 transition-colors">
                        <i class="fa-solid fa-check"></i>
                        <span>Marquer comme livré</span>
                    </button>
                    <button class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg flex items-center justify-center space-x-2 hover:bg-gray-50 transition-colors">
                        <i class="fa-solid fa-phone"></i>
                        <span>Contacter le client</span>
                    </button>
                </div>
            </div>
            
            <!-- Delivery Details -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-medium mb-3 flex items-center">
                        <i class="fa-solid fa-location-dot text-orange-500 mr-2"></i>
                        Adresse de livraison
                    </h3>
                    <p class="text-gray-700">123 Rue de Paris, 75001 Paris</p>
                    <p class="text-gray-500 text-sm mt-1">Appartement 4B, 2ème étage, code: 4567</p>
                    <div class="mt-3">
                        <a href="#" class="text-orange-600 hover:text-orange-700 text-sm flex items-center">
                            <i class="fa-solid fa-map-location-dot mr-1"></i>
                            Voir sur la carte
                        </a>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-medium mb-3 flex items-center">
                        <i class="fa-solid fa-receipt text-orange-500 mr-2"></i>
                        Détails de la commande
                    </h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex justify-between">
                            <span>1x Whopper Menu</span>
                            <span>€9.90</span>
                        </li>
                        <li class="flex justify-between">
                            <span>1x King Fusion Oreo</span>
                            <span>€3.50</span>
                        </li>
                        <li class="flex justify-between">
                            <span>1x Nuggets x9</span>
                            <span>€5.95</span>
                        </li>
                        <li class="flex justify-between font-medium pt-2 border-t border-gray-200 mt-2">
                            <span>Total</span>
                            <span>€19.35</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- New Orders Available -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Nouvelles commandes disponibles</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Order 1 -->
                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md border border-gray-100 transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fa-solid fa-prescription-bottle-medical text-orange-600"></i>
                            </div>
                            <div>
                                <p class="font-medium">Pharmacie - <span class="font-semibold">Pharmacie Centrale</span></p>
                                <p class="text-sm text-gray-500">Commande #PH-7890</p>
                            </div>
                        </div>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Urgent</span>
                    </div>
                    
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-gray-500">Distance</p>
                            <p class="font-medium">3.2 km</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Estimation</p>
                            <p class="font-medium">15 min</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Paiement</p>
                            <p class="font-medium">€8.50</p>
                        </div>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2 rounded-lg transition-colors">
                            Accepter
                        </button>
                        <button class="flex-1 border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                            Refuser
                        </button>
                    </div>
                </div>
                
                <!-- Order 2 -->
                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md border border-gray-100 transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-orange-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium">Courses - <span class="font-semibold">Carrefour Express</span></p>
                                <p class="text-sm text-gray-500">Commande #CE-4567</p>
                            </div>
                        </div>
                        <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Standard</span>
                    </div>
                    
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-gray-500">Distance</p>
                            <p class="font-medium">1.8 km</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Estimation</p>
                            <p class="font-medium">25 min</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Paiement</p>
                            <p class="font-medium">€7.25</p>
                        </div>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2 rounded-lg transition-colors">
                            Accepter
                        </button>
                        <button class="flex-1 border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                            Refuser
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Performance Statistics -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Statistiques de performance</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Weekly Stats -->
                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <h3 class="font-medium text-gray-700 mb-4 flex items-center">
                        <i class="fa-solid fa-chart-line text-orange-500 mr-2"></i>
                        Cette semaine
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Livraisons</span>
                                <span class="font-medium">42</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-orange-500 h-2 rounded-full" style="width: 85%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Taux d'acceptation</span>
                                <span class="font-medium">92%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-orange-500 h-2 rounded-full" style="width: 92%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Satisfaction client</span>
                                <span class="font-medium">4.8/5</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-orange-500 h-2 rounded-full" style="width: 96%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Temps moyen</span>
                                <span class="font-medium">18 min</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-orange-500 h-2 rounded-full" style="width: 78%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Monthly Earnings Chart -->
                <div class="bg-white rounded-xl p-6 shadow-sm md:col-span-2">
                    <h3 class="font-medium text-gray-700 mb-4 flex items-center">
                        <i class="fa-solid fa-money-bill-trend-up text-orange-500 mr-2"></i>
                        Gains mensuels
                    </h3>
                    <div class="h-64 flex items-end space-x-2 mt-4">
                        <div class="flex-1 flex flex-col justify-end items-center">
                            <div class="bg-orange-500 w-full rounded-t-md" style="height: 40%"></div>
                            <span class="text-xs mt-1 text-gray-500">Lun</span>
                        </div>
                        <div class="flex-1 flex flex-col justify-end items-center">
                            <div class="bg-orange-500 w-full rounded-t-md" style="height: 65%"></div>
                            <span class="text-xs mt-1 text-gray-500">Mar</span>
                        </div>
                        <div class="flex-1 flex flex-col justify-end items-center">
                            <div class="bg-orange-500 w-full rounded-t-md" style="height: 50%"></div>
                            <span class="text-xs mt-1 text-gray-500">Mer</span>
                        </div>
                        <div class="flex-1 flex flex-col justify-end items-center">
                            <div class="bg-orange-500 w-full rounded-t-md" style="height: 75%"></div>
                            <span class="text-xs mt-1 text-gray-500">Jeu</span>
                        </div>
                        <div class="flex-1 flex flex-col justify-end items-center">
                            <div class="bg-orange-500 w-full rounded-t-md" style="height: 90%"></div>
                            <span class="text-xs mt-1 text-gray-500">Ven</span>
                        </div>
                        <div class="flex-1 flex flex-col justify-end items-center">
                            <div class="bg-orange-500 w-full rounded-t-md" style="height: 85%"></div>
                            <span class="text-xs mt-1 text-gray-500">Sam</span>
                        </div>
                        <div class="flex-1 flex flex-col justify-end items-center">
                            <div class="bg-orange-500/30 w-full rounded-t-md" style="height: 45%"></div>
                            <span class="text-xs mt-1 text-gray-500">Dim</span>
                        </div>
                    </div>
                    <div class="flex justify-between mt-4">
                        <div>
                            <p class="text-sm text-gray-500">Total cette semaine</p>
                            <p class="text-xl font-bold text-gray-800">€345,75</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Projection mensuelle</p>
                            <p class="text-xl font-bold text-gray-800">€1 380,00</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Deliveries -->
        <div>
            <h2 class="text-xl font-semibold mb-4">Livraisons récentes</h2>
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Commande
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Client
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Distance
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Paiement
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Delivery 1 -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Aujourd'hui, 14:25
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fa-solid fa-utensils text-orange-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">McDonald's</div>
                                            <div class="text-xs text-gray-500">#MD-1234</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">Sophie Martin</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    2.4 km
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    €6.50
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Livré
                                    </span>
                                </td>
                            </tr>
                            
                            <!-- Delivery 2 -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Aujourd'hui, 12:10
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fa-solid fa-prescription-bottle-medical text-orange-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">Pharmacie du Centre</div>
                                            <div class="text-xs text-gray-500">#PC-7890</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">Thomas Dubois</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    3.7 km
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    €8.75
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Livré
                                    </span>
                                </td>
                            </tr>
                            
                            <!-- Delivery 3 -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Aujourd'hui, 10:45
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-orange-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">Monoprix</div>
                                            <div class="text-xs text-gray-500">#MP-4567</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">Émilie Petit</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    1.8 km
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    €7.25
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Livré
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-6 py-3 flex justify-center">
                    <button class="text-orange-600 hover:text-orange-700 hover:underline text-sm font-medium">
                        Voir tout l'historique
                    </button>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Chat Button (Fixed) -->
    <div class="fixed bottom-8 right-8">
        <button class="bg-orange-600 hover:bg-orange-700 text-white w-16 h-16 rounded-full flex items-center justify-center shadow-lg transition-colors">
            <i class="fa-solid fa-headset text-2xl"></i>
        </button>
    </div>
    
    <!-- Footer -->
    <footer class="bg-gray-100 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex justify-center md:justify-start space-x-6">
                    <a href="#" class="text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-circle-question"></i>
                        <span class="ml-2">Aide</span>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-shield"></i>
                        <span class="ml-2">Confidentialité</span>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-file-contract"></i>
                        <span class="ml-2">Conditions</span>
                    </a>
                </div>
                <div class="mt-8 md:mt-0">
                    <p class="text-gray-500 text-sm">
                        &copy; 2025 CoDelivery. Tous droits réservés.
                    </p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>