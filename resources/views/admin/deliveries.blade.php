<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livraisons - Codelivery Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-orange-800 to-orange-950 text-white transition-all duration-300 transform z-30">
        <div class="flex items-center justify-center h-16 border-b border-gray-700">
            <h2 class="text-2xl font-bold">Codelivery</h2>
        </div>
        <nav class="mt-5">
            <div class="px-4">
                <span class="text-xs text-gray-400 uppercase tracking-wider">Principal</span>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 mt-2 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors duration-200">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    <span>Tableau de Bord</span>
                </a>
                <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 mt-2 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors duration-200">
                    <i class="fas fa-users mr-3"></i>
                    <span>Utilisateurs</span>
                </a>
                <a href="{{ route('admin.drivers') }}" class="flex items-center px-4 py-3 mt-2 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors duration-200">
                    <i class="fas fa-car mr-3"></i>
                    <span>Chauffeurs</span>
                </a>
                <a href="{{ route('admin.deliveries') }}" class="flex items-center px-4 py-3 mt-2 text-white bg-orange-700 rounded-lg transition-colors duration-200">
                    <i class="fas fa-box mr-3"></i>
                    <span>Livraisons</span>
                </a>
            </div>
            <div class="px-4 mt-8">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-3 mt-2 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors duration-200 text-left">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="ml-64 p-6">
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

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-orange-900">Livraisons</h1>
            <div class="flex items-center space-x-2">
                <form action="{{ route('admin.deliveries') }}" method="GET" class="flex items-center">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." class="bg-white border border-gray-300 rounded-lg py-2 px-4 pl-10 w-64 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    <button type="submit" class="ml-2 bg-orange-600 text-white py-2 px-4 rounded-lg hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-opacity-50">
                        Rechercher
                    </button>
                </form>
                <div class="ml-2">
                    <select name="status" onchange="location = this.value;" class="bg-white border border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="{{ route('admin.deliveries', ['status' => '']) }}" {{ !request('status') ? 'selected' : '' }}>Tous les Statuts</option>
                        <option value="{{ route('admin.deliveries', ['status' => 'pending']) }}" {{ request('status') == 'pending' ? 'selected' : '' }}>En Attente</option>
                        <option value="{{ route('admin.deliveries', ['status' => 'assigned']) }}" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assignée</option>
                        <option value="{{ route('admin.deliveries', ['status' => 'in_progress']) }}" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En Cours</option>
                        <option value="{{ route('admin.deliveries', ['status' => 'completed']) }}" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminée</option>
                        <option value="{{ route('admin.deliveries', ['status' => 'cancelled']) }}" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chauffeur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom de l'établissement</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Créée le</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($deliveries as $delivery)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <a href="{{ route('admin.deliveries.show', $delivery->id) }}" class="text-orange-600 hover:text-orange-900">
                                        #{{ $delivery->id }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <a href="{{ route('admin.users.show', ['user' => $delivery->client_id]) }}" class="text-sm font-medium text-gray-900 hover:text-orange-600">
                                                {{ $delivery->client ? $delivery->client->full_name : 'Inconnu' }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($delivery->livreur)
                                        <div class="flex items-center">
                                            <div>
                                                <a href="{{ route('admin.drivers.show', ['user' => $delivery->livreur_id]) }}" class="text-sm font-medium text-gray-900 hover:text-orange-600">
                                                    {{ $delivery->livreur->full_name }}
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">Non assigné</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $delivery->establishment_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $delivery->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClass = '';
                                        $statusLabel = '';
                                        switch($delivery->status) {
                                            case 'pending':
                                                $statusClass = 'bg-yellow-100 text-yellow-800';
                                                $statusLabel = 'En attente';
                                                break;
                                            case 'assigned':
                                                $statusClass = 'bg-blue-100 text-blue-800';
                                                $statusLabel = 'Assignée';
                                                break;
                                            case 'in_progress':
                                                $statusClass = 'bg-purple-100 text-purple-800';
                                                $statusLabel = 'En cours';
                                                break;
                                            case 'completed':
                                                $statusClass = 'bg-green-100 text-green-800';
                                                $statusLabel = 'Terminée';
                                                break;
                                            case 'cancelled':
                                                $statusClass = 'bg-red-100 text-red-800';
                                                $statusLabel = 'Annulée';
                                                break;
                                            default:
                                                $statusClass = 'bg-gray-100 text-gray-800';
                                                $statusLabel = ucfirst(str_replace('_', ' ', $delivery->status));
                                        }
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $delivery->created_at->format('d M, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <a href="{{ route('admin.deliveries.show', $delivery->id) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button onclick="confirmDelete({{ $delivery->id }})" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">Aucune livraison trouvée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="p-4 border-t">
                {{ $deliveries->appends(request()->query())->links() }}
            </div>
        </div>

        <!-- Delete Delivery Form (Hidden) -->
        <form id="deleteDeliveryForm" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </div>

    <!-- JavaScript -->
    <script>
        function confirmDelete(id) {
            if (confirm("Êtes-vous sûr de vouloir supprimer cette livraison ?")) {
                const form = document.getElementById('deleteDeliveryForm');
                form.action = "{{ route('admin.deliveries.delete', ':command') }}".replace(':command', id);
                form.submit();
            }
        }
    </script>
</body>
</html>