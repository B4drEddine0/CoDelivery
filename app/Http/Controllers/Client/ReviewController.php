<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Command;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
   
    public function store(Request $request)
    {
        $request->validate([
            'command_id' => 'required|exists:commands,id',
            'comment' => 'required|string|min:3',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $command = Command::where('id', $request->command_id)
            ->where('client_id', Auth::id())
            ->where('status', 'delivered')
            ->firstOrFail();
        
        if ($command->review) {
            return redirect()->back()->with('error', 'Vous avez déjà évalué cette commande.');
        }

        $review = new Review([
            'user_id' => Auth::id(),
            'command_id' => $command->id,
            'comment' => $request->comment,
            'rating' => $request->rating,
        ]);

        $review->save();

        return redirect()->back()->with('success', 'Merci pour votre évaluation! Votre avis est important pour nous.');
    }
} 