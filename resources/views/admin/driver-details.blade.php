<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Details - Codelivery Admin</title>
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
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 mt-2 text-gray-300 {{ request()->routeIs('admin.dashboard') ? 'bg-orange-700 text-white' : 'hover:bg-gray-700' }} rounded-lg transition-colors duration-200">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 mt-2 text-gray-300 {{ request()->routeIs('admin.users*') ? 'bg-orange-700 text-white' : 'hover:bg-gray-700' }} rounded-lg transition-colors duration-200">
                    <i class="fas fa-users mr-3"></i>
                    <span>Users</span>
                </a>
                <a href="{{ route('admin.drivers') }}" class="flex items-center px-4 py-3 mt-2 text-white {{ request()->routeIs('admin.drivers*') ? 'bg-orange-700' : 'hover:bg-gray-700' }} rounded-lg transition-colors duration-200">
                    <i class="fas fa-car mr-3"></i>
                    <span>Drivers</span>
                </a>
                <a href="{{ route('admin.deliveries') }}" class="flex items-center px-4 py-3 mt-2 text-gray-300 {{ request()->routeIs('admin.deliveries*') ? 'bg-orange-700 text-white' : 'hover:bg-gray-700' }} rounded-lg transition-colors duration-200">
                    <i class="fas fa-box mr-3"></i>
                    <span>Deliveries</span>
                </a>
            </div>
            <div class="px-4 mt-8">
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors duration-200 text-left">
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
            <div class="flex items-center">
                <a href="{{ route('admin.drivers') }}" class="mr-2 p-2 bg-gray-200 hover:bg-gray-300 rounded-full">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                <h1 class="text-3xl font-bold text-orange-900">Driver Details</h1>
            </div>
            <div class="flex items-center">
                <div class="h-8 w-8 rounded-full bg-orange-500 flex items-center justify-center">
                    <span class="font-semibold text-sm text-white">{{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}</span>
                </div>
                <span class="ml-2 text-orange-900">{{ Auth::user()->full_name }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Driver Profile Card -->
            <div class="bg-white rounded-lg shadow-md p-6 col-span-1">
                <div class="flex flex-col items-center">
                    <div class="h-24 w-24 rounded-full bg-green-100 flex items-center justify-center mb-4">
                        <span class="font-bold text-3xl text-green-600">{{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $user->full_name }}</h2>
                    <div class="flex items-center mt-2">
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Driver</span>
                        @if($activeDelivery)
                            <span class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">On Delivery</span>
                        @else
                            <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Available</span>
                        @endif
                    </div>
                </div>

                <div class="mt-6 border-t pt-4">
                    <div class="mb-4">
                        <span class="text-sm text-gray-500">Email</span>
                        <p class="font-medium">{{ $user->email }}</p>
                    </div>
                    <div class="mb-4">
                        <span class="text-sm text-gray-500">Phone</span>
                        <p class="font-medium">{{ $user->phone ?? 'Not provided' }}</p>
                    </div>
                    <div class="mb-4">
                        <span class="text-sm text-gray-500">Joined</span>
                        <p class="font-medium">{{ $user->created_at->format('d M, Y') }}</p>
                    </div>
                </div>

                <div class="mt-6 flex">
                    <button onclick="confirmDeleteUser({{ $user->id }}, '{{ $user->full_name }}')" class="w-full py-2 bg-red-500 hover:bg-red-600 text-white rounded flex items-center justify-center">
                        <i class="fas fa-trash mr-2"></i> Delete Driver
                    </button>
                </div>
            </div>

            <!-- Driver Stats and Deliveries -->
            <div class="col-span-1 md:col-span-2">
                <!-- Stats -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-orange-900 mb-4">Driver Statistics</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-orange-600">{{ $deliveries->total() }}</div>
                            <div class="text-sm text-gray-500">Total Deliveries</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $completedDeliveries }}</div>
                            <div class="text-sm text-gray-500">Completed</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $user->livreurCommands()->whereIn('status', ['accepted', 'in_progress'])->count() }}</div>
                            <div class="text-sm text-gray-500">Active</div>
                        </div>
                    </div>
                </div>

                <!-- Active Delivery -->
                @if($activeDelivery)
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-orange-900 mb-2">Current Delivery</h3>
                    <div class="border border-yellow-200 rounded-lg bg-yellow-50 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center mr-3">
                                    @if($activeDelivery->service_type == 'restaurant')
                                        <i class="fa-solid fa-utensils text-yellow-600"></i>
                                    @elseif($activeDelivery->service_type == 'pharmacy')
                                        <i class="fa-solid fa-prescription-bottle-medical text-yellow-600"></i>
                                    @elseif($activeDelivery->service_type == 'market')
                                        <i class="fa-solid fa-shopping-basket text-yellow-600"></i>
                                    @else
                                        <i class="fa-solid fa-box text-yellow-600"></i>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Order #{{ $activeDelivery->id }} - {{ ucfirst($activeDelivery->service_type) }}</h4>
                                    <p class="text-sm text-gray-600">{{ $activeDelivery->created_at->format('d M, Y - H:i') }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full text-xs font-medium">
                                {{ ucfirst($activeDelivery->status) }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-gray-600">From:</p>
                                <p class="font-medium">{{ $activeDelivery->pickup_address }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">To:</p>
                                <p class="font-medium">{{ $activeDelivery->delivery_address }}</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.deliveries.show', $activeDelivery) }}" class="mt-3 inline-flex items-center text-sm font-medium text-orange-600 hover:text-orange-900">
                            View details <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                @endif

                <!-- Recent Deliveries -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <h3 class="text-lg font-semibold text-orange-900">Delivery History</h3>
                        <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs font-medium">{{ $deliveries->total() }} deliveries</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($deliveries as $delivery)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#{{ $delivery->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 bg-orange-100 rounded-full flex items-center justify-center mr-2">
                                                @if($delivery->service_type == 'restaurant')
                                                    <i class="fa-solid fa-utensils text-orange-600 text-xs"></i>
                                                @elseif($delivery->service_type == 'pharmacy')
                                                    <i class="fa-solid fa-prescription-bottle-medical text-orange-600 text-xs"></i>
                                                @elseif($delivery->service_type == 'market')
                                                    <i class="fa-solid fa-shopping-basket text-orange-600 text-xs"></i>
                                                @else
                                                    <i class="fa-solid fa-box text-orange-600 text-xs"></i>
                                                @endif
                                            </div>
                                            <span class="text-sm text-gray-900">{{ ucfirst($delivery->service_type) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $delivery->client->full_name ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ Str::limit($delivery->delivery_address, 20) }}
                                    </td>
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $delivery->created_at->format('d M, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.deliveries.show', $delivery) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No deliveries found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="p-4 border-t">
                        {{ $deliveries->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Driver Form (Hidden) -->
    <form id="deleteUserForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- JavaScript -->
    <script>
        function confirmDeleteUser(id, name) {
            if (confirm("Are you sure you want to delete driver: " + name + "? This action cannot be undone.")) {
                const form = document.getElementById('deleteUserForm');
                form.action = "{{ route('admin.users.delete', ':id') }}".replace(':id', id);
                form.submit();
            }
        }
    </script>
</body>
</html>