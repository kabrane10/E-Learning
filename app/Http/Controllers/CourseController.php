<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::where('is_published', true)
            ->with(['instructor', 'lessons'])
            ->withCount(['lessons', 'students']);
        
        // Filtres
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->has('level')) {
            $query->where('level', $request->level);
        }
        
        if ($request->has('q')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%');
            });
        }
        
        $courses = $query->latest()->paginate(12);
        $categories = Course::where('is_published', true)
            ->distinct()
            ->pluck('category');
        
        return view('courses.index', compact('courses', 'categories'));
    }
    
    public function show(Course $course)
    {
        if (!$course->is_published && !auth()->user()?->hasRole('admin')) {
            abort(404);
        }
        
        $course->load(['instructor', 'chapters.lessons']);
        
        $isEnrolled = false;
        if (auth()->check()) {
            $isEnrolled = $course->students()->where('user_id', auth()->id())->exists();
        }
        
        // Récupérer les cours similaires
        $relatedCourses = Course::where('is_published', true)
            ->where('category', $course->category)
            ->where('id', '!=', $course->id)
            ->withCount('lessons')
            ->limit(3)
            ->get();
        
        return view('courses.show', compact('course', 'isEnrolled', 'relatedCourses'));
    }
    
    public function search(Request $request)
    {
        return $this->index($request);
    }
}