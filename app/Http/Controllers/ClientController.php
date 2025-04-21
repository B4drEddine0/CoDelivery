<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Display the client dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get 3 most recent commands
        $recentCommands = $user->clientCommands()
            ->latest()
            ->take(3)
            ->get();
        
        // Get 1 command in progress (status is 'accepted' or 'in_delivery')
        $ongoingCommand = $user->clientCommands()
            ->whereIn('status', ['accepted', 'in_delivery'])
            ->latest()
            ->first();
        
        return view('client.dashboard', [
            'recentCommands' => $recentCommands,
            'ongoingCommand' => $ongoingCommand
        ]);
    }
}
