<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est inscrit
        if (!$course->students()->where('user_id', $user->id)->exists()) {
            abort(403, 'Vous devez être inscrit au cours pour laisser un avis.');
        }
        
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);
        
        Review::updateOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $course->id
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
            ]
        );
        
        return back()->with('success', 'Merci pour votre avis !');
    }
}