<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    /**
     * Inscrire l'étudiant à un cours.
     */
    public function store(Request $request, Course $course)
    {
        $user = Auth::user();
        
        // Vérifier si le cours est publié
        if (!$course->is_published) {
            return back()->with('error', 'Ce cours n\'est pas encore disponible.');
        }
        
        // Vérifier si déjà inscrit
        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
            
        if ($existingEnrollment) {
            return redirect()->route('student.learn', $course)
                ->with('info', 'Vous êtes déjà inscrit à ce cours.');
        }
        
        // ✅ VÉRIFICATION PAIEMENT POUR LES COURS PAYANTS
        if (!$course->is_free) {
            // Rediriger vers la page de paiement
            return redirect()->route('courses.payment', $course);
        }
        
        // ✅ Pour les cours gratuits : inscription directe
        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'progress_percentage' => 0,
            'enrolled_at' => now(),
        ]);
        
        return redirect()->route('student.learn', $course)
            ->with('success', 'Vous êtes maintenant inscrit à ce cours !');
    }
    
    /**
     * Désinscrire l'étudiant.
     */
    public function destroy(Course $course)
    {
        $user = Auth::user();
        
        Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->delete();
        
        return redirect()->route('student.my-courses')
            ->with('success', 'Vous avez été désinscrit du cours.');
    }

    /**
 * Confirmer l'inscription à un cours payant.
 */
public function confirm(Request $request, Course $course)
{
    $user = Auth::user();
    
    // Vérifier si déjà inscrit
    $exists = Enrollment::where('user_id', $user->id)
        ->where('course_id', $course->id)
        ->exists();
        
    if ($exists) {
        return redirect()->route('student.learn', $course)
            ->with('info', 'Vous êtes déjà inscrit à ce cours.');
    }
    
    // ✅ Créer l'inscription (simule le paiement)
    Enrollment::create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'progress_percentage' => 0,
        'enrolled_at' => now(),
        'price_paid' => $course->price, // ✅ Enregistrer le prix payé
        'paid_at' => now(),
    ]);
    
    return redirect()->route('student.learn', $course)
        ->with('success', '✅ Inscription confirmée ! Vous pouvez maintenant accéder au cours.');
}
}