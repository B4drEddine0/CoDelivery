<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Models\Command;

class ClientController extends Controller
{
   
    public function dashboard()
    {
        $user = Auth::user();
        
        $recentCommands = $user->clientCommands()
            ->latest()
            ->take(5)
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
            $query->where('title', 'like', $search)
            ->orWhere('establishment_name', 'like', $search)
            ->orWhere('delivery_address', 'like', $search);
        }
        
        $commands = $query->paginate(10)->withQueryString();
        
        return view('client.commands', [
            'commands' => $commands
        ]);
    }

   
    public function getLivreurContact(Command $command)
    {
        if (Auth::id() !== $command->client_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        if (!$command->livreur_id) {
            return response()->json(['error' => 'No delivery person assigned yet'], 404);
        }
        
        $livreur = $command->livreur;
        
        return response()->json([
            'success' => true,
            'name' => $livreur->first_name . ' ' . $livreur->last_name,
            'phone' => $livreur->phone
        ]);
    }
}
