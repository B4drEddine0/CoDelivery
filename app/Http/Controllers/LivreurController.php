<?php

namespace App\Http\Controllers;

use App\Models\Command;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LivreurController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        
        $currentCommand = $user->livreurCommands()
            ->whereIn('status', ['accepted', 'in_progress'])
            ->with('client')
            ->latest()
            ->first();
        
        $recentlyCompletedCommands = $user->livreurCommands()
            ->where('status', 'delivered')
            ->latest()
            ->paginate(5);
        
        $todayEarnings = $user->livreurCommands()
            ->where('status', 'delivered')
            ->whereDate('delivered_at', today())
            ->sum('price');

        $todayDeliveries = $user->livreurCommands()
            ->where('status', 'delivered')
            ->whereDate('delivered_at', today())
            ->count();
        
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
    

    public function commands(Request $request)
    {
        $user = Auth::user();
        
        $myCommands = $user->livreurCommands()
            ->latest()
            ->get();
        
        $activeCommand = $user->livreurCommands()
            ->whereIn('status', ['accepted', 'in_progress'])
            ->latest()
            ->first();
        
        $canAcceptCommands = !$activeCommand;
        
        $query = Command::where('status', 'pending')
            ->whereNull('livreur_id')
            ->latest();


        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }
        
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
    
  
    public function historique(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->livreurCommands()
            ->with('client')
            ->where('status', 'delivered')
            ->orderBy('delivered_at', 'desc');
        
        
        $deliveries = $query->paginate(10)->withQueryString();
        
        $totalDeliveries = $user->livreurCommands()->where('status', 'delivered')->count();
        
        $totalEarnings = $user->livreurCommands()->where('status', 'delivered')->sum('price');
        
        return view('livreur.historique', [
            'deliveries' => $deliveries,
            'totalDeliveries' => $totalDeliveries,
            'totalEarnings' => $totalEarnings
                ]);
    }

    
    public function reviews()
    {
        $user = Auth::user();
        
        $reviews = DB::table('reviews')
            ->join('commands', 'reviews.command_id', '=', 'commands.id')
            ->join('users', 'reviews.user_id', '=', 'users.id')
            ->where('commands.livreur_id', $user->id)
            ->select('reviews.*', 'commands.title as command_title', 'commands.service_type', 
                     'users.first_name', 'users.last_name')
            ->orderBy('reviews.created_at', 'desc')
            ->paginate(10);
        
        $averageRating = DB::table('reviews')
            ->join('commands', 'reviews.command_id', '=', 'commands.id')
            ->where('commands.livreur_id', $user->id)
            ->avg('reviews.rating');
        
        $totalReviews = DB::table('reviews')
            ->join('commands', 'reviews.command_id', '=', 'commands.id')
            ->where('commands.livreur_id', $user->id)
            ->count();
        
        $ratingDistribution = DB::table('reviews')
            ->join('commands', 'reviews.command_id', '=', 'commands.id')
            ->where('commands.livreur_id', $user->id)
            ->select('reviews.rating', DB::raw('count(*) as count'))
            ->groupBy('reviews.rating')
            ->pluck('count', 'rating')
            ->toArray();
        
        for ($i = 1; $i <= 5; $i++) {
            if (!isset($ratingDistribution[$i])) {
                $ratingDistribution[$i] = 0;
            }
        }
        
        ksort($ratingDistribution);
        
        return view('livreur.reviews', [
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'totalReviews' => $totalReviews,
            'ratingDistribution' => $ratingDistribution
        ]);
    }
}
