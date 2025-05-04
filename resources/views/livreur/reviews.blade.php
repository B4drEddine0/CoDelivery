<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoDelivery - Mes évaluations</title>
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
    <header class="bg-gradient-to-r from-orange-800 to-orange-950 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-2">
                    <a href="{{ route('livreur.dashboard') }}" class="flex items-center space-x-2">
                        <svg class="w-10 h-10" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M32 4c-11 0-20 9-20 20 0 11 20 36 20 36s20-25 20-36c0-11-9-20-20-20z" fill="#EA580C"/>
                            <circle cx="32" cy="24" r="12" fill="#FB923C"/>
                            <rect x="24" y="18" width="16" height="12" fill="#FFFFFF"/>
                            <path d="M24 22h16M32 18v12" stroke="#EA580C" stroke-width="1.5"/>
                            <path d="M14 44l-6 6M50 44l6 6" stroke="#FB923C" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                        <span class="text-xl font-bold">CoDelivery</span>
                    </a>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('livreur.dashboard') }}" class="text-white hover:text-orange-300 transition-colors">Tableau de bord</a>
                    <a href="{{ route('livreur.commands') }}" class="text-white hover:text-orange-300 transition-colors">Commandes</a>
                    <a href="{{ route('livreur.historique') }}" class="text-white hover:text-orange-300 transition-colors">Historique</a>
                    <a href="{{ route('livreur.reviews') }}" class="text-white hover:text-orange-300 transition-colors border-b-2 border-orange-500 pb-1">Évaluations</a>
                    
                    <div class="relative">
                        <button class="flex items-center space-x-2 focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center">
                                <span class="font-semibold text-sm">{{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}</span>
                            </div>
                        </button>
                    </div>
                </div>
                
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

    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold">Mes évaluations</h1>
            <p class="text-gray-600">Consultez les avis laissés par vos clients</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex flex-col items-center justify-center">
                    <div class="text-5xl font-bold text-gray-800 mb-2">{{ number_format($averageRating, 1) }}</div>
                    <div class="flex items-center mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($averageRating))
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endif
                        @endfor
                    </div>
                    <div class="text-sm text-gray-500">{{ $totalReviews }} évaluations</div>
                </div>
                
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold mb-4">Répartition des évaluations</h3>
                    
                    @for($i = 5; $i >= 1; $i--)
                        @php
                            $count = $ratingDistribution[$i] ?? 0;
                            $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                        @endphp
                        <div class="flex items-center mb-2">
                            <div class="flex items-center w-20">
                                <span class="text-sm mr-2">{{ $i }}</span>
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 h-4 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-orange-500" style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="w-16 text-right">
                                <span class="text-sm text-gray-500">{{ $count }}</span>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <h2 class="text-lg font-semibold p-6 border-b">Tous les avis</h2>
            
            @if($reviews->count() > 0)
                <div class="divide-y">
                    @foreach($reviews as $review)
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <p class="font-semibold">{{ $review->first_name }} {{ $review->last_name }}</p>
                                    <div class="flex items-center mt-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">{{ date('d/m/Y', strtotime($review->created_at)) }}</p>
                                    <div class="flex items-center mt-1">
                                        <div class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">
                                            {{ ucfirst($review->service_type) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <p class="text-sm font-medium text-gray-800">{{ $review->command_title }}</p>
                            </div>
                            <p class="text-gray-600">{{ $review->comment }}</p>
                        </div>
                    @endforeach
                </div>
                
                <div class="px-6 py-4 border-t">
                    {{ $reviews->links() }}
                </div>
            @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-star text-gray-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Aucune évaluation trouvée</h3>
                    <p class="text-gray-500">Vous n'avez pas encore reçu d'évaluations de vos clients.</p>
                </div>
            @endif
        </div>
    </main>
    
    <footer class="bg-gradient-to-r from-orange-800 to-orange-950 text-white py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex justify-center md:justify-start">
                    <svg class="w-10 h-10" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M32 4c-11 0-20 9-20 20 0 11 20 36 20 36s20-25 20-36c0-11-9-20-20-20z" fill="#EA580C"/>
                        <circle cx="32" cy="24" r="12" fill="#FB923C"/>
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