<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    public function store(Request $request, Course $course, Chapter $chapter = null)
    {
        if ($course->instructor_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content_type' => 'required|in:video,pdf,quiz,text',
            'chapter_id' => 'nullable|exists:chapters,id',
            'video_file' => 'required_if:content_type,video|file|mimes:mp4,mov,avi,webm|max:2048000', // 2GB max
            'pdf_file' => 'required_if:content_type,pdf|file|mimes:pdf|max:51200', // 50MB max
            'duration' => 'nullable|integer',
            'is_free_preview' => 'boolean',
        ]);

        $order = $course->lessons()
            ->where('chapter_id', $chapter?->id)
            ->max('order') + 1;

        $lessonData = [
            'title' => $validated['title'],
            'content_type' => $validated['content_type'],
            'chapter_id' => $chapter?->id,
            'course_id' => $course->id,
            'order' => $order,
            'is_free_preview' => $request->boolean('is_free_preview'),
            'duration' => $validated['duration'] ?? 0,
        ];

        $lesson = Lesson::create($lessonData);

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
            'lesson' => $lesson->load('chapter'),
            'message' => 'Leçon créée avec succès'
        ]);
    }

    public function update(Request $request, Course $course, Lesson $lesson)
    {
        if ($course->instructor_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content_type' => 'required|in:video,pdf,quiz,text',
            'chapter_id' => 'nullable|exists:chapters,id',
            'video_file' => 'nullable|file|mimes:mp4,mov,avi,webm|max:2048000',
            'pdf_file' => 'nullable|file|mimes:pdf|max:51200',
            'duration' => 'nullable|integer',
            'is_free_preview' => 'boolean',
        ]);

        $lesson->update([
            'title' => $validated['title'],
            'content_type' => $validated['content_type'],
            'chapter_id' => $validated['chapter_id'],
            'is_free_preview' => $request->boolean('is_free_preview'),
            'duration' => $validated['duration'] ?? $lesson->duration,
        ]);

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
            'message' => 'Leçon mise à jour'
        ]);
    }

    public function destroy(Course $course, Lesson $lesson)
    {
        if ($course->instructor_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

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
        if ($course->instructor_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

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