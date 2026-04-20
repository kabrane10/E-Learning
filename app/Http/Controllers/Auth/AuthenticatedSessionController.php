<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\GamificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    protected GamificationService $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        
        // 🎮 GAMIFICATION : Points de connexion quotidienne
        $now = now();
        $lastLogin = $user->last_login_at;
        
        $user->update(['last_login_at' => $now]);
        
        // Vérifier si c'est une nouvelle journée
        if (!$lastLogin || $lastLogin->diffInDays($now) >= 1) {
            $this->gamificationService->addPoints($user, 'daily_login');
            $this->gamificationService->updateStreak($user);
        } else {
            // Juste mettre à jour l'activité
            $this->gamificationService->updateStreak($user);
        }

        // Redirection selon le rôle
        if ($user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }
        
        if ($user->hasRole('instructor')) {
            return redirect()->intended(route('instructor.courses.index', absolute: false));
        }
        
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}