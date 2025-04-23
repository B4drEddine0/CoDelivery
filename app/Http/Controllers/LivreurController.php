<?php

namespace App\Http\Controllers;

use App\Models\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LivreurController extends Controller
{
    /**
     * Display the livreur dashboard.
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        
        // Get the current active command (in progress or accepted)
        $currentCommand = $user->livreurCommands()
            ->whereIn('status', ['accepted', 'in_progress'])
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
