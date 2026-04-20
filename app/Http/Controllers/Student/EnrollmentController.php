<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $user = Auth::user();
        
        // Vérifier si le cours est publié
        if (!$course->is_published) {
            abort(404);
        }
        
        // Vérifier si déjà inscrit
        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
            
        if ($existingEnrollment) {
            return redirect()->route('student.learn', $course)
                ->with('info', 'Vous êtes déjà inscrit à ce cours.');
        }
        
        // Créer l'inscription
        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'progress_percentage' => 0,
        ]);
        
        return redirect()->route('student.learn', $course)
            ->with('success', 'Vous êtes maintenant inscrit à ce cours !');
    }
}