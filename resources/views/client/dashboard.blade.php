<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Client - CoDelivery</title>
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
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Tableau de bord Client</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-800">Commandes actives</h3>
                        <span class="text-orange-500 bg-orange-100 rounded-full px-3 py-1 text-sm font-medium">0</span>
                    </div>
                    <p class="text-gray-600 text-sm">Aucune commande active pour le moment.</p>
                    <a href="#" class="mt-4 inline-flex items-center text-sm font-medium text-orange-600 hover:text-orange-500">
                        Créer une commande <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-800">Commandes terminées</h3>
                        <span class="text-green-500 bg-green-100 rounded-full px-3 py-1 text-sm font-medium">0</span>
                    </div>
                    <p class="text-gray-600 text-sm">Aucune commande terminée pour le moment.</p>
                    <a href="#" class="mt-4 inline-flex items-center text-sm font-medium text-orange-600 hover:text-orange-500">
                        Voir l'historique <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-800">Livreurs favoris</h3>
                        <span class="text-blue-500 bg-blue-100 rounded-full px-3 py-1 text-sm font-medium">0</span>
                    </div>
                    <p class="text-gray-600 text-sm">Aucun livreur favori pour le moment.</p>
                    <a href="#" class="mt-4 inline-flex items-center text-sm font-medium text-orange-600 hover:text-orange-500">
                        Explorer les livreurs <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-800">Créer une nouvelle commande</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Remplissez le formulaire ci-dessous pour créer une nouvelle commande.</p>
                <form class="space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
                        <input type="text" id="title" name="title" 
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500"
                               placeholder="Ex: Livraison de courses">
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500"
                                  placeholder="Décrivez votre commande en détail..."></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Adresse de livraison</label>
                            <input type="text" id="address" name="address" 
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500"
                                   placeholder="Ex: 123 Rue de Paris, 75001 Paris">
                        </div>
                        
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priorité</label>
                            <select id="priority" name="priority"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="low">Basse</option>
                                <option value="medium" selected>Moyenne</option>
                                <option value="high">Haute</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <button type="submit" class="px-6 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-all font-medium">
                            Créer la commande
                        </button>
                    </div>
                </form>
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
