<?php

namespace App\Http\Controllers;

use App\Models\Command;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommandController extends Controller
{
    /**
     * Display a listing of the commands.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isClient()) {
            $commands = $user->clientCommands()->latest()->get();
            return view('client.commands.index', compact('commands'));
        } elseif ($user->isLivreur()) {
            $commands = $user->livreurCommands()->latest()->get();
            $availableCommands = Command::where('status', 'pending')->latest()->get();
            return view('livreur.commands.index', compact('commands', 'availableCommands'));
        } else {
            return redirect()->route('login');
        }
    }

    /**
     * Show the form for creating a new command.
     */
    public function create()
    {
        if (!Auth::user()->isClient()) {
            return redirect()->route('dashboard')->with('error', 'Seuls les clients peuvent créer des commandes.');
        }
        
        // Return the view directly without any additional processing
        return view('client.create');
    }

    /**
     * Store a newly created command in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isClient()) {
            return redirect()->route('dashboard')->with('error', 'Seuls les clients peuvent créer des commandes.');
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_type' => 'required|string|in:restaurant,pharmacy,market,package',
            'establishment_name' => 'nullable|string|max:255',
            'pickup_address' => 'required|string|max:255',
            'delivery_address' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'priority' => 'required|string|in:low,medium,high',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $command = Command::create([
            'client_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'service_type' => $request->service_type,
            'establishment_name' => $request->establishment_name,
            'pickup_address' => $request->pickup_address,
            'delivery_address' => $request->delivery_address,
            'price' => $request->price ?? 0,
            'status' => 'pending',
            'priority' => $request->priority,
        ]);

        return redirect()->route('client.dashboard')
            ->with('success', 'Commande créée avec succès! Un livreur va bientôt la prendre en charge.');
    }

    /**
     * Display the specified command.
     */
    public function show(Command $command)
    {
        $user = Auth::user();
        
        if (($user->isClient() && $command->client_id == $user->id) || 
            ($user->isLivreur() && ($command->livreur_id == $user->id || $command->status == 'pending'))) {
            return view('commands.show', compact('command'));
        }
        
        return redirect()->route('dashboard')
            ->with('error', 'Vous n\'êtes pas autorisé à voir cette commande.');
    }

    /**
     * Accept a command (for livreurs).
     */
    public function accept(Command $command)
    {
        $user = Auth::user();
        
        if (!$user->isLivreur()) {
            return redirect()->route('dashboard')
                ->with('error', 'Seuls les livreurs peuvent accepter des commandes.');
        }
        
        if ($command->status !== 'pending') {
            return redirect()->route('dashboard')
                ->with('error', 'Cette commande a déjà été prise en charge ou n\'est plus disponible.');
        }
        
        $command->update([
            'livreur_id' => $user->id,
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
        
        return redirect()->route('livreur.dashboard')
            ->with('success', 'Commande acceptée avec succès!');
    }

    /**
     * Update the status of a command to in_progress.
     */
    public function startDelivery(Command $command)
    {
        $user = Auth::user();
        
        if (!$user->isLivreur() || $command->livreur_id != $user->id) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette commande.');
        }
        
        if ($command->status !== 'accepted') {
            return redirect()->route('dashboard')
                ->with('error', 'Cette commande ne peut pas être mise en livraison.');
        }
        
        $command->update([
            'status' => 'in_progress',
        ]);
        
        return redirect()->route('livreur.dashboard')
            ->with('success', 'Commande en cours de livraison!');
    }

    /**
     * Update the status of a command to delivered.
     */
    public function completeDelivery(Command $command)
    {
        $user = Auth::user();
        
        if (!$user->isLivreur() || $command->livreur_id != $user->id) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette commande.');
        }
        
        if ($command->status !== 'in_progress') {
            return redirect()->route('dashboard')
                ->with('error', 'Cette commande ne peut pas être marquée comme livrée.');
        }
        
        $command->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
        
        return redirect()->route('livreur.dashboard')
            ->with('success', 'Commande livrée avec succès!');
    }

    /**
     * Cancel a command.
     */
    public function cancel(Command $command)
    {
        $user = Auth::user();
        
        if (($user->isClient() && $command->client_id == $user->id) || 
            ($user->isLivreur() && $command->livreur_id == $user->id)) {
            
            if (in_array($command->status, ['delivered', 'cancelled'])) {
                return redirect()->route('dashboard')
                    ->with('error', 'Cette commande ne peut pas être annulée.');
            }
            
            $command->update([
                'status' => 'cancelled',
            ]);
            
            return redirect()->route('dashboard')
                ->with('success', 'Commande annulée avec succès!');
        }
        
        return redirect()->route('dashboard')
            ->with('error', 'Vous n\'êtes pas autorisé à annuler cette commande.');
    }
}
