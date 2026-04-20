<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Achievement;
use App\Models\Level;
use App\Models\User;
use Illuminate\Http\Request;

class GamificationController extends Controller
{
    public function index()
    {
        $stats = [
            'total_points' => User::sum('total_points'),
            'average_level' => round(User::avg('current_level'), 1),
            'badges_earned' => \App\Models\UserBadge::whereNotNull('earned_at')->count(),
            'achievements_completed' => \App\Models\UserAchievement::whereNotNull('completed_at')->count(),
        ];
        
        $topUsers = User::orderBy('total_points', 'desc')->limit(10)->get();
        
        return view('admin.gamification.index', compact('stats', 'topUsers'));
    }
    
    public function badges()
    {
        $badges = Badge::withCount('users')->orderBy('category')->get();
        return view('admin.gamification.badges', compact('badges'));
    }
    
    public function achievements()
    {
        $achievements = Achievement::withCount('users')->orderBy('tier')->get();
        return view('admin.gamification.achievements', compact('achievements'));
    }
    
    public function levels()
    {
        $levels = Level::orderBy('level_number')->get();
        return view('admin.gamification.levels', compact('levels'));
    }
    
    public function storeBadge(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'required|string',
            'color' => 'required|string',
            'category' => 'required|string',
            'points_reward' => 'required|integer|min:0',
            'criteria_type' => 'required|string',
            'criteria_count' => 'required|integer|min:1',
        ]);
        
        Badge::create([
            'name' => $validated['name'],
            'slug' => \Illuminate\Support\Str::slug($validated['name']),
            'description' => $validated['description'],
            'icon' => $validated['icon'],
            'color' => $validated['color'],
            'category' => $validated['category'],
            'points_reward' => $validated['points_reward'],
            'criteria' => [
                'type' => $validated['criteria_type'],
                'count' => $validated['criteria_count'],
            ],
        ]);
        
        return back()->with('success', 'Badge créé avec succès !');
    }
}