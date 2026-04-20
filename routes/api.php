<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Conversation;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Messages non lus
Route::middleware('auth:sanctum')->get('/unread-messages-count', function () {
    $user = auth()->user();
    $count = 0;
    
    if ($user && method_exists($user, 'conversations')) {
        foreach ($user->conversations as $conversation) {
            $count += $conversation->unreadMessagesForUser($user->id);
        }
    }
    
    return response()->json(['count' => $count]);
});

// Statistiques gamification (pour widget)
Route::middleware('auth:sanctum')->get('/gamification-stats', function () {
    $user = auth()->user();
    
    return response()->json([
        'level' => $user->current_level ?? 1,
        'points' => $user->total_points ?? 0,
        'streak' => $user->streak_days ?? 0,
        'badges_count' => $user->badges()->whereNotNull('earned_at')->count(),
        'rank' => \App\Models\User::where('total_points', '>', $user->total_points ?? 0)->count() + 1,
    ]);
});