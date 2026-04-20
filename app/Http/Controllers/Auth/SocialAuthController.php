<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Liste des providers supportés
     */
    private const SUPPORTED_PROVIDERS = ['google', 'github', 'facebook', 'linkedin'];
    
    /**
     * Redirige vers le provider OAuth
     */
    public function redirect(string $provider)
    {
        $this->validateProvider($provider);
        
        return Socialite::driver($provider)->redirect();
    }
    
    /**
     * Valide que le provider est supporté
     */
    private function validateProvider(string $provider): void
    {
        if (!in_array($provider, self::SUPPORTED_PROVIDERS)) {
            abort(404, 'Provider non supporté');
        }
    }
    
    /**
     * Crée un nouvel utilisateur à partir des données sociales
     */
    private function createUser(string $provider, $socialUser): User
    {
        $name = $socialUser->getName() ?? $socialUser->getNickname() ?? explode('@', $socialUser->getEmail())[0];
        
        $user = User::create([
            'name' => $name,
            'email' => $socialUser->getEmail(),
            'password' => Hash::make(Str::random(32)),
            'email_verified_at' => now(),
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'avatar' => $socialUser->getAvatar(),
        ]);
        
        // Assigner le rôle étudiant par défaut
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('student');
        }
        
        return $user;
    }
    
    /**
     * Met à jour les informations sociales d'un utilisateur existant
     */
    private function updateSocialInfo(User $user, string $provider, $socialUser): void
    {
        $updated = false;
        
        if (!$user->provider) {
            $user->provider = $provider;
            $updated = true;
        }
        
        if (!$user->provider_id) {
            $user->provider_id = $socialUser->getId();
            $updated = true;
        }
        
        if (!$user->avatar && $socialUser->getAvatar()) {
            $user->avatar = $socialUser->getAvatar();
            $updated = true;
        }
        
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
            $updated = true;
        }
        
        if ($updated) {
            $user->save();
        }
    }

    /**
     * Handle callback from provider.
     */
    public function callback(string $provider)
    {
        $this->validateProvider($provider);
        
        try {
            $providerUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Une erreur est survenue lors de la connexion avec ' . ucfirst($provider));
        }

        // Vérifier que l'email est fourni
        if (!$providerUser->getEmail()) {
            return redirect()->route('login')
                ->with('error', 'Aucune adresse email n\'a été fournie par ' . ucfirst($provider));
        }

        // Trouver ou créer l'utilisateur
        $user = User::findOrCreateFromSocial($provider, $providerUser);

        // Connecter l'utilisateur
        Auth::login($user, true);

        // Redirection selon le rôle
        return $this->redirectAfterLogin($user);
    }

    /**
     * Handle linking social account to existing user.
     */
    public function link(string $provider)
    {
        $this->validateProvider($provider);
        
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Vous devez être connecté pour lier un compte.');
        }

        return Socialite::driver($provider)
            ->redirectUrl(route('social.callback.link', $provider))
            ->redirect();
    }

    /**
     * Handle callback for linking social account.
     */
    public function linkCallback(string $provider)
    {
        $this->validateProvider($provider);
        
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $providerUser = Socialite::driver($provider)
                ->redirectUrl(route('social.callback.link', $provider))
                ->user();
        } catch (\Exception $e) {
            return redirect()->route('profile.edit')
                ->with('error', 'Erreur lors de la liaison du compte ' . ucfirst($provider));
        }

        // Vérifier si ce compte social n'est pas déjà lié à un autre utilisateur
        $existingUser = User::where('provider', $provider)
            ->where('provider_id', $providerUser->getId())
            ->first();

        if ($existingUser && $existingUser->id !== Auth::id()) {
            return redirect()->route('profile.edit')
                ->with('error', 'Ce compte ' . ucfirst($provider) . ' est déjà lié à un autre utilisateur.');
        }

        // Lier le compte social
        Auth::user()->update([
            'provider' => $provider,
            'provider_id' => $providerUser->getId(),
            'avatar' => $providerUser->getAvatar() ?: Auth::user()->avatar,
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Compte ' . ucfirst($provider) . ' lié avec succès !');
    }

    /**
     * Handle unlinking social account.
     */
    public function unlink(string $provider)
    {
        $this->validateProvider($provider);
        
        $user = Auth::user();
        
        // Vérifier que l'utilisateur a un mot de passe défini
        if (!$user->password || Hash::check('', $user->password)) {
            return redirect()->route('profile.edit')
                ->with('error', 'Vous devez définir un mot de passe avant de dissocier votre compte ' . ucfirst($provider));
        }

        $user->update([
            'provider' => null,
            'provider_id' => null,
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Compte ' . ucfirst($provider) . ' dissocié avec succès !');
    }

    /**
     * Redirect user after login based on role.
     */
    private function redirectAfterLogin(User $user)
    {
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        
        if ($user->hasRole('instructor')) {
            return redirect()->route('instructor.courses.index');
        }
        
        return redirect()->route('dashboard');
    }
}
