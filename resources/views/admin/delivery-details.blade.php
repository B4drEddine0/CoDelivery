<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Details - Codelivery Admin</title>
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
                <a href="{{ route('admin.drivers') }}" class="flex items-center px-4 py-3 mt-2 text-gray-300 {{ request()->routeIs('admin.drivers*') ? 'bg-orange-700 text-white' : 'hover:bg-gray-700' }} rounded-lg transition-colors duration-200">
                    <i class="fas fa-car mr-3"></i>
                    <span>Drivers</span>
                </a>
                <a href="{{ route('admin.deliveries') }}" class="flex items-center px-4 py-3 mt-2 text-white {{ request()->routeIs('admin.deliveries*') ? 'bg-orange-700' : 'hover:bg-gray-700' }} rounded-lg transition-colors duration-200">
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
                <a href="{{ route('admin.deliveries') }}" class="mr-2 p-2 bg-gray-200 hover:bg-gray-300 rounded-full">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                <h1 class="text-3xl font-bold text-orange-900">Delivery Details</h1>
            </div>
            <div class="flex items-center">
                <div class="h-8 w-8 rounded-full bg-orange-500 flex items-center justify-center">
                    <span class="font-semibold text-sm text-white">{{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}</span>
                </div>
                <span class="ml-2 text-orange-900">{{ Auth::user()->full_name }}</span>
            </div>
        </div>

        <!-- Delivery Status Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-14 w-14 rounded-full bg-orange-100 flex items-center justify-center mr-4">
                        @if($command->service_type == 'restaurant')
                            <i class="fa-solid fa-utensils text-orange-600 text-xl"></i>
                        @elseif($command->service_type == 'pharmacy')
                            <i class="fa-solid fa-prescription-bottle-medical text-orange-600 text-xl"></i>
                        @elseif($command->service_type == 'market')
                            <i class="fa-solid fa-shopping-basket text-orange-600 text-xl"></i>
                        @else
                            <i class="fa-solid fa-box text-orange-600 text-xl"></i>
                        @endif
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Order #{{ $command->id }}</h2>
                        <p class="text-gray-600">{{ ucfirst($command->service_type) }} Delivery</p>
                    </div>
                </div>
                
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
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ];
                @endphp
                <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $statusClasses[$command->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $statusLabels[$command->status] ?? ucfirst($command->status) }}
                </span>
            </div>

            <!-- Timeline -->
            <div class="mt-8">
                <div class="relative">
                    <div class="absolute left-0 inset-y-0 w-0.5 bg-gray-200"></div>
                    
                    <!-- Order Placed -->
                    <div class="relative flex items-start mb-6">
                        <div class="flex items-center h-6">
                            <div class="relative z-10 w-6 h-6 flex items-center justify-center bg-orange-500 rounded-full">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-medium">Order Placed</h3>
                            <time class="block text-sm text-gray-500">{{ $command->created_at->format('M d, Y - h:i A') }}</time>
                            <p class="mt-1 text-gray-600">Order created by {{ $command->client->full_name ?? 'Unknown' }}</p>
                        </div>
                    </div>
                    
                    <!-- Order Accepted -->
                    <div class="relative flex items-start mb-6">
                        <div class="flex items-center h-6">
                            <div class="relative z-10 w-6 h-6 flex items-center justify-center {{ in_array($command->status, ['accepted', 'in_progress', 'delivered']) ? 'bg-orange-500' : 'bg-gray-200' }} rounded-full">
                                @if(in_array($command->status, ['accepted', 'in_progress', 'delivered']))
                                    <i class="fas fa-check text-white text-xs"></i>
                                @endif
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-medium">Order Accepted</h3>
                            @if(in_array($command->status, ['accepted', 'in_progress', 'delivered']))
                                <time class="block text-sm text-gray-500">{{ $command->accepted_at ? $command->accepted_at->format('M d, Y - h:i A') : 'N/A' }}</time>
                                <p class="mt-1 text-gray-600">Accepted by driver {{ $command->livreur->full_name ?? 'Unknown' }}</p>
                            @else
                                <span class="text-sm text-gray-500">Waiting for driver</span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- In Progress -->
                    <div class="relative flex items-start mb-6">
                        <div class="flex items-center h-6">
                            <div class="relative z-10 w-6 h-6 flex items-center justify-center {{ in_array($command->status, ['in_progress', 'delivered']) ? 'bg-orange-500' : 'bg-gray-200' }} rounded-full">
                                @if(in_array($command->status, ['in_progress', 'delivered']))
                                    <i class="fas fa-check text-white text-xs"></i>
                                @endif
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-medium">In Progress</h3>
                            @if(in_array($command->status, ['in_progress', 'delivered']))
                                <time class="block text-sm text-gray-500">{{ $command->started_at ? $command->started_at->format('M d, Y - h:i A') : 'N/A' }}</time>
                                <p class="mt-1 text-gray-600">Driver has picked up the order</p>
                            @else
                                <span class="text-sm text-gray-500">Not started yet</span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Delivered -->
                    <div class="relative flex items-start">
                        <div class="flex items-center h-6">
                            <div class="relative z-10 w-6 h-6 flex items-center justify-center {{ $command->status == 'delivered' ? 'bg-green-500' : 'bg-gray-200' }} rounded-full">
                                @if($command->status == 'delivered')
                                    <i class="fas fa-check text-white text-xs"></i>
                                @endif
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-medium">Delivered</h3>
                            @if($command->status == 'delivered')
                                <time class="block text-sm text-gray-500">{{ $command->completed_at ? $command->completed_at->format('M d, Y - h:i A') : 'N/A' }}</time>
                                <p class="mt-1 text-gray-600">Order successfully delivered</p>
                            @else
                                <span class="text-sm text-gray-500">Not delivered yet</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Delivery Details -->
            <div class="col-span-1 md:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-orange-900 mb-4">Delivery Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <span class="text-sm text-gray-500">Service Type</span>
                                <p class="font-medium">{{ ucfirst($command->service_type) }}</p>
                            </div>
                            <div class="mb-4">
                                <span class="text-sm text-gray-500">Pickup Address</span>
                                <p class="font-medium">{{ $command->pickup_address }}</p>
                            </div>
                            <div class="mb-4">
                                <span class="text-sm text-gray-500">Delivery Address</span>
                                <p class="font-medium">{{ $command->delivery_address }}</p>
                            </div>
                            <div class="mb-4">
                                <span class="text-sm text-gray-500">Order Date</span>
                                <p class="font-medium">{{ $command->created_at->format('M d, Y - h:i A') }}</p>
                            </div>
                        </div>
                        <div>
                            <div class="mb-4">
                                <span class="text-sm text-gray-500">Order Notes</span>
                                <p class="font-medium">{{ $command->instructions ?? 'No special instructions' }}</p>
                            </div>
                            <div class="mb-4">
                                <span class="text-sm text-gray-500">Status</span>
                                <p class="font-medium">{{ $statusLabels[$command->status] ?? ucfirst($command->status) }}</p>
                            </div>
                            @if($command->price)
                            <div class="mb-4">
                                <span class="text-sm text-gray-500">Price</span>
                                <p class="font-medium">{{ $command->price }} DH</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Map -->
                @if($command->latitude && $command->longitude)
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-orange-900 mb-4">Delivery Location</h3>
                    <div id="map" class="w-full h-64 rounded-lg"></div>
                </div>
                @endif
            </div>

            <!-- Customer and Driver Info -->
            <div class="col-span-1">
                <!-- Customer -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-orange-900 mb-4">Customer</h3>
                    @if($command->client)
                    <div class="flex items-start">
                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <span class="font-semibold text-sm text-blue-600">
                                {{ substr($command->client->first_name, 0, 1) }}{{ substr($command->client->last_name, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <h4 class="font-medium">{{ $command->client->full_name }}</h4>
                            <p class="text-sm text-gray-600">{{ $command->client->email }}</p>
                            <p class="text-sm text-gray-600">{{ $command->client->phone ?? 'No phone' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.show', ['user' => $command->client->id]) }}" class="mt-4 inline-flex items-center text-sm font-medium text-orange-600 hover:text-orange-900">
                        View customer profile <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                    @else
                    <p class="text-gray-500">Customer information not available</p>
                    @endif
                </div>

                <!-- Driver -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-orange-900 mb-4">Driver</h3>
                    @if($command->livreur)
                    <div class="flex items-start">
                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                            <span class="font-semibold text-sm text-green-600">
                                {{ substr($command->livreur->first_name, 0, 1) }}{{ substr($command->livreur->last_name, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <h4 class="font-medium">{{ $command->livreur->full_name }}</h4>
                            <p class="text-sm text-gray-600">{{ $command->livreur->email }}</p>
                            <p class="text-sm text-gray-600">{{ $command->livreur->phone ?? 'No phone' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.drivers.show', $command->livreur) }}" class="mt-4 inline-flex items-center text-sm font-medium text-orange-600 hover:text-orange-900">
                        View driver profile <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                    @else
                    <p class="text-gray-500">No driver assigned yet</p>
                    @endif
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-orange-900 mb-4">Actions</h3>
                    <button onclick="confirmDeleteDelivery({{ $command->id }})" class="w-full py-2 bg-red-500 hover:bg-red-600 text-white rounded flex items-center justify-center">
                        <i class="fas fa-trash mr-2"></i> Delete Delivery
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Delivery Form (Hidden) -->
    <form id="deleteDeliveryForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- JavaScript -->
    <script>
        function confirmDeleteDelivery(id) {
            if (confirm("Are you sure you want to delete this delivery? This action cannot be undone.")) {
                const form = document.getElementById('deleteDeliveryForm');
                form.action = "{{ route('admin.deliveries.delete', ':command') }}".replace(':command', id);
                form.submit();
            }
        }
    </script>

    @if($command->latitude && $command->longitude)
    <!-- MapBox Scripts -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css' rel='stylesheet' />
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            mapboxgl.accessToken = 'pk.eyJ1IjoiY29kZWxpdmVyeSIsImEiOiJjbHRuNnNtY3YwODM2MmpvNzNhdGY4bHhyIn0.KrcuT-2e7DJFcPh1huwaDA';
            
            const map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/streets-v12',
                center: [{{ $command->longitude }}, {{ $command->latitude }}],
                zoom: 15
            });
            
            // Add marker
            new mapboxgl.Marker({ color: '#FF6B35' })
                .setLngLat([{{ $command->longitude }}, {{ $command->latitude }}])
                .addTo(map);
        });
    </script>
    @endif
</body>
</html>