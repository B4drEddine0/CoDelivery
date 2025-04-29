<?php

namespace App\Http\Controllers;

use App\Models\Command;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LivreurController extends Controller
{
    /**
     * Display the livreur dashboard.
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        
        // Get the current active command (in progress or accepted) with client information
        $currentCommand = $user->livreurCommands()
            ->whereIn('status', ['accepted', 'in_progress'])
            ->with('client')
            ->latest()
            ->first();
        
        // Get recently completed commands with pagination
        $recentlyCompletedCommands = $user->livreurCommands()
            ->where('status', 'delivered')
            ->latest()
            ->paginate(5);
        
        // Calculate today's earnings
        $todayEarnings = $user->livreurCommands()
            ->where('status', 'delivered')
            ->whereDate('delivered_at', today())
            ->sum('price');

        // Count today's deliveries
        $todayDeliveries = $user->livreurCommands()
            ->where('status', 'delivered')
            ->whereDate('delivered_at', today())
            ->count();
        
        // Check if the livreur can accept new commands
        $canAcceptCommands = !$currentCommand || 
            ($currentCommand->status !== 'accepted' && $currentCommand->status !== 'in_progress');
        
        return view('livreur.dashboard', [
            'currentCommand' => $currentCommand,
            'recentlyCompletedCommands' => $recentlyCompletedCommands,
            'todayEarnings' => $todayEarnings,
            'todayDeliveries' => $todayDeliveries,
            'canAcceptCommands' => $canAcceptCommands
        ]);
    }
    
    /**
     * Display available commands for livreur.
     */
    public function commands(Request $request)
    {
        $user = Auth::user();
        
        // Get commands assigned to the livreur
        $myCommands = $user->livreurCommands()
            ->latest()
            ->get();
        
        // Check if the livreur has an active command (in progress or accepted)
        $activeCommand = $user->livreurCommands()
            ->whereIn('status', ['accepted', 'in_progress'])
            ->latest()
            ->first();
        
        // Check if the livreur can accept new commands
        $canAcceptCommands = !$activeCommand;
        
        // Get available commands (pending status) - always show them regardless of active command
        $query = Command::where('status', 'pending')
            ->whereNull('livreur_id')
            ->latest();
        
        // Filter by date if provided
        if ($request->filled('date')) {
            $date = $request->date;
            $query->whereDate('created_at', $date);
        }
        
        // Filter by service type if provided
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }
        
        // Filter by priority if provided
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        $availableCommands = $query->get();
        
        return view('livreur.commands', [
            'myCommands' => $myCommands,
            'availableCommands' => $availableCommands,
            'canAcceptCommands' => $canAcceptCommands,
            'activeCommand' => $activeCommand
        ]);
    }
    
    /**
     * Display delivery history for livreur.
     */
    public function historique(Request $request)
    {
        $user = Auth::user();
        
        // Base query for completed deliveries
        $query = $user->livreurCommands()
            ->with('client')
            ->where('status', 'delivered')
            ->orderBy('delivered_at', 'desc');
        
        // Apply filters if provided
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'this_week':
                    $query->whereBetween('delivered_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereBetween('delivered_at', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;
                case 'last_3_months':
                    $query->whereBetween('delivered_at', [now()->subMonths(3), now()]);
                    break;
                case 'this_year':
                    $query->whereBetween('delivered_at', [now()->startOfYear(), now()->endOfYear()]);
                    break;
            }
        }
        
        // Filter by service type
        if ($request->filled('service_type') && $request->service_type !== 'all') {
            $query->where('service_type', $request->service_type);
        }
        
        // Search
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('establishment_name', 'like', $search)
                  ->orWhere('delivery_address', 'like', $search);
            });
        }
        
        // Get the deliveries with pagination
        $deliveries = $query->paginate(10)->withQueryString();
        
        // Calculate statistics
        $totalDeliveries = $user->livreurCommands()->where('status', 'delivered')->count();
        
        // Calculate total distance (assuming we have a distance field or can compute it)
        // For demo, we'll use a random calculation based on delivery count
        $totalDistance = $totalDeliveries * rand(3, 8); // Average 3-8 km per delivery
        
        // Calculate total earnings
        $totalEarnings = $user->livreurCommands()->where('status', 'delivered')->sum('price');
        
        // Calculate average rating (assuming we have a rating system)
        // For demo, we'll use a random rating between 4 and 5
        $averageRating = round(rand(40, 50) / 10, 1);
        
        return view('livreur.historique', [
            'deliveries' => $deliveries,
            'totalDeliveries' => $totalDeliveries,
            'totalDistance' => $totalDistance,
            'totalEarnings' => $totalEarnings,
            'averageRating' => $averageRating
        ]);
    }
}
