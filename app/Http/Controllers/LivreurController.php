<?php

namespace App\Http\Controllers;

use App\Models\Command;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LivreurController extends Controller
{
    /**
     * Store the livreur's location in the database
     */
    public function storeLocation(Request $request)
    {
        $user = Auth::user();
        
        // Validate the request
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'city' => 'required|string',
        ]);
        
        // Store the location in the user's metadata
        $user->update([
            'metadata' => array_merge($user->metadata ?? [], [
                'last_location' => [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'city' => $request->city,
                    'updated_at' => now()->toIso8601String()
                ]
            ])
        ]);
        
        // If livreur has active commands, update their location tracking too
        $activeCommands = $user->livreurCommands()
            ->whereIn('status', ['accepted', 'in_progress'])
            ->get();
            
        foreach ($activeCommands as $command) {
            if ($command->locationTracking) {
                $command->locationTracking->update([
                    'livreur_latitude' => $request->latitude,
                    'livreur_longitude' => $request->longitude,
                    'location_updated_at' => now()
                ]);
            }
        }
        
        return response()->json(['success' => true]);
    }
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
}
