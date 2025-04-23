<?php

namespace App\Http\Controllers;

use App\Models\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LivreurController extends Controller
{
    /**
     * Display the livreur dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        $activeCommands = $user->livreurCommands()
            ->whereIn('status', ['accepted', 'in_progress'])
            ->latest()
            ->take(3)
            ->get();
        
        $recentlyCompletedCommands = $user->livreurCommands()
            ->where('status', 'delivered')
            ->latest()
            ->take(3)
            ->get();
        
        return view('livreur.dashboard', [
            'activeCommands' => $activeCommands,
            'recentlyCompletedCommands' => $recentlyCompletedCommands
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
        
        // Get available commands (pending status)
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
            'availableCommands' => $availableCommands
        ]);
    }
}
