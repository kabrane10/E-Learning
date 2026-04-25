<?php

use App\Http\Controllers\Api\Instructor\AnalyticsController;
use App\Http\Controllers\Api\Instructor\WithdrawController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Conversation;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Route pour récupérer l'utilisateur connecté (nécessaire pour Sanctum)
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

// Routes Instructor (protégées par Sanctum)
Route::middleware(['auth:sanctum', 'role:instructor,admin'])->prefix('instructor')->name('api.instructor.')->group(function () {
    
    // Analytics des cours
    Route::get('/courses/{course}/analytics', [CourseController::class, 'analytics'])->name('courses.analytics');
    
    // Retraits (Withdraw)
    Route::get('/payment-settings', [WithdrawController::class, 'settings'])->name('payment-settings');
    Route::get('/balance', [WithdrawController::class, 'balance'])->name('balance');
    Route::get('/withdraw-history', [WithdrawController::class, 'history'])->name('withdraw-history');
    Route::post('/withdraw', [WithdrawController::class, 'store'])->name('withdraw');
    
});

// Transactions
Route::get('/instructor/transactions', [App\Http\Controllers\Api\Instructor\WithdrawController::class, 'transactions'])
    ->name('api.instructor.transactions');
    

    Route::middleware(['auth:sanctum'])->prefix('instructor/analytics')->name('api.instructor.analytics.')->group(function () {
        Route::get('/engagement', [AnalyticsController::class, 'engagement'])->name('engagement');
    });