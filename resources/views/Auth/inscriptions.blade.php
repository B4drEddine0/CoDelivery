<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - CoDelivery</title>
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
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <section class="w-full py-4">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-4">
                <div class="flex items-center justify-center space-x-2 mb-2">
                    <svg class="w-8 h-8" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M32 4c-11 0-20 9-20 20 0 11 20 36 20 36s20-25 20-36c0-11-9-20-20-20z" fill="#EA580C"/>
                        <circle cx="32" cy="24" r="12" fill="#FB923C"/>
                        <rect x="24" y="18" width="16" height="12" fill="#FFFFFF"/>
                        <path d="M24 22h16M32 18v12" stroke="#EA580C" stroke-width="1.5"/>
                        <path d="M14 44l-6 6M50 44l6 6" stroke="#FB923C" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                    <h1 class="text-2xl font-bold text-gray-800">CoDelivery - Inscription</h1>
                </div>
                <p class="text-sm text-gray-600 mt-1">Rejoignez CoDelivery et profitez de nos services de livraison</p>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6">
                <form>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="first-name" class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                            <input type="text" id="first-name" name="first-name" 
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500"
                                   placeholder="Jean">
                        </div>
                        <div>
                            <label for="last-name" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                            <input type="text" id="last-name" name="last-name" 
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500"
                                   placeholder="Dupont">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email" 
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500"
                                   placeholder="jean.dupont@example.com">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input type="tel" id="phone" name="phone" 
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500"
                                   placeholder="+33 6 12 34 56 78">
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                            <input type="password" id="password" name="password" 
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500"
                                   placeholder="••••••••">
                        </div>
                        <div>
                            <label for="confirm-password" class="block text-sm font-medium text-gray-700 mb-1">Confirmer</label>
                            <input type="password" id="confirm-password" name="confirm-password" 
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500"
                                   placeholder="••••••••">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
                            <select id="role" name="role"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="client" selected>Client - Je souhaite commander</option>
                                <option value="courier">Livreur - Je souhaite effectuer des livraisons</option>
                            </select>
                        </div>
                        
                        <div class="md:col-span-2 my-2">
                            <div class="flex items-start">
                                <input id="terms" name="terms" type="checkbox" class="h-4 w-4 mt-1 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                <label for="terms" class="ml-2 block text-xs text-gray-700">
                                    J'accepte les <a href="#" class="text-orange-600 hover:text-orange-500">conditions générales</a> et la <a href="#" class="text-orange-600 hover:text-orange-500">politique de confidentialité</a>
                                </label>
                            </div>
                        </div>
                        
                        <div class="md:col-span-2">
                            <button type="submit" class="w-full px-6 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-all font-medium">
                                S'inscrire
                            </button>
                        </div>
                    </div>
                </form>
                
                <div class="mt-4">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-xs">
                            <span class="px-2 bg-white text-gray-500">Ou inscrivez-vous avec</span>
                        </div>
                    </div>

                    <div class="mt-3 grid grid-cols-3 gap-2">
                        <button class="w-full flex justify-center py-1.5 px-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <i class="fab fa-google text-red-500"></i>
                        </button>
                        <button class="w-full flex justify-center py-1.5 px-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <i class="fab fa-facebook-f text-blue-600"></i>
                        </button>
                        <button class="w-full flex justify-center py-1.5 px-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <i class="fab fa-apple"></i>
                        </button>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <p class="text-xs text-gray-600">
                        Vous avez déjà un compte?
                        <a href="login.html" class="font-medium text-orange-600 hover:text-orange-500">Se connecter</a>
                    </p>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="index.html" class="text-xs font-medium text-orange-600 hover:text-orange-500">
                    <i class="fas fa-arrow-left mr-1"></i>Retour à l'accueil
                </a>
            </div>
        </div>
    </section>
</body>
</html>
