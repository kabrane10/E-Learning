<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    protected CourseService $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index()
    {
        $courses = Auth::user()->taughtCourses()
                    ->withCount(['lessons', 'students'])
                    ->latest()
                    ->paginate(12);
                    
        return view('instructor.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('instructor.courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'category' => 'required|string|max:100',
            'thumbnail' => 'nullable|image|max:5120',
        ]);

        $course = $this->courseService->createCourse(
            $validated,
            Auth::id(),
            $request->file('thumbnail')
        );

        return redirect()
            ->route('instructor.courses.show', $course)
            ->with('success', 'Cours créé avec succès !');
    }

    public function show(Course $course)
    {
        $course->load(['chapters.lessons']);
        return view('instructor.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        return view('instructor.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'category' => 'required|string|max:100',
            'thumbnail' => 'nullable|image|max:5120',
        ]);

        $this->courseService->updateCourse(
            $course,
            $validated,
            $request->file('thumbnail')
        );

        return redirect()
            ->route('instructor.courses.show', $course)
            ->with('success', 'Cours mis à jour !');
    }

    public function destroy(Course $course)
    {
        $this->courseService->deleteCourse($course);
        
        return redirect()
            ->route('instructor.courses.index')
            ->with('success', 'Cours supprimé.');
    }

    public function togglePublish(Course $course)
    {
        $this->courseService->togglePublish($course);
        
        return back()->with('success', 
            $course->is_published ? 'Cours publié !' : 'Cours dépublié.'
        );
    }
}