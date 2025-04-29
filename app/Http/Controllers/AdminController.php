<?php

namespace App\Http\Controllers;

use App\Models\Command;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard with statistics and data
     */
    public function dashboard()
    {
        // Get counts for statistics cards
        $totalUsers = User::where('role', 'client')->count();
        $totalDrivers = User::where('role', 'livreur')->count();
        $totalDeliveries = Command::count();
        $pendingDeliveries = Command::where('status', 'pending')->count();
        $completedDeliveries = Command::where('status', 'delivered')->count();
        $reports = 0; // This would ideally come from a reports table

        // Get recent users for the user management tab
        $recentUsers = User::where('role', 'client')
            ->latest()
            ->take(10)
            ->get();

        // Get recent drivers for the driver management tab
        $recentDrivers = User::where('role', 'livreur')
            ->latest()
            ->take(10)
            ->get();

        // Get recent deliveries for the deliveries tab
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
            'reports',
            'recentUsers',
            'recentDrivers',
            'recentDeliveries'
        ));
    }

    /**
     * Display a list of all users
     */
    public function users(Request $request)
    {
        $query = User::where('role', 'client');
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->paginate(15);
        return view('admin.users', compact('users'));
    }

    /**
     * Display a list of all drivers
     */
    public function drivers(Request $request)
    {
        $query = User::where('role', 'livreur');
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        $drivers = $query->paginate(15);
        return view('admin.drivers', compact('drivers'));
    }

    /**
     * Display a list of all deliveries
     */
    public function deliveries(Request $request)
    {
        $query = Command::with(['client', 'livreur']);

        // Filter by status if provided
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Search functionality
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

    /**
     * Delete a user
     */
    public function deleteUser(User $user)
    {
        // Check if the user is not an admin
        if ($user->role === 'admin') {
            return back()->with('error', 'Cannot delete an admin user');
        }

        $user->delete();
        return back()->with('success', 'User deleted successfully');
    }

    /**
     * Delete a delivery
     */
    public function deleteDelivery(Command $command)
    {
        $command->delete();
        return back()->with('success', 'Delivery deleted successfully');
    }

    /**
     * Display details for a specific user
     */
    public function showUser(User $user)
    {
        // Check if user is a client
        if ($user->role !== 'client') {
            return redirect()->route('admin.users')->with('error', 'User not found');
        }
        
        $deliveries = $user->clientCommands()->latest()->paginate(5);
        return view('admin.user-details', compact('user', 'deliveries'));
    }

    /**
     * Display details for a specific driver
     */
    public function showDriver(User $user)
    {
        // Check if user is a driver
        if ($user->role !== 'livreur') {
            return redirect()->route('admin.drivers')->with('error', 'Driver not found');
        }
        
        $deliveries = $user->livreurCommands()->latest()->paginate(5);
        $activeDelivery = $user->livreurCommands()->whereIn('status', ['accepted', 'in_progress'])->first();
        $completedDeliveries = $user->livreurCommands()->where('status', 'delivered')->count();
        
        return view('admin.driver-details', compact('user', 'deliveries', 'activeDelivery', 'completedDeliveries'));
    }

    /**
     * Display details for a specific delivery
     */
    public function showDelivery(Command $command)
    {
        return view('admin.delivery-details', compact('command'));
    }
}