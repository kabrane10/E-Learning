<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $instructorId = Auth::id();
        
        $stats = [
            'average' => round(Review::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))->avg('rating') ?? 0, 1),
            'total' => Review::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))->count(),
            '5star' => Review::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))->where('rating', 5)->count(),
            '4star' => Review::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))->where('rating', 4)->count(),
            '3star' => Review::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))->where('rating', 3)->count(),
            '2star' => Review::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))->where('rating', 2)->count(),
            '1star' => Review::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))->where('rating', 1)->count(),
        ];
        
        $reviews = Review::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))
            ->with(['user', 'course'])
            ->latest()
            ->paginate(20);
        
        return view('instructor.reviews.index', compact('stats', 'reviews'));
    }
    
    public function courseReviews(Course $course)
    {
        $this->authorize('view', $course);
        
        $reviews = $course->reviews()
            ->with('user')
            ->latest()
            ->paginate(20);
        
        $stats = [
            'average' => round($course->reviews()->avg('rating') ?? 0, 1),
            'total' => $course->reviews()->count(),
        ];
        
        return view('instructor.reviews.course', compact('course', 'reviews', 'stats'));
    }
    
    public function reply(Request $request, Course $course, Review $review)
    {
        $this->authorize('view', $course);
        
        $validated = $request->validate([
            'reply' => 'required|string|max:1000',
        ]);
        
        $review->update([
            'instructor_reply' => $validated['reply'],
            'replied_at' => now(),
        ]);
        
        return back()->with('success', 'Réponse publiée avec succès !');
    }
}