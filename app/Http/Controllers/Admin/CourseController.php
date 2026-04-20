<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::with(['instructor'])
            ->withCount(['lessons', 'students'])
            ->when($request->search, function($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%');
            })
            ->when($request->category, function($query) use ($request) {
                $query->where('category', $request->category);
            })
            ->when($request->status, function($query) use ($request) {
                if ($request->status === 'published') {
                    $query->where('is_published', true);
                } elseif ($request->status === 'draft') {
                    $query->where('is_published', false);
                }
            })
            ->latest()
            ->paginate(12);
            
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        // Logique de création
        return redirect()->route('admin.courses.index')->with('success', 'Cours créé');
    }

    public function show(Course $course)
    {
        $course->load(['instructor', 'chapters.lessons', 'students']);
        return view('admin.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        // Logique de mise à jour
        return redirect()->route('admin.courses.index')->with('success', 'Cours mis à jour');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Cours supprimé');
    }
    
    public function togglePublish(Course $course)
    {
        $course->update(['is_published' => !$course->is_published]);
        return back()->with('success', 'Statut modifié');
    }
    
    public function bulkAction(Request $request)
    {
        return back()->with('success', 'Action effectuée');
    }
    
    public function approve(Course $course)
    {
        $course->update(['is_published' => true]);
        return back()->with('success', 'Cours approuvé');
    }
    
    public function reject(Course $course)
    {
        // Logique de rejet
        return back()->with('success', 'Cours rejeté');
    }
}