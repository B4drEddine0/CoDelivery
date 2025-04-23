<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoDelivery - Commandes disponibles</title>
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
                    <a href="{{ route('livreur.dashboard') }}" class="text-white hover:text-orange-300 transition-colors">Tableau de bord</a>
                    <a href="{{ route('livreur.commands') }}" class="text-white hover:text-orange-300 transition-colors border-b-2 border-orange-500 pb-1">Commandes disponibles</a>
                    
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

        <!-- Page Title and Filter -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-4 md:mb-0">
                <h1 class="text-2xl font-bold text-gray-800">Commandes disponibles</h1>
                <p class="text-gray-600">Parcourez et acceptez les commandes disponibles dans votre zone</p>
            </div>
            
            <form action="{{ route('livreur.commands') }}" method="GET" class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                <!-- Date Filter -->
                <div class="relative">
                    <input type="date" name="date" value="{{ request('date') }}" class="appearance-none bg-white border border-gray-300 rounded-lg py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                
                <!-- Type Filter -->
                <div class="relative">
                    <select name="service_type" class="appearance-none bg-white border border-gray-300 rounded-lg py-2 px-4 pr-8 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Tous les types</option>
                        <option value="restaurant" {{ request('service_type') == 'restaurant' ? 'selected' : '' }}>Restaurant</option>
                        <option value="pharmacy" {{ request('service_type') == 'pharmacy' ? 'selected' : '' }}>Pharmacie</option>
                        <option value="market" {{ request('service_type') == 'market' ? 'selected' : '' }}>Courses</option>
                        <option value="package" {{ request('service_type') == 'package' ? 'selected' : '' }}>Colis</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
                
                <!-- Priority Filter -->
                <div class="relative">
                    <select name="priority" class="appearance-none bg-white border border-gray-300 rounded-lg py-2 px-4 pr-8 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Toutes priorités</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Urgent</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Standard</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Basse</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    Filtrer
                </button>

                @if(request()->has('date') || request()->has('service_type') || request()->has('priority'))
                <a href="{{ route('livreur.commands') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors text-center">
                    Réinitialiser
                </a>
                @endif
            </form>
        </div>
        
      
        <!-- My Current Commands Section -->
        @if($myCommands->count() > 0)
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Mes commandes en cours</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($myCommands->where('status', '!=', 'delivered')->where('status', '!=', 'cancelled') as $command)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border-l-4 {{ $command->status == 'accepted' ? 'border-blue-500' : 'border-green-500' }}">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                    @if($command->service_type == 'restaurant')
                                    <i class="fa-solid fa-utensils text-orange-600"></i>
                                    @elseif($command->service_type == 'pharmacy')
                                    <i class="fa-solid fa-prescription-bottle-medical text-orange-600"></i>
                                    @elseif($command->service_type == 'market')
                                    <i class="fa-solid fa-shopping-basket text-orange-600"></i>
                                    @else
                                    <i class="fa-solid fa-box text-orange-600"></i>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">{{ $command->title }}</h3>
                                    <p class="text-sm text-gray-500">{{ $command->establishment_name }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $command->status == 'accepted' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $command->status == 'accepted' ? 'Acceptée' : 'En cours' }}
                            </span>
                        </div>
                        
                        <div class="space-y-3 mb-4">
                            <div class="flex items-start">
                                <i class="fa-solid fa-location-dot text-gray-400 mt-1 mr-2"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Adresse de retrait:</p>
                                    <p class="text-sm text-gray-700">{{ $command->pickup_address }}</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fa-solid fa-location-dot text-orange-500 mt-1 mr-2"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Adresse de livraison:</p>
                                    <p class="text-sm text-gray-700">{{ $command->delivery_address }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-500">Prix de livraison</p>
                                <p class="font-semibold text-gray-800">{{ number_format($command->price, 2) }} DH</p>
                            </div>
                            
                            <div class="space-x-2">
                                @if($command->status == 'accepted')
                                <form action="{{ route('livreur.commands.start', $command) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                                        Démarrer
                                    </button>
                                </form>
                                @elseif($command->status == 'in_progress')
                                <form action="{{ route('livreur.commands.complete', $command) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                                        Terminer
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Available Orders Section -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Commandes disponibles ({{ $availableCommands->count() }})</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($availableCommands as $command)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border-l-4 {{ $command->priority == 'high' ? 'border-red-500' : 'border-orange-500' }}">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                    @if($command->service_type == 'restaurant')
                                    <i class="fa-solid fa-utensils text-orange-600"></i>
                                    @elseif($command->service_type == 'pharmacy')
                                    <i class="fa-solid fa-prescription-bottle-medical text-orange-600"></i>
                                    @elseif($command->service_type == 'market')
                                    <i class="fa-solid fa-shopping-basket text-orange-600"></i>
                                    @else
                                    <i class="fa-solid fa-box text-orange-600"></i>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">{{ $command->title }}</h3>
                                    <p class="text-sm text-gray-500">{{ $command->establishment_name }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $command->priority == 'high' ? 'bg-red-100 text-red-800' : ($command->priority == 'medium' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ $command->priority == 'high' ? 'Urgent' : ($command->priority == 'medium' ? 'Standard' : 'Basse') }}
                            </span>
                        </div>
                        
                        <div class="space-y-3 mb-4">
                            <div class="flex items-start">
                                <i class="fa-solid fa-location-dot text-gray-400 mt-1 mr-2"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Adresse de retrait:</p>
                                    <p class="text-sm text-gray-700">{{ $command->pickup_address }}</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fa-solid fa-location-dot text-orange-500 mt-1 mr-2"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Adresse de livraison:</p>
                                    <p class="text-sm text-gray-700">{{ $command->delivery_address }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-500">Prix de livraison</p>
                                <p class="font-semibold text-gray-800">{{ number_format($command->price, 2) }} DH</p>
                            </div>
                            
                            <div class="space-x-2">
                                <a href="{{ route('livreur.commands.show', $command) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-3 rounded-lg text-sm transition-colors inline-block">
                                    <i class="fa-solid fa-circle-info mr-1"></i> Détails
                                </a>
                                @if($canAcceptCommands)
                                <form action="{{ route('livreur.commands.accept', $command) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                                        Accepter
                                    </button>
                                </form>
                                @else
                                <button type="button" class="bg-gray-400 text-white font-medium py-2 px-4 rounded-lg text-sm cursor-not-allowed" title="Vous avez déjà une commande en cours. Veuillez la terminer avant d'en accepter une nouvelle.">
                                    Accepter
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-3 bg-white rounded-xl shadow-sm overflow-hidden p-6 text-center">
                    <i class="fa-solid fa-clipboard-list text-gray-400 text-4xl mb-3"></i>
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">Aucune commande disponible</h3>
                    <p class="text-gray-600">Il n'y a pas de commandes disponibles pour le moment. Revenez plus tard !</p>
                </div>
                @endforelse
            </div>
            
            @if($availableCommands->count() > 9)
            <!-- Load More Button -->
            <div class="mt-6 text-center">
                <button class="bg-white border border-gray-300 rounded-lg py-2 px-4 text-gray-700 hover:bg-gray-50 transition-colors">
                    Voir plus de commandes
                    <i class="fa-solid fa-chevron-down ml-1"></i>
                </button>
            </div>
            @endif
        </div>
    </main>
    
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
    
    <!-- Chat Button -->
    <div class="fixed bottom-6 right-6">
        <button class="bg-orange-600 hover:bg-orange-700 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg transition-colors">
            <i class="fa-solid fa-headset text-xl"></i>
        </button>
    </div>
</body>
</html>
