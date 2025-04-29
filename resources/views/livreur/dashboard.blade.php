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
                    <a href="{{ route('livreur.dashboard') }}" class="text-white hover:text-orange-300 transition-colors">Tableau de bord</a>
                    <a href="{{ route('livreur.commands') }}" class="text-white hover:text-orange-300 transition-colors">Commandes</a>
                    <a href="{{ route('livreur.historique') }}" class="text-white hover:text-orange-300 transition-colors">Historique</a>
                    
                    <!-- User Profile -->
                    <div class="relative">
                        <button class="flex items-center space-x-2 focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center">
                                <span class="font-semibold text-sm">{{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}</span>
                            </div>
                            <span>{{ Auth::user()->full_name }}</span>
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
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif
        
        <!-- Status and Earnings Banner -->
        <div class="mb-8 bg-gradient-to-r from-orange-600 to-orange-800 rounded-2xl p-8 text-white">
            <div class="md:flex items-center justify-between">
                <div class="mb-6 md:mb-0">
                    <h1 class="text-2xl font-bold mb-2">Bonjour, {{ Auth::user()->first_name }}!</h1>
                    <div class="flex items-center space-x-3">
                        <div class="bg-green-500/20 px-3 py-1 rounded-full flex items-center">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                            <span class="text-sm font-medium">En ligne</span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <p class="text-sm text-white/70 mb-1">Gains aujourd'hui</p>
                        <p class="text-2xl font-bold">{{ number_format($todayEarnings, 2) }} DH</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <p class="text-sm text-white/70 mb-1">Livraisons</p>
                        <p class="text-2xl font-bold">{{ $todayDeliveries }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Current Delivery -->
        @if($currentCommand)
        <div class="mb-8 bg-white rounded-xl p-6 shadow-sm border-l-4 {{ $currentCommand->status == 'accepted' ? 'border-blue-500' : 'border-green-500' }}">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Livraison en cours</h2>
                <form action="{{ route('livreur.commands.reset', $currentCommand) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-gray-700" title="Réinitialiser cette commande">
                        <i class="fa-solid fa-rotate-left"></i>
                    </button>
                </form>
            </div>
            <div class="flex flex-col md:flex-row md:items-center justify-between">
                <div class="mb-4 md:mb-0">
                    <div class="flex items-center mb-2">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                            @if($currentCommand->service_type == 'restaurant')
                            <i class="fa-solid fa-utensils text-orange-600"></i>
                            @elseif($currentCommand->service_type == 'pharmacy')
                            <i class="fa-solid fa-prescription-bottle-medical text-orange-600"></i>
                            @elseif($currentCommand->service_type == 'market')
                            <i class="fa-solid fa-shopping-basket text-orange-600"></i>
                            @else
                            <i class="fa-solid fa-box text-orange-600"></i>
                            @endif
                        </div>
                        <div>
                            <p class="font-medium">{{ ucfirst($currentCommand->service_type) }} - <span class="font-semibold">{{ $currentCommand->establishment_name }}</span></p>
                            <p class="text-sm text-gray-500">Commande #{{ $currentCommand->id }}</p>
                        </div>
                    </div>
                    
                    <!-- Progress Steps -->
                    <div class="flex items-center space-x-2 mt-4">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-check text-green-600"></i>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 relative">
                            <div class="absolute inset-0 bg-green-500" style="width: 100%;"></div>
                        </div>
                        <div class="w-8 h-8 {{ $currentCommand->status == 'in_progress' ? 'bg-orange-100' : 'bg-gray-100' }} rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-truck {{ $currentCommand->status == 'in_progress' ? 'text-orange-600' : 'text-gray-400' }}"></i>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 relative">
                            <div class="absolute inset-0 bg-green-500" style="width: {{ $currentCommand->status == 'in_progress' ? '50%' : '0%' }};"></div>
                        </div>
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-flag-checkered text-gray-400"></i>
                        </div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-1 px-2">
                        <span>Acceptée</span>
                        <span>En livraison</span>
                        <span>Livrée</span>
                    </div>
                </div>
                
                <div class="flex flex-col space-y-3">
                    @if($currentCommand->status == 'accepted')
                    <form action="{{ route('livreur.commands.start', $currentCommand) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg flex items-center justify-center space-x-2 transition-colors w-full">
                            <i class="fa-solid fa-truck"></i>
                            <span>Démarrer la livraison</span>
                        </button>
                    </form>
                    @elseif($currentCommand->status == 'in_progress')
                    <form action="{{ route('livreur.commands.complete', $currentCommand) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg flex items-center justify-center space-x-2 transition-colors w-full">
                            <i class="fa-solid fa-check"></i>
                            <span>Marquer comme livré</span>
                        </button>
                    </form>
                    @endif
                    <a href="tel:{{ $currentCommand->client->phone }}" class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg flex items-center justify-center space-x-2 hover:bg-gray-50 transition-colors">
                        <i class="fa-solid fa-phone"></i>
                        <span>Contacter le client</span>
                    </a>
                </div>
            </div>
            
            <!-- Delivery Details -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-medium mb-3 flex items-center">
                        <i class="fa-solid fa-location-dot text-orange-500 mr-2"></i>
                        Adresse de livraison
                    </h3>
                    <p class="text-gray-700">{{ $currentCommand->delivery_address }}</p>
                    <div class="mt-3">
                        <a href="commands/{{ $currentCommand->id }}/track" class="text-orange-600 hover:text-orange-700 text-sm flex items-center">
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
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ $currentCommand->title }}</span>
                        </div>
                        @if($currentCommand->description)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ $currentCommand->description }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-sm font-medium mt-2 pt-2 border-t border-gray-200">
                            <span>Prix de livraison</span>
                            <span>{{ number_format($currentCommand->price, 2) }} DH</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="mb-8 bg-white rounded-xl p-6 shadow-sm">
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-clipboard-list text-gray-400 text-2xl"></i>
                </div>
                <h2 class="text-xl font-semibold mb-2">Aucune livraison en cours</h2>
                <p class="text-gray-600 mb-4">Vous n'avez pas de livraison en cours pour le moment.</p>
                <a href="{{ route('livreur.commands') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg inline-flex items-center justify-center space-x-2 transition-colors">
                    <i class="fa-solid fa-search"></i>
                    <span>Voir les commandes disponibles</span>
                </a>
            </div>
        </div>
        @endif
        
   
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
                                    Type
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Adresse
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Montant
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentlyCompletedCommands as $command)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $command->delivered_at ? $command->delivered_at->format('d/m/Y H:i') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="ml-0">
                                            <div class="text-sm font-medium text-gray-900">#{{ $command->id }} - {{ $command->title }}</div>
                                            <div class="text-xs text-gray-500">{{ $command->establishment_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-orange-100 flex items-center justify-center mr-2">
                                            @if($command->service_type == 'restaurant')
                                            <i class="fa-solid fa-utensils text-orange-600 text-xs"></i>
                                            @elseif($command->service_type == 'pharmacy')
                                            <i class="fa-solid fa-prescription-bottle-medical text-orange-600 text-xs"></i>
                                            @elseif($command->service_type == 'market')
                                            <i class="fa-solid fa-shopping-basket text-orange-600 text-xs"></i>
                                            @else
                                            <i class="fa-solid fa-box text-orange-600 text-xs"></i>
                                            @endif
                                        </div>
                                        <span class="text-sm text-gray-900">{{ ucfirst($command->service_type) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ Str::limit($command->delivery_address, 30) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    {{ number_format($command->price, 2) }} DH
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($command->delivery_address) }}" target="_blank" class="text-orange-600 hover:text-orange-700 text-sm">
                                        <i class="fa-solid fa-map-location-dot"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Aucune livraison récente à afficher
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-6 py-3">
                    {{ $recentlyCompletedCommands->links() }}
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