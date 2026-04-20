<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function myCourses()
    {
        $user = Auth::user();
        
        $enrolledCourses = $user->enrolledCourses()
            ->with(['instructor', 'lessons'])
            ->withPivot('progress_percentage', 'completed_at')
            ->latest('enrollments.created_at')
            ->paginate(12);
            
        // Statistiques
        $totalCourses = $user->enrolledCourses()->count();
        $completedCourses = $user->enrolledCourses()
            ->whereNotNull('enrollments.completed_at')
            ->count();
            
        $totalHours = $user->enrolledCourses()
            ->join('lessons', 'courses.id', '=', 'lessons.course_id')
            ->sum('lessons.duration') / 60;
            
        $averageProgress = $totalCourses > 0 
            ? $user->enrolledCourses()->avg('enrollments.progress_percentage') 
            : 0;
        
        // Recommandations (cours similaires non suivis)
        $enrolledCourseIds = $user->enrolledCourses()->pluck('course_id');
        
        $recommendedCourses = Course::where('is_published', true)
            ->whereNotIn('id', $enrolledCourseIds)
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
}