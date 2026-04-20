<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    public function show(Course $course)
    {
        $user = Auth::user();
        
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereNotNull('completed_at')
            ->firstOrFail();
        
        return view('student.certificate', compact('course', 'enrollment', 'user'));
    }
    
    public function download(Course $course)
    {
        $user = Auth::user();
        
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereNotNull('completed_at')
            ->firstOrFail();
        
        $pdf = PDF::loadView('student.certificate-pdf', compact('course', 'enrollment', 'user'));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('certificat-' . $course->slug . '.pdf');
    }
}