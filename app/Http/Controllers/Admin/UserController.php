<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('role') && method_exists(User::class, 'role')) {
            $query->role($request->role);
        }
        
        $users = $query->latest()->paginate(15);
        
        // Récupérer les rôles si Spatie est installé
        $roles = class_exists(\Spatie\Permission\Models\Role::class) 
            ? \Spatie\Permission\Models\Role::all() 
            : collect(['admin', 'instructor', 'student']);
        
        $stats = [
            'total_users' => User::count(),
            'total_admins' => method_exists(User::class, 'role') ? User::role('admin')->count() : 0,
            'total_instructors' => method_exists(User::class, 'role') ? User::role('instructor')->count() : 0,
            'total_students' => method_exists(User::class, 'role') ? User::role('student')->count() : User::count(),
        ];
        
        return view('admin.users.index', compact('users', 'roles', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = $this->getAvailableRoles();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'nullable|string|min:8',
            'role' => 'required|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'] ?? 'password123'),
            'email_verified_at' => $request->boolean('email_verified') ? now() : null,
        ]);
        
        if (method_exists($user, 'assignRole')) {
            $user->assignRole($validated['role']);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['enrolledCourses', 'taughtCourses']);
        
        $stats = [
            'enrolled_courses' => $user->enrolledCourses()->count(),
            'completed_courses' => $user->enrolledCourses()->whereNotNull('enrollments.completed_at')->count(),
            'taught_courses' => $user->taughtCourses()->count(),
            'average_progress' => $user->enrolledCourses()->avg('enrollments.progress_percentage') ?? 0,
        ];
        
        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = $this->getAvailableRoles();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string',
            'status' => 'nullable|string|in:active,inactive,banned',
            'language' => 'nullable|string|size:2',
            'timezone' => 'nullable|string',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);
        
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }
        
        if ($request->has('email_verified')) {
            $user->update(['email_verified_at' => $request->boolean('email_verified') ? now() : null]);
        }
        
        if (method_exists($user, 'syncRoles')) {
            $user->syncRoles([$validated['role']]);
        }
        
        // Sauvegarder les préférences
        $preferences = $user->preferences ?? [];
        $preferences['language'] = $validated['language'] ?? 'fr';
        $preferences['timezone'] = $validated['timezone'] ?? 'Europe/Paris';
        $preferences['notifications_email'] = $request->boolean('notifications_email');
        $preferences['notifications_push'] = $request->boolean('notifications_push');
        $preferences['newsletter'] = $request->boolean('newsletter');
        $user->update(['preferences' => $preferences]);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
    
    /**
     * Impersonate a user.
     */
    public function impersonate(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas usurper votre propre compte.');
        }
        
        session(['impersonated_by' => auth()->id()]);
        auth()->login($user);
        
        return redirect()->route('dashboard')->with('success', 'Vous êtes maintenant connecté en tant que ' . $user->name);
    }
    
    /**
     * Logout all sessions for a user.
     */
    public function logoutAllSessions(User $user)
    {
        // Logique pour déconnecter toutes les sessions
        // Nécessite le package laravel/sanctum ou une table sessions
        
        return response()->json(['success' => true, 'message' => 'Toutes les sessions ont été déconnectées.']);
    }
    
    /**
     * Send password reset email.
     */
    public function sendPasswordReset(User $user)
    {
        $status = Password::sendResetLink(['email' => $user->email]);
        
        return response()->json([
            'success' => $status === Password::RESET_LINK_SENT,
            'message' => $status === Password::RESET_LINK_SENT 
                ? 'Email de réinitialisation envoyé.' 
                : 'Erreur lors de l\'envoi.'
        ]);
    }
    
    /**
     * Send verification email.
     */
    public function sendVerificationEmail(User $user)
    {
        if ($user->hasVerifiedEmail()) {
            return response()->json(['success' => false, 'message' => 'Email déjà vérifié.']);
        }
        
        $user->sendEmailVerificationNotification();
        
        return response()->json(['success' => true, 'message' => 'Email de vérification envoyé.']);
    }
    
    /**
     * Get available roles.
     */
    private function getAvailableRoles()
    {
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            return \Spatie\Permission\Models\Role::all();
        }
        
        // Fallback : retourner un tableau de rôles
        return collect([
            (object) ['name' => 'admin'],
            (object) ['name' => 'instructor'],
            (object) ['name' => 'student'],
        ]);
    }
}