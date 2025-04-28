<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoDelivery - Tableau de bord client</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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
                    <a href="{{ route('client.dashboard') }}" class="text-white hover:text-orange-300 transition-colors">Home</a>
                    <a href="{{ route('client.commands') }}" class="text-white hover:text-orange-300 transition-colors">Mes commandes</a>                    
                     <!-- User Profile -->
                     <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center">
                                <span class="font-semibold text-sm">{{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}</span>
                            </div>
                            <span>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50" style="display: none;">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-orange-100">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Se déconnecter
                                    </div>
                                </button>
                            </form>
                        </div>
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
        <!-- Welcome Banner -->
        <div class="mb-8 bg-gradient-to-r from-orange-600 to-orange-800 rounded-2xl p-8 text-white">
            <div class="md:flex items-center justify-between">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-2xl font-bold mb-2">Bonjour, {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}!</h1>
                    <p>Que souhaitez-vous commander aujourd'hui?</p>
                </div>
                <a href="{{ route('client.commands.create') }}" class="bg-white text-orange-600 hover:bg-orange-100 rounded-full px-8 py-3 font-semibold flex items-center space-x-2 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Créer une commande</span>
                </a>
            </div>
        </div>
        
        <!-- Service Type Selection -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Services disponibles</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Restaurant -->
                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md cursor-pointer border-2 border-transparent hover:border-orange-500 transition-all">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fa-solid fa-utensils text-2xl text-orange-600"></i>
                        </div>
                        <h3 class="font-medium">Restaurants</h3>
                    </div>
                </div>
                
                <!-- Pharmacy -->
                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md cursor-pointer border-2 border-transparent hover:border-orange-500 transition-all">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fa-solid fa-prescription-bottle-medical text-2xl text-orange-600"></i>
                        </div>
                        <h3 class="font-medium">Pharmacies</h3>
                    </div>
                </div>
                
                <!-- Market -->
                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md cursor-pointer border-2 border-transparent hover:border-orange-500 transition-all">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-orange-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-medium">Courses</h3>
                    </div>
                </div>
                
                <!-- Package -->
                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md cursor-pointer border-2 border-transparent hover:border-orange-500 transition-all">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-orange-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4"/>
                            </svg>
                        </div>
                        <h3 class="font-medium">Colis</h3>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Ongoing Order (if any) -->
        @if($ongoingCommand)
        <div class="mb-8 bg-white rounded-xl p-6 shadow-sm border-l-4 border-orange-500">
            <h2 class="text-xl font-semibold mb-4">Commande en cours</h2>
            <div class="flex flex-col md:flex-row md:items-center justify-between">
                <div class="mb-4 md:mb-0">
                    <p class="text-gray-700">{{ ucfirst($ongoingCommand->service_type) }} - <span class="font-medium">{{ $ongoingCommand->establishment_name }}</span></p>
                    <div class="mt-2 flex items-center space-x-2">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-truck text-green-600 text-sm"></i>
                        </div>
                        <span class="text-green-600 font-medium">
                            @if($ongoingCommand->status == 'accepted')
                                Commande acceptée
                            @elseif($ongoingCommand->status == 'in_progress')
                                En cours de livraison
                            @endif
                        </span>
                    </div>
                </div>
                
                <div class="flex space-x-3">
                    <a href="{{ route('client.commands.show', $ongoingCommand->id) }}" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50">
                        Détails
                    </a>
                    <a href="{{ route('client.commands.track', $ongoingCommand->id) }}" class="bg-orange-100 text-orange-600 px-4 py-2 rounded-lg hover:bg-orange-200">
                        <i class="fa-solid fa-location-dot mr-2"></i>Suivre
                    </a>
                </div>
            </div>
        </div>
        @else
        <div class="mb-8 bg-white rounded-xl p-6 shadow-sm border-l-4 border-gray-300">
            <h2 class="text-xl font-semibold mb-4">Commande en cours</h2>
            <p class="text-gray-500">Vous n'avez aucune commande en cours actuellement.</p>
            <div class="mt-4">
                <a href="{{ route('client.commands.create') }}" class="inline-flex items-center text-orange-600 hover:text-orange-700">
                    <span>Créer une nouvelle commande</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
        @endif
        
        <!-- Available Delivery Drivers -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Livreurs disponibles</h2>
                <div class="flex items-center space-x-2 text-sm">
                    <span class="text-gray-500">Trier par:</span>
                    <select class="border rounded-lg py-1 px-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option>Meilleures notes</option>
                        <option>Distance</option>
                        <option>Temps estimé</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Driver 1 -->
                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md cursor-pointer transition-all">
                    <div class="flex items-center mb-4">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Livreur" class="w-14 h-14 rounded-full object-cover mr-4">
                        <div>
                            <h3 class="font-medium">Thomas Martin</h3>
                            <div class="flex items-center mt-1">
                                <div class="flex">
                                    <i class="fa-solid fa-star text-yellow-400"></i>
                                    <i class="fa-solid fa-star text-yellow-400"></i>
                                    <i class="fa-solid fa-star text-yellow-400"></i>
                                    <i class="fa-solid fa-star text-yellow-400"></i>
                                    <i class="fa-solid fa-star text-yellow-400"></i>
                                </div>
                                <span class="ml-1 text-sm text-gray-600">5.0 (124 livraisons)</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center text-sm text-gray-500">
                        <span><i class="fa-solid fa-location-dot mr-1"></i> À 1.2 km</span>
                        <span><i class="fa-regular fa-clock mr-1"></i> Disponible</span>
                        <button class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-lg transition-colors">
                            Choisir
                        </button>
                    </div>
                </div>
                
                <!-- Driver 2 -->
                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md cursor-pointer transition-all">
                    <div class="flex items-center mb-4">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Livreur" class="w-14 h-14 rounded-full object-cover mr-4">
                        <div>
                            <h3 class="font-medium">Sophie Dubois</h3>
                            <div class="flex items-center mt-1">
                                <div class="flex">
                                    <i class="fa-solid fa-star text-yellow-400"></i>
                                    <i class="fa-solid fa-star text-yellow-400"></i>
                                    <i class="fa-solid fa-star text-yellow-400"></i>
                                    <i class="fa-solid fa-star text-yellow-400"></i>
                                    <i class="fa-solid fa-star-half-stroke text-yellow-400"></i>
                                </div>
                                <span class="ml-1 text-sm text-gray-600">4.5 (89 livraisons)</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center text-sm text-gray-500">
                        <span><i class="fa-solid fa-location-dot mr-1"></i> À 0.8 km</span>
                        <span><i class="fa-regular fa-clock mr-1"></i> Disponible</span>
                        <button class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-lg transition-colors">
                            Choisir
                        </button>
                    </div>
                </div>
                
                <!-- Driver 3 -->
                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md cursor-pointer transition-all">
                    <div class="flex items-center mb-4">
                        <img src="https://randomuser.me/api/portraits/men/76.jpg" alt="Livreur" class="w-14 h-14 rounded-full object-cover mr-4">
                        <div>
                            <h3 class="font-medium">Lucas Bernard</h3>
                            <div class="flex items-center mt-1">
                                <div class="flex">
                                    <i class="fa-solid fa-star text-yellow-400"></i>
                                    <i class="fa-solid fa-star text-yellow-400"></i>
                                    <i class="fa-solid fa-star text-yellow-400"></i>
                                    <i class="fa-solid fa-star text-yellow-400"></i>
                                    <i class="fa-regular fa-star text-yellow-400"></i>
                                </div>
                                <span class="ml-1 text-sm text-gray-600">4.0 (56 livraisons)</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center text-sm text-gray-500">
                        <span><i class="fa-solid fa-location-dot mr-1"></i> À 2.5 km</span>
                        <span><i class="fa-regular fa-clock mr-1"></i> Disponible</span>
                        <button class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-lg transition-colors">
                            Choisir
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 text-center">
                <button class="text-orange-600 hover:text-orange-700 hover:underline">
                    Voir plus de livreurs
                </button>
            </div>
        </div>
        
        <!-- Previous Orders -->
        <div>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Commandes récentes</h2>
                <a href="{{ route('client.commands') }}" class="text-orange-600 hover:text-orange-700 flex items-center">
                    Voir toutes les commandes
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Détails</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if($recentCommands->count() > 0)
                                @foreach($recentCommands as $command)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $command->created_at->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ ucfirst($command->service_type) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $command->establishment_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($command->price, 2) }} DH</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'accepted' => 'bg-blue-100 text-blue-800',
                                                'in_progress' => 'bg-orange-100 text-orange-800',
                                                'delivered' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                            ];
                                            
                                            $statusLabels = [
                                                'pending' => 'En attente',
                                                'accepted' => 'Acceptée',
                                                'in_progress' => 'En livraison',
                                                'delivered' => 'Livrée',
                                                'cancelled' => 'Annulée',
                                            ];
                                        @endphp
                                        
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$command->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $statusLabels[$command->status] ?? ucfirst($command->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('client.commands.show', $command->id) }}" class="text-orange-600 hover:text-orange-900">Détails</a>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Vous n'avez pas encore de commandes. <a href="{{ route('client.commands.create') }}" class="text-orange-600 hover:underline">Créer votre première commande</a>.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Chat Button (Fixed) -->
    <div class="fixed bottom-8 right-8">
        <button class="bg-orange-600 hover:bg-orange-700 text-white w-16 h-16 rounded-full flex items-center justify-center shadow-lg transition-colors">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
        </button>
        <span class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold">
            2
        </span>
    </div>
    
    <!-- Footer with simplified version -->
    <footer class="bg-gradient-to-r from-orange-800 to-orange-950 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-2 mb-4 md:mb-0">
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
                </div>
                <div class="text-sm text-gray-400">
                    &copy; 2025 CoDelivery. Tous droits réservés.
                </div>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">Aide</a>
                    <a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">Confidentialité</a>
                    <a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">Conditions</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
