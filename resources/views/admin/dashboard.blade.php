<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Admin - Codelivery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
    <div class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-orange-800 to-orange-950 text-white transition-all duration-300 transform z-30">
        <div class="flex items-center justify-center h-16 border-b border-gray-700">
            <h2 class="text-2xl font-bold">Codelivery</h2>
        </div>
        <nav class="mt-5">
            <div class="px-4">
                <span class="text-xs text-gray-400 uppercase tracking-wider">Principal</span>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 mt-2 text-white {{ request()->routeIs('admin.dashboard') ? 'bg-orange-700' : 'hover:bg-gray-700' }} rounded-lg transition-colors duration-200">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    <span>Tableau de Bord</span>
                </a>
                <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 mt-2 text-gray-300 {{ request()->routeIs('admin.users') ? 'bg-orange-700 text-white' : 'hover:bg-gray-700' }} rounded-lg transition-colors duration-200">
                    <i class="fas fa-users mr-3"></i>
                    <span>Utilisateurs</span>
                </a>
                <a href="{{ route('admin.drivers') }}" class="flex items-center px-4 py-3 mt-2 text-gray-300 {{ request()->routeIs('admin.drivers') ? 'bg-orange-700 text-white' : 'hover:bg-gray-700' }} rounded-lg transition-colors duration-200">
                    <i class="fas fa-car mr-3"></i>
                    <span>Chauffeurs</span>
                </a>
                <a href="{{ route('admin.deliveries') }}" class="flex items-center px-4 py-3 mt-2 text-gray-300 {{ request()->routeIs('admin.deliveries') ? 'bg-orange-700 text-white' : 'hover:bg-gray-700' }} rounded-lg transition-colors duration-200">
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
            <h1 class="text-3xl font-bold text-orange-900">Tableau de Bord Admin</h1>
            <div class="flex items-center">
                <div class="flex items-center">
                    <div class="h-8 w-8 rounded-full bg-orange-500 flex items-center justify-center">
                        <span class="font-semibold text-sm text-white">{{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}</span>
                    </div>
                    <span class="ml-2 text-orange-900">{{ Auth::user()->full_name }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center transform transition-transform duration-300 hover:-translate-y-1 hover:shadow-orange-200">
                <div class="rounded-full bg-orange-100 p-3 mr-4">
                    <i class="fas fa-users text-orange-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $totalUsers }}</h3>
                    <p class="text-gray-600">Utilisateurs</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center transform transition-transform duration-300 hover:-translate-y-1">
                <div class="rounded-full bg-blue-100 p-3 mr-4">
                    <i class="fas fa-car text-blue-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $totalDrivers }}</h3>
                    <p class="text-gray-600">Chauffeurs</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center transform transition-transform duration-300 hover:-translate-y-1">
                <div class="rounded-full bg-green-100 p-3 mr-4">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $completedDeliveries }}</h3>
                    <p class="text-gray-600">Livraisons Terminées</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center transform transition-transform duration-300 hover:-translate-y-1">
                <div class="rounded-full bg-yellow-100 p-3 mr-4">
                    <i class="fas fa-clock text-yellow-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $pendingDeliveries }}</h3>
                    <p class="text-gray-600">Livraisons en Attente</p>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <div class="border-b border-orange-200">
                <nav class="flex -mb-px">
                    <button onclick="openTab(event, 'users')" class="tab-btn active whitespace-nowrap py-4 px-6 border-b-2 border-orange-500 font-medium text-sm text-orange-600">
                        Gérer les Utilisateurs
                    </button>
                    <button onclick="openTab(event, 'drivers')" class="tab-btn whitespace-nowrap py-4 px-6 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-orange-700 hover:border-orange-300">
                        Gérer les Chauffeurs
                    </button>
                    <button onclick="openTab(event, 'deliveries')" class="tab-btn whitespace-nowrap py-4 px-6 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Livraisons
                    </button>
                </nav>
            </div>
        </div>

        <div id="users" class="tab-content active">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold text-orange-800">Gestion des Utilisateurs</h2>
                    <a href="{{ route('admin.users') }}" class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors">Voir Tous</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date d'inscription</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentUsers as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                                                <span class="font-semibold text-sm text-orange-600">{{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->full_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->phone ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('d M, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-blue-600 hover:text-blue-900 mr-3" onclick="viewUserDetails({{ $user->id }})"><i class="fas fa-eye"></i></button>
                                    <button class="text-red-600 hover:text-red-900" onclick="confirmDeleteUser({{ $user->id }})"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">Aucun utilisateur trouvé</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="drivers" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold text-orange-800">Gestion des Chauffeurs</h2>
                    <a href="{{ route('admin.drivers') }}" class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors">Voir Tous</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Livraisons</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentDrivers as $driver)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $driver->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="font-semibold text-sm text-blue-600">{{ substr($driver->first_name, 0, 1) }}{{ substr($driver->last_name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $driver->full_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $driver->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $driver->phone ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $driver->driverCommands()->count() }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-blue-600 hover:text-blue-900 mr-3" onclick="viewDriverDetails({{ $driver->id }})"><i class="fas fa-eye"></i></button>
                                    <button class="text-red-600 hover:text-red-900" onclick="confirmDeleteDriver({{ $driver->id }})"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">Aucun chauffeur trouvé</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="deliveries" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold text-orange-800">Gestion des Livraisons</h2>
                    <a href="{{ route('admin.deliveries') }}" class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors">Voir Toutes</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chauffeur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentDeliveries as $delivery)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $delivery->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $delivery->client->full_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $delivery->driver->full_name ?? 'Non assigné' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($delivery->status === 'pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                                    @elseif($delivery->status === 'in_progress')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">En cours</span>
                                    @elseif($delivery->status === 'completed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Terminée</span>
                                    @elseif($delivery->status === 'cancelled')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Annulée</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $delivery->created_at->format('d M, Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-blue-600 hover:text-blue-900" onclick="viewDeliveryDetails({{ $delivery->id }})"><i class="fas fa-eye"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">Aucune livraison trouvée</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="confirmationModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-md p-6 max-w-sm w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4" id="modalTitle">Confirmer la suppression</h3>
            <p class="text-gray-600 mb-6" id="modalMessage">Êtes-vous sûr de vouloir supprimer cet élément ? Cette action ne peut pas être annulée.</p>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition-colors">Annuler</button>
                <button type="button" id="confirmButton" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors">Supprimer</button>
            </div>
        </div>
    </div>

    <script>
        function openTab(evt, tabName) {
            var i, tabContent, tabButtons;
            
            tabContent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabContent.length; i++) {
                tabContent[i].classList.add("hidden");
            }
            
            tabButtons = document.getElementsByClassName("tab-btn");
            for (i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove("active", "border-orange-500", "text-orange-600");
                tabButtons[i].classList.add("border-transparent", "text-gray-500");
            }
            
            document.getElementById(tabName).classList.remove("hidden");
            evt.currentTarget.classList.add("active", "border-orange-500", "text-orange-600");
            evt.currentTarget.classList.remove("border-transparent", "text-gray-500");
        }

        function closeModal() {
            document.getElementById('confirmationModal').classList.add('hidden');
        }

        function showModal(title, message, confirmAction) {
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalMessage').innerText = message;
            document.getElementById('confirmButton').onclick = confirmAction;
            document.getElementById('confirmationModal').classList.remove('hidden');
        }

      
        function viewUserDetails(id) {
            window.location.href = "{{ route('admin.users.show', ':id') }}".replace(':id', id);
        }

        function confirmDeleteUser(id) {
            showModal(
                "Confirmer la suppression",
                "Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action ne peut pas être annulée.",
                function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('admin.users.delete', ':id') }}".replace(':id', id);
                    form.style.display = 'none';
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    const method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'DELETE';
                    
                    form.appendChild(csrfToken);
                    form.appendChild(method);
                    document.body.appendChild(form);
                    form.submit();
                }
            );
        }

        function viewDriverDetails(id) {
            window.location.href = "{{ route('admin.drivers.show', ':id') }}".replace(':id', id);
        }

        function confirmDeleteDriver(id) {
            showModal(
                "Confirmer la suppression",
                "Êtes-vous sûr de vouloir supprimer ce chauffeur ? Cette action ne peut pas être annulée.",
                function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('admin.drivers.delete', ':id') }}".replace(':id', id);
                    form.style.display = 'none';
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    const method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'DELETE';
                    
                    form.appendChild(csrfToken);
                    form.appendChild(method);
                    document.body.appendChild(form);
                    form.submit();
                }
            );
        }

        function viewDeliveryDetails(id) {
            window.location.href = "{{ route('admin.deliveries.show', ':id') }}".replace(':id', id);
        }
    </script>
</body>
</html>