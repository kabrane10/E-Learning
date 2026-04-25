<?php

namespace App\Http\Middleware;

use App\Models\Course;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckCourseAccess
{
    public function handle(Request $request, Closure $next)
    {
        $course = $request->route('course');
        
        // Si le cours est un ID, le récupérer
        if (is_numeric($course)) {
            $course = Course::findOrFail($course);
        }
        
        $user = Auth::user();
        
        // ✅ Le formateur du cours a toujours accès
        if ($user && $user->id === $course->instructor_id) {
            return $next($request);
        }
        
        // ✅ L'admin a toujours accès
        if ($user && $user->hasRole('admin')) {
            return $next($request);
        }
        
        // ✅ Vérifier si le cours est publié
        if (!$course->is_published) {
            abort(404, 'Cours non disponible.');
        }
        
        // ✅ Cours gratuit : accessible à tous les utilisateurs connectés
        if ($course->is_free) {
            if (!$user) {
                return redirect()->route('login');
            }
            return $next($request);
        }
        
        // ✅ Cours payant : vérifier l'inscription (paiement)
        if (!$user) {
            return redirect()->route('login');
        }
        
        $isEnrolled = $course->students()
            ->where('user_id', $user->id)
            ->exists();
        
        if (!$isEnrolled) {
            return redirect()->route('courses.payment', $course)
                ->with('error', 'Ce cours est payant. Veuillez vous inscrire pour y accéder.');
        }
        
        return $next($request);
    }
}