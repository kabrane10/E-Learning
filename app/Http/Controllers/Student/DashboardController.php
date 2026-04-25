<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Affiche les cours de l'étudiant.
     */
    public function myCourses()
    {
        $user = Auth::user();
        
        $enrolledCourses = $user->enrolledCourses()
            ->with(['instructor', 'lessons'])
            ->withPivot('progress_percentage', 'completed_at')
            ->latest('enrollments.created_at')
            ->paginate(12);
            
        // Statistiques
        $totalCourses = $enrolledCourses->total();
        $completedCourses = $user->enrolledCourses()
            ->whereNotNull('enrollments.completed_at')
            ->count();
            
        $totalHours = $user->enrolledCourses()
            ->join('lessons', 'courses.id', '=', 'lessons.course_id')
            ->sum('lessons.duration') / 3600;
            
        $averageProgress = $totalCourses > 0 
            ? $user->enrolledCourses()->avg('enrollments.progress_percentage') 
            : 0;
        
        // Cours recommandés
        $recommendedCourses = Course::where('is_published', true)
            ->whereNotIn('id', $user->enrolledCourses()->pluck('course_id'))
            ->with(['instructor'])
            ->withCount('lessons')
            ->inRandomOrder()
            ->limit(4)
            ->get();
        
        return view('student.dashboard', compact(
            'enrolledCourses',
            'totalCourses',
            'completedCourses',
            'totalHours',
            'averageProgress',
            'recommendedCourses'
        ));
    }
    
    /**
     * Statistiques rapides (API).
     */
    public function stats()
    {
        $user = Auth::user();
        
        return response()->json([
            'total_courses' => $user->enrolledCourses()->count(),
            'completed_courses' => $user->enrolledCourses()->whereNotNull('enrollments.completed_at')->count(),
            'total_hours' => round($user->enrolledCourses()
                ->join('lessons', 'courses.id', '=', 'lessons.course_id')
                ->sum('lessons.duration') / 3600, 1),
            'average_progress' => round($user->enrolledCourses()->avg('enrollments.progress_percentage') ?? 0),
        ]);
    }
}