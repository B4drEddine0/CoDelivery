<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoDelivery - Détails de la commande</title>
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
                    <a href="{{ route('client.dashboard') }}" class="flex items-center space-x-2">
                        <svg class="w-10 h-10" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M32 4c-11 0-20 9-20 20 0 11 20 36 20 36s20-25 20-36c0-11-9-20-20-20z" fill="#EA580C"/>
                            <circle cx="32" cy="24" r="12" fill="#FB923C"/>
                            <rect x="24" y="18" width="16" height="12" fill="#FFFFFF"/>
                            <path d="M24 22h16M32 18v12" stroke="#EA580C" stroke-width="1.5"/>
                        </svg>
                        <span class="text-xl font-bold">CoDelivery</span>
                    </a>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('client.dashboard') }}" class="text-white hover:text-orange-300 transition-colors">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Dashboard
                    </a>
                    
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

    <main class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="javascript:history.back()" class="inline-flex items-center text-gray-600 hover:text-orange-600">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour au tableau de bord
            </a>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-2">{{ $command->title }}</h1>
                    <div class="flex items-center space-x-4">
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
                            
                            $serviceIcons = [
                                'restaurant' => '<i class="fa-solid fa-utensils text-orange-600"></i>',
                                'pharmacy' => '<i class="fa-solid fa-prescription-bottle-medical text-green-600"></i>',
                                'market' => '<i class="fa-solid fa-cart-shopping text-blue-600"></i>',
                                'package' => '<i class="fa-solid fa-box text-purple-600"></i>',
                            ];
                            
                            $bgColors = [
                                'restaurant' => 'bg-orange-100',
                                'pharmacy' => 'bg-green-100',
                                'market' => 'bg-blue-100',
                                'package' => 'bg-purple-100',
                            ];
                        @endphp
                        
                        <div class="flex items-center">
                            <div class="w-8 h-8 {{ $bgColors[$command->service_type] ?? 'bg-gray-100' }} rounded-full flex items-center justify-center mr-2">
                                {!! $serviceIcons[$command->service_type] ?? '<i class="fa-solid fa-question text-gray-600"></i>' !!}
                            </div>
                            <span class="text-gray-700">{{ ucfirst($command->service_type) }}</span>
                        </div>
                        
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$command->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $statusLabels[$command->status] ?? ucfirst($command->status) }}
                        </span>
                    </div>
                </div>
                
                <div class="mt-4 md:mt-0">
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Commande #{{ $command->id }}</p>
                        <p class="text-sm text-gray-500">{{ $command->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Informations de l'établissement</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Nom de l'établissement</p>
                            <p class="font-medium">{{ $command->establishment_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Adresse de ramassage</p>
                            <p>{{ $command->pickup_address }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Détails de la commande</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Description</p>
                            <p>{{ $command->description ?: 'Aucune description fournie' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Adresse de livraison</p>
                            <p>{{ $command->delivery_address }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Priorité</p>
                            @php
                                $priorityLabels = [
                                    'low' => 'Basse',
                                    'medium' => 'Moyenne',
                                    'high' => 'Haute'
                                ];
                                
                                $priorityColors = [
                                    'low' => 'text-blue-600',
                                    'medium' => 'text-orange-600',
                                    'high' => 'text-red-600'
                                ];
                            @endphp
                            <p class="{{ $priorityColors[$command->priority] ?? '' }} font-medium">
                                {{ $priorityLabels[$command->priority] ?? ucfirst($command->priority) }}
                            </p>
                        </div>
                    </div>
                </div>
                
                @if($command->status == 'delivered')
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Évaluation</h2>
                    
                    @if($command->review)
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $command->review->rating)
                                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endif
                                @endfor
                                <span class="ml-2 text-gray-600">{{ $command->review->rating }}/5</span>
                            </div>
                            <span class="ml-2 text-sm text-gray-500">Ajouté le {{ $command->review->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="mt-2">
                            <p class="text-sm font-medium text-gray-700">Commentaire :</p>
                            <p class="text-gray-600 text-sm mt-1 bg-gray-50 p-3 rounded-lg">{{ $command->review->comment }}</p>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <p class="text-gray-600 mb-4">Vous n'avez pas encore évalué cette commande.</p>
                        <button onclick="openReviewModal({{ $command->id }}, '{{ $command->title }}')" class="inline-flex items-center justify-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg">
                            <i class="fa-solid fa-star mr-2"></i> Évaluer cette commande
                        </button>
                    </div>
                    @endif
                </div>
                @endif
                
                @if($command->livreur_id)
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Informations du livreur</h2>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mr-4">
                            <span class="text-lg font-semibold text-gray-700">{{ substr($command->livreur->first_name, 0, 1) }}{{ substr($command->livreur->last_name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-medium">{{ $command->livreur->first_name }} {{ $command->livreur->last_name }}</p>
                            <p class="text-sm text-gray-500">{{ $command->livreur->phone }}</p>
                        </div>
                    </div>
                </div>
                @endif
                
               @if(auth()->user()->role == 'livreur' && $command->livreur_id == auth()->id())
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Actions</h2>
                    <div class="flex flex-wrap gap-3">
                        @if($command->status == 'accepted')
                        <form action="{{ route('livreur.commands.start', $command->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                                <i class="fa-solid fa-truck mr-2"></i>Démarrer la livraison
                            </button>
                        </form>
                        @endif
                        
                        @if($command->status == 'in_progress')
                        <form action="{{ route('livreur.commands.complete', $command->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors">
                                <i class="fa-solid fa-check-circle mr-2"></i>Marquer comme livrée
                            </button>
                        </form>
                        @endif
                        
                        @if(in_array($command->status, ['accepted', 'in_progress']))
                        <a href="{{ route('livreur.commands.track', $command->id) }}" class="inline-flex items-center bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors">
                            <i class="fa-solid fa-location-dot mr-2"></i>Suivre en temps réel
                        </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            
            <div>
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
                    <h2 class="text-lg font-semibold mb-4">Résumé</h2>                    
                    <div class="space-y-2 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sous-total</span>
                            <span>{{ number_format($command->price - 10, 2) }} DH</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Frais de livraison</span>
                            <span>10.00 DH</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-gray-100 font-semibold">
                            <span>Total</span>
                            <span class="text-orange-600">{{ number_format($command->price, 2) }} DH</span>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="font-medium mb-3">Statut de la commande</h3>
                        <div class="space-y-4">
                            @php
                                $statuses = ['pending', 'accepted', 'in_progress', 'delivered'];
                                $currentStatusIndex = array_search($command->status, $statuses);
                                if ($currentStatusIndex === false) $currentStatusIndex = -1;
                            @endphp
                            
                            <div class="flex items-start">
                                <div class="flex flex-col items-center mr-4">
                                    <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                    <div class="h-full w-0.5 bg-gray-300 mt-2"></div>
                                </div>
                                <div>
                                    <p class="font-medium">Commande créée</p>
                                    <p class="text-sm text-gray-500">{{ $command->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex flex-col items-center mr-4">
                                    <div class="w-8 h-8 rounded-full {{ $currentStatusIndex >= 1 ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500' }} flex items-center justify-center">
                                        <i class="fa-solid {{ $currentStatusIndex >= 1 ? 'fa-check' : 'fa-clock' }}"></i>
                                    </div>
                                    <div class="h-full w-0.5 bg-gray-300 mt-2"></div>
                                </div>
                                <div>
                                    <p class="font-medium">Commande acceptée</p>
                                    <p class="text-sm text-gray-500">{{ $command->accepted_at ? $command->accepted_at->format('d/m/Y H:i') : 'En attente' }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex flex-col items-center mr-4">
                                    <div class="w-8 h-8 rounded-full {{ $currentStatusIndex >= 2 ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500' }} flex items-center justify-center">
                                        <i class="fa-solid {{ $currentStatusIndex >= 2 ? 'fa-check' : 'fa-clock' }}"></i>
                                    </div>
                                    <div class="h-full w-0.5 bg-gray-300 mt-2"></div>
                                </div>
                                <div>
                                    <p class="font-medium">En cours de livraison</p>
                                    <p class="text-sm text-gray-500">{{ $command->in_progress_at ? $command->in_progress_at->format('d/m/Y H:i') : 'En attente' }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex flex-col items-center mr-4">
                                    <div class="w-8 h-8 rounded-full {{ $currentStatusIndex >= 3 ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500' }} flex items-center justify-center">
                                        <i class="fa-solid {{ $currentStatusIndex >= 3 ? 'fa-check' : 'fa-clock' }}"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="font-medium">Livré</p>
                                    <p class="text-sm text-gray-500">{{ $command->delivered_at ? $command->delivered_at->format('d/m/Y H:i') : 'En attente' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <div id="reviewModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg max-w-lg w-full mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900" id="reviewCommandTitle">Évaluer votre commande</h3>
                    <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-500">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
                
                <form id="reviewForm" action="{{ route('client.reviews.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="command_id" id="reviewCommandId">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Note</label>
                        <div class="flex items-center space-x-1" id="ratingStars">
                            <button type="button" onclick="setRating(1)" class="text-2xl text-gray-300 hover:text-yellow-400 focus:outline-none">★</button>
                            <button type="button" onclick="setRating(2)" class="text-2xl text-gray-300 hover:text-yellow-400 focus:outline-none">★</button>
                            <button type="button" onclick="setRating(3)" class="text-2xl text-gray-300 hover:text-yellow-400 focus:outline-none">★</button>
                            <button type="button" onclick="setRating(4)" class="text-2xl text-gray-300 hover:text-yellow-400 focus:outline-none">★</button>
                            <button type="button" onclick="setRating(5)" class="text-2xl text-gray-300 hover:text-yellow-400 focus:outline-none">★</button>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="5">
                    </div>
                    
                    <div class="mb-4">
                        <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Commentaire</label>
                        <textarea id="comment" name="comment" rows="4" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="Partagez votre expérience avec ce service..."></textarea>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="button" onclick="closeReviewModal()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg mr-2">Annuler</button>
                        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg">Soumettre</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        function contactLivreur(commandId) {
            $.ajax({
                url: `/client/commands/${commandId}/contact`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const modal = document.createElement('div');
                        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                        modal.innerHTML = `
                            <div class="bg-white rounded-lg p-6 shadow-xl max-w-md w-full">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-xl font-bold text-gray-900">Contact du livreur</h3>
                                    <button class="text-gray-500 hover:text-gray-700" onclick="this.parentElement.parentElement.parentElement.remove()">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="mb-4">
                                    <p class="text-gray-700"><span class="font-semibold">Nom:</span> ${response.name}</p>
                                    <p class="text-gray-700"><span class="font-semibold">Téléphone:</span> ${response.phone}</p>
                                </div>
                                <div class="flex justify-end">
                                    <a href="tel:${response.phone}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700">
                                        <i class="fa-solid fa-phone mr-2"></i>Appeler
                                    </a>
                                </div>
                            </div>
                        `;
                        
                        document.body.appendChild(modal);
                    } else {
                        alert('Une erreur est survenue. Veuillez réessayer.');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 404) {
                        alert('Aucun livreur n\'est encore assigné à cette commande.');
                    } else {
                        alert('Une erreur est survenue. Veuillez réessayer.');
                    }
                }
            });
        }
        
        let currentRating = 5;
        
        function openReviewModal(commandId, commandTitle) {
            document.getElementById('reviewCommandId').value = commandId;
            document.getElementById('reviewCommandTitle').textContent = 'Évaluer: ' + commandTitle;
            document.getElementById('reviewModal').classList.remove('hidden');
            setRating(5); 
        }
        
        function closeReviewModal() {
            document.getElementById('reviewModal').classList.add('hidden');
        }
        
        function setRating(rating) {
            currentRating = rating;
            document.getElementById('ratingInput').value = rating;
            
            
            const stars = document.getElementById('ratingStars').children;
            for (let i = 0; i < stars.length; i++) {
                if (i < rating) {
                    stars[i].classList.remove('text-gray-300');
                    stars[i].classList.add('text-yellow-400');
                } else {
                    stars[i].classList.remove('text-yellow-400');
                    stars[i].classList.add('text-gray-300');
                }
            }
        }
    </script>
</body>
</html>
