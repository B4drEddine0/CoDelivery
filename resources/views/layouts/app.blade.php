<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'CoDelivery' }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Tailwind Config -->
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
                        },
                        orange: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                            950: '#431407',
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
    
    @yield('styles')
</head>
<body class="bg-gray-50">
    <header class="bg-gradient-to-r from-orange-800 to-orange-950 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-2">
                    <svg class="w-10 h-10" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M32 4c-11 0-20 9-20 20 0 11 20 36 20 36s20-25 20-36c0-11-9-20-20-20z" fill="#EA580C"/>
                        <circle cx="32" cy="24" r="8" fill="white"/>
                    </svg>
                    <a href="{{ auth()->user() && auth()->user()->isClient() ? route('client.dashboard') : (auth()->user() && auth()->user()->isLivreur() ? route('livreur.dashboard') : '/') }}" class="text-xl font-bold">CoDelivery</a>
                </div>
                
                @auth
                <div class="flex items-center">
                    <div class="hidden md:block">
                        <div class="flex items-center space-x-4">
                            @if(auth()->user()->isClient())
                                <a href="{{ route('client.dashboard') }}" class="text-white hover:text-orange-200 px-3 py-2 rounded-md text-sm font-medium">Tableau de bord</a>
                                <a href="{{ route('client.commands') }}" class="text-white hover:text-orange-200 px-3 py-2 rounded-md text-sm font-medium">Mes commandes</a>
                                <a href="{{ route('client.commands.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-2 rounded-md text-sm font-medium">Nouvelle commande</a>
                            @elseif(auth()->user()->isLivreur())
                                <a href="{{ route('livreur.dashboard') }}" class="text-white hover:text-orange-200 px-3 py-2 rounded-md text-sm font-medium">Tableau de bord</a>
                                <a href="{{ route('livreur.commands') }}" class="text-white hover:text-orange-200 px-3 py-2 rounded-md text-sm font-medium">Mes livraisons</a>
                            @endif
                        </div>
                    </div>
                    
                    <div class="ml-4 relative" x-data="{ open: false }">
                        <div>
                            <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-orange-800 focus:ring-white">
                                <div class="bg-orange-600 rounded-full w-8 h-8 flex items-center justify-center">
                                    <span class="text-white font-medium">{{ substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1) }}</span>
                                </div>
                            </button>
                        </div>
                        
                        <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50" style="display: none;">
                            <div class="px-4 py-2 border-b">
                                <p class="text-sm font-medium">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mon profil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Se déconnecter</button>
                            </form>
                        </div>
                    </div>
                </div>
                @else
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-white hover:text-orange-200 px-3 py-2 rounded-md text-sm font-medium">Connexion</a>
                    <a href="{{ route('register') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-2 rounded-md text-sm font-medium">Inscription</a>
                </div>
                @endauth
            </div>
        </div>
    </header>
    
    <main>
        @yield('content')
    </main>
    
    @yield('scripts')
</body>
</html>
