<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoDelivery - Mes Commandes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }
        main {
            flex: 1;
        }
    </style>
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
                    <a href="{{ route('client.dashboard') }}" class="text-white hover:text-orange-300 transition-colors">Home</a>
                    <a href="{{ route('client.commands') }}" class="text-white hover:text-orange-300 transition-colors border-b-2 border-orange-500 pb-1">Mes commandes</a>
                    
                    <!-- User Profile -->
                    <div class="relative">
                        <button class="flex items-center space-x-2 focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center">
                                <span class="font-semibold text-sm">{{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}</span>
                            </div>
                            <span>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
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
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold">Mes Commandes</h1>
            <a href="{{ route('client.commands.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Nouvelle commande</span>
            </a>
        </div>
        
        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <form action="{{ route('client.commands') }}" method="GET" class="flex flex-col md:flex-row md:items-end space-y-4 md:space-y-0 md:space-x-4">
                <div class="flex-1">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select id="status" name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Tous les statuts</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Acceptée</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En livraison</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Livrée</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>
                
                <div class="flex-1">
                    <label for="service_type" class="block text-sm font-medium text-gray-700 mb-1">Type de service</label>
                    <select id="service_type" name="service_type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Tous les services</option>
                        <option value="restaurant" {{ request('service_type') == 'restaurant' ? 'selected' : '' }}>Restaurant</option>
                        <option value="pharmacy" {{ request('service_type') == 'pharmacy' ? 'selected' : '' }}>Pharmacie</option>
                        <option value="market" {{ request('service_type') == 'market' ? 'selected' : '' }}>Courses</option>
                        <option value="package" {{ request('service_type') == 'package' ? 'selected' : '' }}>Colis</option>
                    </select>
                </div>
                
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="Rechercher...">
                </div>
                
                <div>
                    <button type="submit" class="w-full md:w-auto bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg">
                        Filtrer
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Commands List -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            @if($commands->count() > 0)
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
                            @foreach($commands as $command)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $command->created_at->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @php
                                                $bgColors = [
                                                    'restaurant' => 'bg-orange-100',
                                                    'pharmacy' => 'bg-green-100',
                                                    'market' => 'bg-blue-100',
                                                    'package' => 'bg-purple-100',
                                                ];
                                                
                                                $serviceIcons = [
                                                    'restaurant' => '<i class="fa-solid fa-utensils text-orange-600"></i>',
                                                    'pharmacy' => '<i class="fa-solid fa-prescription-bottle-medical text-green-600"></i>',
                                                    'market' => '<i class="fa-solid fa-cart-shopping text-blue-600"></i>',
                                                    'package' => '<i class="fa-solid fa-box text-purple-600"></i>',
                                                ];
                                            @endphp
                                            
                                            <div class="w-8 h-8 {{ $bgColors[$command->service_type] ?? 'bg-gray-100' }} rounded-full flex items-center justify-center mr-3">
                                                {!! $serviceIcons[$command->service_type] ?? '<i class="fa-solid fa-question text-gray-600"></i>' !!}
                                            </div>
                                            <span class="font-medium">{{ ucfirst($command->service_type) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $command->title }}</p>
                                            <p class="text-sm text-gray-500">{{ $command->establishment_name }}</p>
                                        </div>
                                    </td>
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
                                        <a href="{{ route('client.commands.show', $command->id) }}" class="text-orange-600 hover:text-orange-900 mr-3">Détails</a>
                                        
                                        @if($command->status == 'pending')
                                            <form action="{{ route('client.commands.cancel', $command->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900">Annuler</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $commands->links() }}
                </div>
            @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-inbox text-gray-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Aucune commande trouvée</h3>
                    <p class="text-gray-500 mb-4">Vous n'avez pas encore passé de commande ou aucune commande ne correspond à vos critères de recherche.</p>
                    <a href="{{ route('client.commands.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Créer une commande
                    </a>
                </div>
            @endif
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="bg-gradient-to-r from-orange-800 to-orange-950 text-white py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex justify-center md:justify-start">
                    <svg class="w-10 h-10" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Location Pin -->
                        <path d="M32 4c-11 0-20 9-20 20 0 11 20 36 20 36s20-25 20-36c0-11-9-20-20-20z" fill="#EA580C"/>
                        <!-- Inner Circle -->
                        <circle cx="32" cy="24" r="12" fill="#FB923C"/>
                        <!-- Package Icon -->
                        <rect x="24" y="18" width="16" height="12" fill="#FFFFFF"/>
                        <path d="M24 22h16M32 18v12" stroke="#EA580C" stroke-width="1.5"/>
                    </svg>
                    <span class="ml-2 text-xl font-bold">CoDelivery</span>
                </div>
                <p class="mt-4 text-center md:mt-0 md:text-right text-sm text-gray-300">
                    &copy; 2025 CoDelivery. Tous droits réservés.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
