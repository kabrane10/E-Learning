<?php

namespace App\Http\Controllers;
use App\Models\Level;
use App\Models\User;
use App\Models\Badge;
use App\Models\Achievement;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GamificationController extends Controller
{
    protected GamificationService $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    public function index()
    {
        $user = Auth::user();
        
        // Récupérer les statistiques de base
        $stats = $this->gamificationService->getUserStats($user);
        
        // S'assurer que toutes les clés existent
        $stats = array_merge([
            'total_points' => $user->total_points ?? 0,
            'average_level' => 0,
            'current_level' => [
                'number' => $user->current_level ?? 1,
                'name' => 'Débutant',
                'icon' => '🌱',
                'color' => 'green',
            ],
            'next_level' => null,
            'streak_days' => $user->streak_days ?? 0,
            'badges' => [
                'earned' => 0,
                'total' => Badge::where('is_active', true)->count(),
                'percentage' => 0,
            ],
            'achievements' => [
                'completed' => 0,
                'total' => Achievement::where('is_active', true)->count(),
                'percentage' => 0,
            ],
            'rank' => 1,
        ], $stats ?? []);
        
        // Récupérer le niveau actuel
        $currentLevel = Level::find($user->current_level ?? 1);
        if ($currentLevel) {
            $stats['current_level'] = [
                'number' => $currentLevel->level_number,
                'name' => $currentLevel->name,
                'icon' => $currentLevel->icon ?? '📊',
                'color' => $currentLevel->color ?? 'gray',
            ];
        }
        
        // Récupérer le prochain niveau
        $nextLevel = Level::where('level_number', '>', $user->current_level ?? 1)
            ->orderBy('level_number', 'asc')
            ->first();
            
        if ($nextLevel) {
            $currentPoints = $stats['total_points'] ?? 0;
            $currentLevelPoints = $currentLevel ? $currentLevel->points_required : 0;
            
            $stats['next_level'] = [
                'number' => $nextLevel->level_number,
                'name' => $nextLevel->name,
                'points_required' => $nextLevel->points_required,
                'progress' => $nextLevel->points_required > $currentLevelPoints 
                    ? min(100, round((($currentPoints - $currentLevelPoints) / ($nextLevel->points_required - $currentLevelPoints)) * 100))
                    : 0,
            ];
        }
        
        // Calculer le niveau moyen (pour la vue admin, pas nécessaire ici)
        $stats['average_level'] = round(User::avg('current_level') ?? 1, 1);
        
        // Charger les badges avec la relation pivot
        $badges = Badge::where('is_active', true)
            ->with(['users' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->orderBy('category')
            ->orderBy('order')
            ->get()
            ->map(function ($badge) use ($user) {
                $userBadge = $badge->users->firstWhere('id', $user->id);
                
                $badge->is_earned = $userBadge && $userBadge->pivot && $userBadge->pivot->earned_at !== null;
                $badge->earned_at = $userBadge && $userBadge->pivot ? $userBadge->pivot->earned_at : null;
                
                if ($userBadge && $userBadge->pivot) {
                    $progress = $userBadge->pivot->progress;
                    
                    if (is_string($progress)) {
                        $progress = json_decode($progress, true);
                    }
                    
                    $badge->progress = $progress ?: ['percentage' => 0, 'current' => 0, 'target' => 1];
                } else {
                    $badge->progress = ['percentage' => 0, 'current' => 0, 'target' => 1];
                }
                
                return $badge;
            });
            
        // Charger les achievements avec la relation pivot
        $achievements = Achievement::where('is_active', true)
            ->with(['users' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->orderBy('tier')
            ->orderBy('order')
            ->get()
            ->map(function ($achievement) use ($user) {
                $userAchievement = $achievement->users->firstWhere('id', $user->id);
                
                $achievement->is_completed = $userAchievement && $userAchievement->pivot && $userAchievement->pivot->completed_at !== null;
                $achievement->is_claimed = $userAchievement && $userAchievement->pivot && $userAchievement->pivot->claimed_at !== null;
                
                if ($userAchievement && $userAchievement->pivot) {
                    $progress = $userAchievement->pivot->progress;
                    
                    if (is_string($progress)) {
                        $progress = json_decode($progress, true);
                    }
                    
                    $achievement->progress = $progress ?: ['percentage' => 0];
                } else {
                    $achievement->progress = ['percentage' => 0];
                }
                
                return $achievement;
            });
            
        $leaderboard = $this->gamificationService->getLeaderboard('points', 50);
        
        return view('gamification.index', compact('stats', 'badges', 'achievements', 'leaderboard'));
    }

    public function leaderboard(Request $request)
    {
        $type = $request->get('type', 'points');
        $leaderboard = $this->gamificationService->getLeaderboard($type, 100);
        
        return view('gamification.leaderboard', compact('leaderboard', 'type'));
    }

    public function claim(Achievement $achievement)
    {
        $this->gamificationService->claimAchievement(Auth::user(), $achievement);
        
        return back()->with('success', 'Récompense réclamée avec succès !');
    }

    public function badges()
    {
        $user = Auth::user();
        
        $badges = Badge::where('is_active', true)
            ->with(['users' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->orderBy('category')
            ->orderBy('order')
            ->get()
            ->map(function ($badge) use ($user) {
                $userBadge = $badge->users->firstWhere('id', $user->id);
                
                $badge->is_earned = $userBadge && $userBadge->pivot && $userBadge->pivot->earned_at !== null;
                $badge->earned_at = $userBadge && $userBadge->pivot ? $userBadge->pivot->earned_at : null;
                
                if ($userBadge && $userBadge->pivot) {
                    $progress = $userBadge->pivot->progress;
                    
                    if (is_string($progress)) {
                        $progress = json_decode($progress, true);
                    }
                    
                    $badge->progress = $progress ?: ['percentage' => 0, 'current' => 0, 'target' => 1];
                } else {
                    $badge->progress = ['percentage' => 0, 'current' => 0, 'target' => 1];
                }
                
                return $badge;
            });
        
        return view('gamification.badges', compact('badges'));
    }
}