<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_courses' => Course::count(),
            'total_enrollments' => Enrollment::count(),
            'completion_rate' => $this->getCompletionRate(),
        ];
        
        $popularCourses = Course::with(['instructor'])
            ->withCount('students')
            ->orderByDesc('students_count')
            ->limit(5)
            ->get();
            
        $latestUsers = User::latest()->limit(5)->get();
        
        $latestCourses = Course::with('instructor')
            ->latest()
            ->limit(5)
            ->get();
            
        $enrollmentsChart = $this->getEnrollmentsChartData();
        
        return view('admin.dashboard', compact(
            'stats',
            'popularCourses',
            'latestUsers',
            'latestCourses',
            'enrollmentsChart'
        ));
    }
    
    private function getCompletionRate()
    {
        $total = Enrollment::count();
        if ($total === 0) {
            return 0;
        }
        
        $completed = Enrollment::whereNotNull('completed_at')->count();
        return round(($completed / $total) * 100);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        // Logique de recherche globale
        return back();
    }
    
    public function notifications()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        return view('admin.notifications', compact('notifications'));
    }
    
    public function markAllNotificationsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Toutes les notifications marquées comme lues');
    }
    
    public function markNotificationRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return back();
    }
    
    public function deleteNotification($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();
        return back()->with('success', 'Notification supprimée');
    }
    
    private function getEnrollmentsChartData()
    {
        $data = Enrollment::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        return [
            'labels' => $data->pluck('date')->map(fn($d) => date('d/m', strtotime($d)))->toArray(),
            'data' => $data->pluck('count')->toArray()
        ];
    }
    
    private function getCategoriesChartData()
    {
        return [
            'labels' => ['Développement', 'Design', 'Marketing', 'Business', 'Data Science'],
            'data' => [35, 25, 20, 12, 8]
        ];
    }
}
    
    
