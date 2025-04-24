<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - CoDelivery</title>
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Location Permission System -->
    <script src="/js/location-permission.js"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <!-- Force location permission system to show on login -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Clear any previous location permissions to force the popup to show again
            sessionStorage.removeItem('locationPermissionHandled');
            sessionStorage.removeItem('locationVerified');
            localStorage.removeItem('userLocation');
            localStorage.removeItem('userCity');
            
            // Initialize the location permission system with forceShow=true
            setTimeout(function() {
                if (!window.locationSystem) {
                    window.locationSystem = new LocationPermissionSystem();
                    window.locationSystem.init(true); // Force showing the popup
                } else {
                    window.locationSystem.init(true); // Force showing the popup
                }
            }, 1000); // Short delay to ensure the login form is visible first
        });
    </script>
    <section class="py-10 w-full">
        <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <div class="flex items-center justify-center space-x-2 mb-4">
                    <svg class="w-10 h-10" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M32 4c-11 0-20 9-20 20 0 11 20 36 20 36s20-25 20-36c0-11-9-20-20-20z" fill="#EA580C"/>
                        <circle cx="32" cy="24" r="12" fill="#FB923C"/>
                        <rect x="24" y="18" width="16" height="12" fill="#FFFFFF"/>
                        <path d="M24 22h16M32 18v12" stroke="#EA580C" stroke-width="1.5"/>
                        <path d="M14 44l-6 6M50 44l6 6" stroke="#FB923C" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                    <h1 class="text-2xl font-bold text-gray-800">CoDelivery - Connexion</h1>
                </div>
                <p class="text-gray-600 mt-2">Connectez-vous pour accéder à votre compte</p>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="space-y-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" id="email" name="email" 
                                   class="w-full px-4 py-3 rounded-xl border @error('email') border-red-500 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-orange-500"
                                   placeholder="votre@email.com" value="{{ old('email') }}" required>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
                            <input type="password" id="password" name="password" 
                                   class="w-full px-4 py-3 rounded-xl border @error('password') border-red-500 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-orange-500"
                                   placeholder="••••••••" required>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                <label for="remember-me" class="ml-2 block text-sm text-gray-700">Se souvenir de moi</label>
                            </div>
                            <a href="#" class="text-sm font-medium text-orange-600 hover:text-orange-500">Mot de passe oublié?</a>
                        </div>
                        
                        <button type="submit" class="w-full px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-xl transition-all font-medium">
                            Se connecter
                        </button>
                    </div>
                </form>
                
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Ou continuez avec</span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-3 gap-3">
                        <button class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <i class="fab fa-google text-red-500"></i>
                        </button>
                        <button class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <i class="fab fa-facebook-f text-blue-600"></i>
                        </button>
                        <button class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <i class="fab fa-apple"></i>
                        </button>
                    </div>
                </div>
                
                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600">
                        Vous n'avez pas de compte?
                        <a href="{{ route('register') }}" class="font-medium text-orange-600 hover:text-orange-500">S'inscrire</a>
                    </p>
                </div>
            </div>

            <div class="text-center mt-6">
                <a href="{{ url('/') }}" class="text-sm font-medium text-orange-600 hover:text-orange-500">
                    <i class="fas fa-arrow-left mr-2"></i>Retour à l'accueil
                </a>
            </div>
        </div>
    </section>
</body>
</html>
