<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Livreur - CoDelivery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <svg class="w-8 h-8 mr-2" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M32 4c-11 0-20 9-20 20 0 11 20 36 20 36s20-25 20-36c0-11-9-20-20-20z" fill="#EA580C"/>
                        <circle cx="32" cy="24" r="12" fill="#FB923C"/>
                        <rect x="24" y="18" width="16" height="12" fill="#FFFFFF"/>
                        <path d="M24 22h16M32 18v12" stroke="#EA580C" stroke-width="1.5"/>
                        <path d="M14 44l-6 6M50 44l6 6" stroke="#FB923C" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                    <h1 class="text-xl font-bold text-gray-800">CoDelivery</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">Bienvenue, {{ Auth::user()->first_name }}</span>
                    <div class="relative">
                        <button class="flex items-center text-gray-700 hover:text-orange-500">
                            <i class="fas fa-user-circle text-2xl"></i>
                        </button>
                        <!-- Dropdown menu would go here -->
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-gray-700 hover:text-orange-500">
                            <i class="fas fa-sign-out-alt mr-1"></i> Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Tableau de bord Livreur</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-800">Livraisons en cours</h3>
                        <span class="text-orange-500 bg-orange-100 rounded-full px-3 py-1 text-sm font-medium">0</span>
                    </div>
                    <p class="text-gray-600 text-sm">Aucune livraison en cours pour le moment.</p>
                    <a href="#" class="mt-4 inline-flex items-center text-sm font-medium text-orange-600 hover:text-orange-500">
                        Voir les détails <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-800">Livraisons terminées</h3>
                        <span class="text-green-500 bg-green-100 rounded-full px-3 py-1 text-sm font-medium">0</span>
                    </div>
                    <p class="text-gray-600 text-sm">Aucune livraison terminée pour le moment.</p>
                    <a href="#" class="mt-4 inline-flex items-center text-sm font-medium text-orange-600 hover:text-orange-500">
                        Voir l'historique <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-800">Statistiques</h3>
                        <i class="fas fa-chart-line text-blue-500"></i>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="bg-gray-50 p-2 rounded">
                            <p class="text-xs text-gray-500">Distance totale</p>
                            <p class="text-lg font-semibold">0 km</p>
                        </div>
                        <div class="bg-gray-50 p-2 rounded">
                            <p class="text-xs text-gray-500">Délai moyen</p>
                            <p class="text-lg font-semibold">0 min</p>
                        </div>
                        <div class="bg-gray-50 p-2 rounded">
                            <p class="text-xs text-gray-500">Note moyenne</p>
                            <p class="text-lg font-semibold">-</p>
                        </div>
                        <div class="bg-gray-50 p-2 rounded">
                            <p class="text-xs text-gray-500">Commandes</p>
                            <p class="text-lg font-semibold">0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-800">Commandes disponibles</h3>
            </div>
            <div class="p-6">
                <div class="text-center py-8">
                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-search text-gray-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-800 mb-1">Aucune commande disponible</h3>
                    <p class="text-gray-600 text-sm">Les nouvelles commandes apparaîtront ici. Revenez plus tard!</p>
                </div>
            </div>
        </div>
        
        <div class="mt-8 bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-800">Carte des livraisons</h3>
            </div>
            <div class="p-6">
                <div class="bg-gray-100 h-64 rounded-lg flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-map-marker-alt text-orange-500 text-2xl mb-2"></i>
                        <p class="text-gray-600">La carte sera disponible lorsque vous aurez des livraisons en cours.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-sm text-gray-500">
                <p>&copy; 2025 CoDelivery. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</body>
</html>
