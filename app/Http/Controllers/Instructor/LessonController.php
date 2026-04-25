<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    /**
    * Store a newly created lesson.
    */
   public function store(Request $request, Course $course, Chapter $chapter = null)
   {
       $this->authorize('update', $course);
       
       // ✅ LOG pour voir ce qui arrive
       Log::info('📥 Store Lesson - Toutes les données:', $request->all());
       
       // ✅ Validation avec content_type
       $validated = $request->validate([
           'title' => 'required|string|max:255',
           'content_type' => 'required|in:video,pdf,quiz,text', // ✅ Obligatoire
           'chapter_id' => 'nullable|exists:chapters,id',
           'video_file' => 'nullable|file|mimes:mp4,mov,avi,webm|max:2048000',
           'pdf_file' => 'nullable|file|mimes:pdf|max:51200',
           'content' => 'nullable|string',
           'duration' => 'nullable|integer',
           'is_free_preview' => 'nullable|boolean',
       ]);
       
       Log::info('✅ Données validées:', $validated);
       
       $order = $course->lessons()
           ->where('chapter_id', $chapter?->id)
           ->max('order') + 1;
       
       // ✅ Création avec TOUS les champs obligatoires
       $lesson = $course->lessons()->create([
           'title' => $validated['title'],
           'content_type' => $validated['content_type'], // ✅ Obligatoire
           'chapter_id' => $validated['chapter_id'] ?? $chapter?->id,
           'content' => $validated['content'] ?? null,
           'duration' => $validated['duration'] ?? null,
           'order' => $order,
           'is_free_preview' => $request->boolean('is_free_preview'),
       ]);
       
       Log::info('✅ Leçon créée:', ['id' => $lesson->id, 'content_type' => $lesson->content_type]);
       
       // Gestion des fichiers
       if ($request->hasFile('video_file')) {
           $path = $request->file('video_file')->store("courses/{$course->id}/videos", 'public');
           $lesson->update(['video_path' => $path]);
       }
       
       if ($request->hasFile('pdf_file')) {
           $path = $request->file('pdf_file')->store("courses/{$course->id}/pdfs", 'public');
           $lesson->update(['pdf_path' => $path]);
       }
       
       return response()->json([
           'success' => true,
           'lesson' => $lesson,
           'message' => 'Leçon créée avec succès'
       ], 201);
   }
   
   /**
    * Update the specified lesson.
    */
   public function update(Request $request, Course $course, Lesson $lesson)
   {
       $this->authorize('update', $course);
       
       $validated = $request->validate([
           'title' => 'required|string|max:255',
           'content_type' => 'required|in:video,pdf,quiz,text',
           'chapter_id' => 'nullable|exists:chapters,id',
           'video_file' => 'nullable|file|mimes:mp4,mov,avi,webm|max:2048000',
           'pdf_file' => 'nullable|file|mimes:pdf|max:51200',
           'content' => 'nullable|string',
           'duration' => 'nullable|integer',
           'is_free_preview' => 'nullable|boolean',
       ]);
       
       $lesson->update([
           'title' => $validated['title'],
           'content_type' => $validated['content_type'],
           'chapter_id' => $validated['chapter_id'] ?? $lesson->chapter_id,
           'content' => $validated['content'] ?? $lesson->content,
           'duration' => $validated['duration'] ?? $lesson->duration,
           'is_free_preview' => $request->boolean('is_free_preview'),
       ]);
       
       // Gestion des fichiers
       if ($request->hasFile('video_file')) {
           if ($lesson->video_path) {
               Storage::disk('public')->delete($lesson->video_path);
           }
           $path = $request->file('video_file')->store("courses/{$course->id}/videos", 'public');
           $lesson->update(['video_path' => $path]);
       }
       
       if ($request->hasFile('pdf_file')) {
           if ($lesson->pdf_path) {
               Storage::disk('public')->delete($lesson->pdf_path);
           }
           $path = $request->file('pdf_file')->store("courses/{$course->id}/pdfs", 'public');
           $lesson->update(['pdf_path' => $path]);
       }
       
       return response()->json([
           'success' => true,
           'message' => 'Leçon mise à jour avec succès'
       ]);
   }
    public function destroy(Course $course, Lesson $lesson)
    {
        $this->authorize('update', $course);
        
        if ($lesson->video_path) {
            Storage::disk('public')->delete($lesson->video_path);
        }
        if ($lesson->pdf_path) {
            Storage::disk('public')->delete($lesson->pdf_path);
        }
        
        $lesson->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Leçon supprimée'
        ]);
    }
    
    public function reorder(Request $request, Course $course)
    {
        $this->authorize('update', $course);
        
        $validated = $request->validate([
            'lessons' => 'required|array',
            'lessons.*.id' => 'required|exists:lessons,id',
            'lessons.*.order' => 'required|integer',
            'lessons.*.chapter_id' => 'nullable|integer',
        ]);
        
        foreach ($validated['lessons'] as $lessonData) {
            Lesson::where('id', $lessonData['id'])
                ->where('course_id', $course->id)
                ->update([
                    'order' => $lessonData['order'],
                    'chapter_id' => $lessonData['chapter_id']
                ]);
        }
        
        return response()->json(['success' => true]);
    }
}