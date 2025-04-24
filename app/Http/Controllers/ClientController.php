<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ClientController extends Controller
{
    /**
     * Store the client's location in the database
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
        
        return response()->json(['success' => true]);
    }
   
    public function dashboard()
    {
        $user = Auth::user();
        
        $recentCommands = $user->clientCommands()
            ->latest()
            ->take(3)
            ->get();
        
        $ongoingCommand = $user->clientCommands()
            ->whereIn('status', ['accepted', 'in_progress'])
            ->latest()
            ->first();
        
        return view('client.dashboard', [
            'recentCommands' => $recentCommands,
            'ongoingCommand' => $ongoingCommand
        ]);
    }
    
 
    public function commands(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->clientCommands()->latest();
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }
        
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('establishment_name', 'like', $search)
                  ->orWhere('delivery_address', 'like', $search);
            });
        }
        
        $commands = $query->paginate(10)->withQueryString();
        
        return view('client.commands', [
            'commands' => $commands
        ]);
    }
}
