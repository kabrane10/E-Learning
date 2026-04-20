<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    public function toggle(Course $course)
    {
        $user = Auth::user();
        
        $bookmark = Bookmark::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
            
        if ($bookmark) {
            $bookmark->delete();
            $bookmarked = false;
        } else {
            Bookmark::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);
            $bookmarked = true;
        }
        
        return response()->json([
            'success' => true,
            'bookmarked' => $bookmarked
        ]);
    }
    
    public function index()
    {
        $bookmarks = Auth::user()
            ->bookmarkedCourses()
            ->with(['instructor'])
            ->withCount('lessons')
            ->paginate(12);
            
        return view('student.bookmarks', compact('bookmarks'));
    }
}