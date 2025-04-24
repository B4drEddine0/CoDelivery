<?php

use App\Models\Command;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Public channel for command tracking
Broadcast::channel('command-{commandId}', function ($user, $commandId) {
    $command = Command::find($commandId);
    if (!$command) {
        return false;
    }
    
    // Allow access if the user is the client who created the command
    // or the livreur assigned to the command
    return ($user->isClient() && $command->client_id == $user->id) || 
           ($user->isLivreur() && $command->livreur_id == $user->id);
});
