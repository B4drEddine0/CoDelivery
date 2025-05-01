<?php

namespace App\Http\Controllers;

use App\Models\Command;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommandController extends Controller
{
    
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

  
    public function create()
    {
        if (!Auth::user()->isClient()) {
            return redirect()->route('dashboard')->with('error', 'Seuls les clients peuvent créer des commandes.');
        }
        
        return view('client.create');
    }

    
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
            'pickup_coordinates' => 'nullable|string',
            'delivery_coordinates' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Extract coordinates from pickup_coordinates field
        $pickup_lat = null;
        $pickup_lng = null;
        if ($request->pickup_coordinates) {
            $coordinates = explode(',', $request->pickup_coordinates);
            if (count($coordinates) == 2) {
                $pickup_lng = $coordinates[0];
                $pickup_lat = $coordinates[1];
            }
        }
        
        // Extract coordinates from delivery_coordinates field
        $delivery_lat = null;
        $delivery_lng = null;
        if ($request->delivery_coordinates) {
            $coordinates = explode(',', $request->delivery_coordinates);
            if (count($coordinates) == 2) {
                $delivery_lng = $coordinates[0]; 
                $delivery_lat = $coordinates[1];
            }
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
            'pickup_latitude' => $pickup_lat,
            'pickup_longitude' => $pickup_lng,
            'delivery_latitude' => $delivery_lat, 
            'delivery_longitude' => $delivery_lng,
        ]);

        return redirect()->route('client.dashboard')
            ->with('success', 'Commande créée avec succès! Un livreur va bientôt la prendre en charge.');
    }

    
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
        
        // Check if the livreur already has an active command
        $activeCommand = $user->livreurCommands()
            ->whereIn('status', ['accepted', 'in_progress'])
            ->first();
            
        if ($activeCommand) {
            return redirect()->route('livreur.commands')
                ->with('error', 'Vous avez déjà une commande en cours. Veuillez la terminer avant d\'en accepter une nouvelle.');
        }
        
        $command->update([
            'livreur_id' => $user->id,
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
        
        return redirect()->route('livreur.dashboard')
            ->with('success', 'Commande acceptée avec succès!');
    }

    
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

    
    public function completeDelivery(Command $command)
    {
        $user = Auth::user();
        
        if (!$user->isLivreur() || $command->livreur_id !== $user->id) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'êtes pas autorisé à effectuer cette action.');
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

    
    public function reset(Command $command)
    {
        $user = Auth::user();
        
        if (!$user->isLivreur() || $command->livreur_id !== $user->id) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'êtes pas autorisé à effectuer cette action.');
        }
        
        if ($command->status !== 'accepted' && $command->status !== 'in_progress') {
            return redirect()->route('livreur.commands')
                ->with('error', 'Seules les commandes acceptées ou en cours peuvent être réinitialisées.');
        }
        
        $command->update([
            'status' => 'pending',
            'livreur_id' => null,
            'accepted_at' => null,
            'started_at' => null,
        ]);
        
        return redirect()->route('livreur.commands')
            ->with('success', 'La commande a été réinitialisée avec succès et est à nouveau disponible.');
    }

    
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

    /**
     * Handle updates to a command, including the "Continue to iterate" action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Command  $command
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Command $command)
    {
        $user = Auth::user();
        
        // Check if the user is authorized to update this command
        if (!($user->isClient() && $command->client_id == $user->id) && 
            !($user->isLivreur() && $command->livreur_id == $user->id)) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette commande.');
        }
        
        // Handle "continue_iteration" action
        if ($request->input('action') === 'continue_iteration') {
            // Reset the command to pending state
            $command->update([
                'status' => 'pending',
                'livreur_id' => null,
                'accepted_at' => null,
                'started_at' => null,
            ]);
            
            return redirect()->route('dashboard')
                ->with('success', 'La commande a été remise en attente. Un nouveau livreur pourra la prendre en charge.');
        }
        
        // Handle other update actions as needed
        
        return redirect()->back()
            ->with('error', 'Action non reconnue.');
    }
    
    /**
     * Display the tracking view for a specific command.
     * 
     * @param Command $command
     * @return \Illuminate\Http\Response
     */
    public function trackCommand(Command $command)
    {
        $user = Auth::user();
        
        // Security check: Only the client who created the command or the assigned livreur can track it
        if ($user->id !== $command->client_id && $user->id !== $command->livreur_id) {
            return redirect()->route('dashboard')->with('error', 'Vous n\'êtes pas autorisé à suivre cette commande.');
        }
        
        return view('tracking.show', [
            'command' => $command,
            'isClient' => $user->id === $command->client_id,
            'mapboxToken' => config('services.mapbox.public_token'),
            'firebaseConfig' => [
                'apiKey' => config('services.firebase.api_key'),
                'authDomain' => config('services.firebase.auth_domain'),
                'databaseURL' => config('services.firebase.database_url'),
                'projectId' => config('services.firebase.project_id'),
                'storageBucket' => config('services.firebase.storage_bucket'),
                'messagingSenderId' => config('services.firebase.messaging_sender_id'),
                'appId' => config('services.firebase.app_id')
            ]
        ]);
    }
    
    /**
     * Update the delivery coordinates of a command.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDeliveryCoordinates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'command_id' => 'required|exists:commands,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $command = Command::findOrFail($request->command_id);
        
        // Update the command
        $command->update([
            'delivery_latitude' => $request->latitude,
            'delivery_longitude' => $request->longitude,
        ]);

        return response()->json(['success' => true]);
    }
}
