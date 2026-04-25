<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{

     /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('instructor.profile.edit', compact('user'));
    }

    

    /**
     * Update the profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        \Log::info('Profile update - Données reçues:', $request->all());
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'title' => 'nullable|string|max:255',
                'bio' => 'nullable|string|max:1000',
                'website' => 'nullable|url|max:255',
                'twitter' => 'nullable|string|max:255',
                'linkedin' => 'nullable|string|max:255',
                'youtube' => 'nullable|string|max:255',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', 
            ]);
            
            // Mettre à jour les informations de base
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'title' => $validated['title'] ?? $user->title,
                'bio' => $validated['bio'] ?? $user->bio,
                'website' => $validated['website'] ?? $user->website,
                'twitter' => $validated['twitter'] ?? $user->twitter,
                'linkedin' => $validated['linkedin'] ?? $user->linkedin,
                'youtube' => $validated['youtube'] ?? $user->youtube,
            ]);
            
            // ✅ Gestion de l'avatar
            if ($request->hasFile('avatar')) {
                // Supprimer l'ancien avatar
                $user->clearMediaCollection('avatar');
                
                // Ajouter le nouveau
                $user->addMedia($request->file('avatar'))
                     ->toMediaCollection('avatar');
                
                \Log::info('Avatar updated for user: ' . $user->id);
            }
            
            // ✅ Si l'utilisateur a coché "Supprimer l'avatar"
            if ($request->has('remove_avatar') && $request->input('remove_avatar') === '1') {
                $user->clearMediaCollection('avatar');
                \Log::info('Avatar removed for user: ' . $user->id);
            }
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profil mis à jour avec succès !',
                    //'avatar_url' => $user->avatar, // ✅ Retourne la nouvelle URL
                    'avatar_url' => $user->getFirstMediaUrl('avatar') ? $user->getFirstMediaUrl('avatar') . '?t=' . time() : null,
                ]);
            }
            
            return redirect()->route('instructor.profile.edit')
                ->with('success', 'Profil mis à jour avec succès !');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error:', $e->errors());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $e->errors()
                ], 422);
            }
            
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Erreur mise à jour profil: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur serveur : ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour.');
        }
    }

       /**
     * ✅ Update password separately.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        Auth::user()->update([
            'password' => Hash::make($validated['new_password']),
        ]);
        
        Log::info('Password updated for user: ' . Auth::id());
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Mot de passe modifié avec succès !'
            ]);
        }
        
        return back()->with('success', 'Mot de passe modifié avec succès !');
    }
}