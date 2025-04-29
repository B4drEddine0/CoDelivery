<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Codelivery</title>
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
                <span class="text-xs text-gray-400 uppercase tracking-wider">Main</span>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 mt-2 text-white {{ request()->routeIs('admin.dashboard') ? 'bg-orange-700' : 'hover:bg-gray-700' }} rounded-lg transition-colors duration-200">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 mt-2 text-gray-300 {{ request()->routeIs('admin.users') ? 'bg-orange-700 text-white' : 'hover:bg-gray-700' }} rounded-lg transition-colors duration-200">
                    <i class="fas fa-users mr-3"></i>
                    <span>Users</span>
                </a>
                <a href="{{ route('admin.drivers') }}" class="flex items-center px-4 py-3 mt-2 text-gray-300 {{ request()->routeIs('admin.drivers') ? 'bg-orange-700 text-white' : 'hover:bg-gray-700' }} rounded-lg transition-colors duration-200">
                    <i class="fas fa-car mr-3"></i>
                    <span>Drivers</span>
                </a>
                <a href="{{ route('admin.deliveries') }}" class="flex items-center px-4 py-3 mt-2 text-gray-300 {{ request()->routeIs('admin.deliveries') ? 'bg-orange-700 text-white' : 'hover:bg-gray-700' }} rounded-lg transition-colors duration-200">
                    <i class="fas fa-box mr-3"></i>
                    <span>Deliveries</span>
                </a>
            </div>
            <div class="px-4 mt-8">
                <span class="text-xs text-gray-400 uppercase tracking-wider">Settings</span>
                <a href="#" class="flex items-center px-4 py-3 mt-2 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors duration-200">
                    <i class="fas fa-cog mr-3"></i>
                    <span>Settings</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-3 mt-2 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors duration-200 text-left">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        <span>Logout</span>
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
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-orange-900">Admin Dashboard</h1>
            <div class="flex items-center">
                <div class="relative">
                    <button class="flex items-center text-orange-800 focus:outline-none mr-4">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute top-0 right-0 h-4 w-4 bg-orange-500 rounded-full text-xs flex items-center justify-center text-white">3</span>
                    </button>
                </div>
                <div class="flex items-center">
                    <div class="h-8 w-8 rounded-full bg-orange-500 flex items-center justify-center">
                        <span class="font-semibold text-sm text-white">{{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}</span>
                    </div>
                    <span class="ml-2 text-orange-900">{{ Auth::user()->full_name }}</span>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center transform transition-transform duration-300 hover:-translate-y-1 hover:shadow-orange-200">
                <div class="rounded-full bg-orange-100 p-3 mr-4">
                    <i class="fas fa-users text-orange-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-orange-900">{{ $totalUsers }}</h3>
                    <p class="text-orange-700">Total Users</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center transform transition-transform duration-300 hover:-translate-y-1">
                <div class="rounded-full bg-green-100 p-3 mr-4">
                    <i class="fas fa-car text-green-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $totalDrivers }}</h3>
                    <p class="text-gray-600">Total Drivers</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center transform transition-transform duration-300 hover:-translate-y-1">
                <div class="rounded-full bg-purple-100 p-3 mr-4">
                    <i class="fas fa-box text-purple-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $totalDeliveries }}</h3>
                    <p class="text-gray-600">Total Deliveries</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center transform transition-transform duration-300 hover:-translate-y-1">
                <div class="rounded-full bg-yellow-100 p-3 mr-4">
                    <i class="fas fa-clock text-yellow-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $pendingDeliveries }}</h3>
                    <p class="text-gray-600">Pending Deliveries</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center transform transition-transform duration-300 hover:-translate-y-1">
                <div class="rounded-full bg-green-100 p-3 mr-4">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $completedDeliveries }}</h3>
                    <p class="text-gray-600">Completed Deliveries</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center transform transition-transform duration-300 hover:-translate-y-1">
                <div class="rounded-full bg-red-100 p-3 mr-4">
                    <i class="fas fa-flag text-red-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $reports }}</h3>
                    <p class="text-gray-600">Reports</p>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-6">
            <div class="border-b border-orange-200">
                <nav class="flex -mb-px">
                    <button onclick="openTab(event, 'users')" class="tab-btn active whitespace-nowrap py-4 px-6 border-b-2 border-orange-500 font-medium text-sm text-orange-600">
                        Manage Users
                    </button>
                    <button onclick="openTab(event, 'drivers')" class="tab-btn whitespace-nowrap py-4 px-6 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-orange-700 hover:border-orange-300">
                        Manage Drivers
                    </button>
                    <button onclick="openTab(event, 'deliveries')" class="tab-btn whitespace-nowrap py-4 px-6 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Deliveries
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab Contents -->
        <div id="users" class="tab-content active">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold text-orange-800">User Management</h2>
                    <a href="{{ route('admin.users') }}" class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined Date</th>
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
                                    <button class="text-red-600 hover:text-red-900" onclick="confirmDeleteUser({{ $user->id }}, '{{ $user->full_name }}')"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No users found</td>
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
                    <h2 class="text-xl font-semibold text-gray-800">Driver Management</h2>
                    <a href="{{ route('admin.drivers') }}" class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
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
                                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                                <span class="font-semibold text-sm text-green-600">{{ substr($driver->first_name, 0, 1) }}{{ substr($driver->last_name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $driver->full_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $driver->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $driver->phone ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $hasActiveDelivery = $driver->livreurCommands()->whereIn('status', ['accepted', 'in_progress'])->exists();
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $hasActiveDelivery ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $hasActiveDelivery ? 'On Delivery' : 'Active' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-blue-600 hover:text-blue-900 mr-3" onclick="viewDriverDetails({{ $driver->id }})"><i class="fas fa-eye"></i></button>
                                    <button class="text-red-600 hover:text-red-900" onclick="confirmDeleteUser({{ $driver->id }}, '{{ $driver->full_name }}')"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No drivers found</td>
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
                    <h2 class="text-xl font-semibold text-gray-800">Delivery Management</h2>
                    <a href="{{ route('admin.deliveries') }}" class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentDeliveries as $delivery)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $delivery->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $delivery->client->full_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $delivery->livreur->full_name ?? 'Not assigned' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($delivery->service_type) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Str::limit($delivery->delivery_address, 20) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-blue-100 text-blue-800',
                                            'accepted' => 'bg-yellow-100 text-yellow-800',
                                            'in_progress' => 'bg-orange-100 text-orange-800',
                                            'delivered' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Pending',
                                            'accepted' => 'Accepted',
                                            'in_progress' => 'In Progress',
                                            'delivered' => 'Completed',
                                            'cancelled' => 'Cancelled',
                                        ];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$delivery->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$delivery->status] ?? ucfirst($delivery->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $delivery->created_at->format('d M, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-blue-600 hover:text-blue-900 mr-3" onclick="viewDeliveryDetails({{ $delivery->id }})"><i class="fas fa-eye"></i></button>
                                    <button class="text-red-600 hover:text-red-900" onclick="confirmDeleteDelivery({{ $delivery->id }})"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">No deliveries found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Delete User Form (Hidden) -->
        <form id="deleteUserForm" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

        <!-- Delete Delivery Form (Hidden) -->
        <form id="deleteDeliveryForm" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </div>

    <!-- JavaScript for tab functionality -->
    <script>
        function openTab(evt, tabName) {
            // Hide all tab content
            const tabContents = document.getElementsByClassName("tab-content");
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.add("hidden");
                tabContents[i].classList.remove("active");
            }

            // Remove active class from all tab buttons
            const tabButtons = document.getElementsByClassName("tab-btn");
            for (let i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove("active");
                tabButtons[i].classList.remove("border-orange-500");
                tabButtons[i].classList.remove("text-orange-600");
                tabButtons[i].classList.add("border-transparent");
                tabButtons[i].classList.add("text-gray-500");
            }

            // Show the selected tab content
            document.getElementById(tabName).classList.remove("hidden");
            document.getElementById(tabName).classList.add("active");

            // Add active class to the clicked button
            evt.currentTarget.classList.add("active");
            evt.currentTarget.classList.add("border-orange-500");
            evt.currentTarget.classList.add("text-orange-600");
            evt.currentTarget.classList.remove("border-transparent");
            evt.currentTarget.classList.remove("text-gray-500");
        }

        // Functions for user actions
        function viewUserDetails(id) {
            window.location.href = `/admin/users/${id}`;
        }

        function confirmDeleteUser(id, name) {
            if (confirm("Are you sure you want to delete user: " + name + "?")) {
                const form = document.getElementById('deleteUserForm');
                form.action = "/admin/users/" + id;
                form.submit();
            }
        }

        // Functions for driver actions
        function viewDriverDetails(id) {
            window.location.href = "/admin/drivers/" + id;
        }

        // Functions for delivery actions
        function viewDeliveryDetails(id) {
            window.location.href = "/admin/deliveries/" + id;
        }

        function confirmDeleteDelivery(id) {
            if (confirm("Are you sure you want to delete delivery #" + id + "?")) {
                const form = document.getElementById('deleteDeliveryForm');
                form.action = "/admin/deliveries/" + id;
                form.submit();
            }
        }
    </script>
</body>
</html>