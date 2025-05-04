<?php

namespace App\Http\Controllers;

use App\Models\Command;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::where('role', 'client')->count();
        $totalDrivers = User::where('role', 'livreur')->count();
        $totalDeliveries = Command::count();
        $pendingDeliveries = Command::where('status', 'pending')->count();
        $completedDeliveries = Command::where('status', 'delivered')->count();
        $newClientsToday = User::where('role', 'client')
            ->whereDate('created_at', today())
            ->count();
        
        $activeDrivers = User::where('role', 'livreur')
            ->whereHas('livreurCommands', function($query) {
                $query->whereIn('status', ['accepted', 'in_progress']);
            })
            ->count();
            
        $activeDriversList = User::where('role', 'livreur')
            ->whereHas('livreurCommands', function($query) {
                $query->whereIn('status', ['accepted', 'in_progress']);
            })
            ->take(10)
            ->withCount('livreurCommands as deliveries_count')
            ->get()
            ->each(function($driver) {
                $driver->average_rating = 4.5; 
            });
            
        $deliveriesToday = Command::whereDate('created_at', today())->count();        
        $monthlyRevenue = Command::whereMonth('created_at', now()->month)
            ->where('status', 'delivered')
            ->sum('price');
            
        $todayRevenue = Command::whereDate('created_at', today())
            ->where('status', 'delivered')
            ->sum('price');
        
        $newClients = User::where('role', 'client')
            ->latest()
            ->take(10)
            ->withCount('clientCommands')
            ->get();

        $recentUsers = User::where('role', 'client')
            ->latest()
            ->take(10)
            ->get();

        $recentDrivers = User::where('role', 'livreur')
            ->latest()
            ->take(10)
            ->get();

        $recentDeliveries = Command::with(['client', 'livreur'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalDrivers',
            'totalDeliveries',
            'pendingDeliveries',
            'completedDeliveries',
            'recentUsers',
            'recentDrivers',
            'recentDeliveries',
            'newClientsToday',
            'activeDrivers',
            'activeDriversList',
            'deliveriesToday',
            'monthlyRevenue',
            'todayRevenue',
            'newClients'
        ));
    }

    public function users(Request $request)
    {
        $query = User::where('role', 'client');
        
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('first_name', 'LIKE', "%{$search}%")
                ->orWhere('last_name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%");
                    }

        $users = $query->paginate(15);
        return view('admin.users', compact('users'));
    }


    public function drivers(Request $request)
    {
        $query = User::where('role', 'livreur');
        
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
             }
        
        $drivers = $query->paginate(15);
        return view('admin.drivers', compact('drivers'));
    }

   
    public function deliveries(Request $request)
    {
        $query = Command::with(['client', 'livreur']);

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('establishment_name', 'LIKE', "%{$search}%")
                  ->orWhere('title', 'LIKE', "%{$search}%")
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('livreur', function($q) use ($search) {
                      $q->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $deliveries = $query->latest()->paginate(15);
        return view('admin.deliveries', compact('deliveries'));
    }


    public function deleteUser(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Cannot delete an admin user');
        }

        $user->delete();
        return back()->with('success', 'User deleted successfully');
    }

 
    public function deleteDelivery(Command $command)
    {
        $command->delete();
        return back()->with('success', 'Delivery deleted successfully');
    }


    public function showUser(User $user)
    {
        if ($user->role !== 'client') {
            return redirect()->route('admin.users')->with('error', 'User not found');
        }
        
        $deliveries = $user->clientCommands()->latest()->paginate(5);
        return view('admin.user-details', compact('user', 'deliveries'));
    }


    public function showDriver(User $user)
    {
        if ($user->role !== 'livreur') {
            return redirect()->route('admin.drivers')->with('error', 'Driver not found');
        }
        
        $deliveries = $user->livreurCommands()->latest()->paginate(5);
        $activeDelivery = $user->livreurCommands()->whereIn('status', ['accepted', 'in_progress'])->first();
        $completedDeliveries = $user->livreurCommands()->where('status', 'delivered')->count();
        
        return view('admin.driver-details', compact('user', 'deliveries', 'activeDelivery', 'completedDeliveries'));
    }


    public function showDelivery(Command $command)
    {
        return view('admin.delivery-details', compact('command'));
    }
}