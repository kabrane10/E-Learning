<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChapterController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $this->authorize('update', $course);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);
        
        $order = $course->chapters()->max('order') + 1;
        
        $chapter = $course->chapters()->create([
            'title' => $validated['title'],
            'order' => $order,
        ]);
        
        return response()->json([
            'success' => true,
            'chapter' => $chapter,
            'message' => 'Chapitre créé avec succès'
        ]);
    }
    
    public function update(Request $request, Course $course, Chapter $chapter)
    {
        $this->authorize('update', $course);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);
        
        $chapter->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Chapitre mis à jour'
        ]);
    }
    
    public function destroy(Course $course, Chapter $chapter)
    {
        $this->authorize('update', $course);
        
        $chapter->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Chapitre supprimé'
        ]);
    }
    
    public function reorder(Request $request, Course $course)
    {
        $this->authorize('update', $course);
        
        $validated = $request->validate([
            'chapters' => 'required|array',
            'chapters.*.id' => 'required|exists:chapters,id',
            'chapters.*.order' => 'required|integer',
        ]);
        
        foreach ($validated['chapters'] as $chapterData) {
            Chapter::where('id', $chapterData['id'])
                ->where('course_id', $course->id)
                ->update(['order' => $chapterData['order']]);
        }
        
        return response()->json(['success' => true]);
    }
}