<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoDelivery - Historique des livraisons</title>
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
                    <a href="{{ route('livreur.commands') }}" class="text-white hover:text-orange-300 transition-colors">Commandes disponibles</a>
                    <a href="{{ route('livreur.historique') }}" class="text-white hover:text-orange-300 transition-colors border-b-2 border-orange-500 pb-1">Historique</a>
                    
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
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Page Title and Filter -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-4 md:mb-0">
                <h1 class="text-2xl font-bold text-gray-800">Historique des livraisons</h1>
                <p class="text-gray-600">Consultez toutes vos livraisons passées</p>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total des livraisons</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalDeliveries }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-truck-fast text-orange-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Gains totaux</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($totalEarnings, 2) }} DH</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-money-bill-wave text-orange-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Commandes en moyenne par jour</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalDeliveries > 0 ? number_format($totalDeliveries / max(1, now()->diffInDays($deliveries->min('delivered_at'))), 1) : '0' }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-chart-line text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Deliveries History Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-8">
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
                                Adresse
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Paiement
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($deliveries as $delivery)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $delivery->delivered_at ? $delivery->delivered_at->format('d/m/Y, H:i') : '--' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                            @if($delivery->service_type == 'restaurant')
                                                <i class="fa-solid fa-utensils text-orange-600 text-sm"></i>
                                            @elseif($delivery->service_type == 'pharmacy')
                                                <i class="fa-solid fa-prescription-bottle-medical text-orange-600 text-sm"></i>
                                            @elseif($delivery->service_type == 'market')
                                                <svg class="w-4 h-4 text-orange-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-orange-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $delivery->establishment_name }}</div>
                                            <div class="text-xs text-gray-500">#{{ substr($delivery->id, 0, 4) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $delivery->client->first_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $delivery->delivery_address }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    {{ number_format($delivery->price, 2) }} DH
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                    Aucune livraison trouvée dans votre historique.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="bg-gray-50 px-6 py-3 flex items-center justify-between border-t border-gray-200">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Affichage de <span class="font-medium">{{ $deliveries->firstItem() ?? 0 }}</span> à <span class="font-medium">{{ $deliveries->lastItem() ?? 0 }}</span> sur <span class="font-medium">{{ $deliveries->total() }}</span> résultats
                        </p>
                    </div>
                    <div>
                        {{ $deliveries->links() }}
                    </div>
                </div>
            </div>
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
</body>
</html>
