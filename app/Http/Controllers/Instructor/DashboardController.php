<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $instructorId = Auth::id();
        
        // ✅ Statistiques réelles
        $stats = [
            'total_courses' => Course::where('instructor_id', $instructorId)->count(),
            'published_courses' => Course::where('instructor_id', $instructorId)->where('is_published', true)->count(),
            'draft_courses' => Course::where('instructor_id', $instructorId)->where('is_published', false)->count(),
            'total_students' => Enrollment::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))
                ->distinct('user_id')
                ->count('user_id'),
            'total_revenue' => $this->calculateTotalRevenue($instructorId),
            'average_rating' => round(Review::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))->avg('rating') ?? 0, 1),
            'total_reviews' => Review::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))->count(),
            'completion_rate' => $this->calculateCompletionRate($instructorId),
        ];
        
        // ✅ Cours récents (réels)
        $recentCourses = Course::where('instructor_id', $instructorId)
            ->withCount(['students', 'lessons'])
            ->withAvg('reviews', 'rating')
            ->latest()
            ->limit(5)
            ->get();
        
        // ✅ Activité récente (réelle)
        $recentActivities = $this->getRecentActivities($instructorId);
        
        // ✅ Données pour les graphiques (réelles)
        $enrollmentsChart = $this->getEnrollmentsChartData($instructorId);
        $revenueChart = $this->getRevenueChartData($instructorId);
        
        // ✅ Top cours (réels)
        $topCourses = Course::where('instructor_id', $instructorId)
            ->withCount('students')
            ->orderBy('students_count', 'desc')
            ->limit(5)
            ->get();
        
        return view('instructor.dashboard.index', compact(
            'stats', 'recentCourses', 'recentActivities', 
            'enrollmentsChart', 'revenueChart', 'topCourses'
        ));
    }
    
    private function calculateTotalRevenue($instructorId): float
    {
        $courses = Course::where('instructor_id', $instructorId)
            ->where('is_free', false)
            ->withCount('students')
            ->get();
        
        $total = 0;
        foreach ($courses as $course) {
            $total += $course->students_count * $course->price * 0.8;
        }
        
        return $total;
    }
    
    private function calculateCompletionRate($instructorId): float
    {
        $total = Enrollment::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))->count();
        if ($total === 0) return 0;
        
        $completed = Enrollment::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))
            ->whereNotNull('completed_at')
            ->count();
            
        return round(($completed / $total) * 100);
    }
    
    private function getRecentActivities($instructorId): array
    {
        $activities = [];
        
        // Dernières inscriptions
        $recentEnrollments = Enrollment::with(['user', 'course'])
            ->whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($e) => [
                'type' => 'enrollment',
                'icon' => 'fa-user-plus',
                'color' => 'green',
                'message' => "{$e->user->name} s'est inscrit à \"{$e->course->title}\"",
                'time' => $e->created_at->diffForHumans(),
            ])->toArray();
        
        // Derniers avis
        $recentReviews = Review::with(['user', 'course'])
            ->whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($r) => [
                'type' => 'review',
                'icon' => 'fa-star',
                'color' => 'yellow',
                'message' => "{$r->user->name} a laissé un avis {$r->rating}★ sur \"{$r->course->title}\"",
                'time' => $r->created_at->diffForHumans(),
            ])->toArray();
        
        // Dernières complétions
        $recentCompletions = Enrollment::with(['user', 'course'])
            ->whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->limit(5)
            ->get()
            ->map(fn($e) => [
                'type' => 'completion',
                'icon' => 'fa-check-circle',
                'color' => 'blue',
                'message' => "{$e->user->name} a terminé \"{$e->course->title}\"",
                'time' => $e->completed_at->diffForHumans(),
            ])->toArray();
        
        $activities = array_merge($recentEnrollments, $recentReviews, $recentCompletions);
        
        // Trier par date (plus récent en premier)
        usort($activities, fn($a, $b) => strtotime($b['time']) <=> strtotime($a['time']));
        
        return array_slice($activities, 0, 5);
    }
    
    private function getEnrollmentsChartData($instructorId): array
    {
        $data = Enrollment::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        if ($data->isEmpty()) {
            return [
                'labels' => ['Aucune donnée'],
                'data' => [0]
            ];
        }
            
        return [
            'labels' => $data->pluck('date')->map(fn($d) => date('d/m', strtotime($d)))->toArray(),
            'data' => $data->pluck('count')->toArray(),
        ];
    }
    
    private function getRevenueChartData($instructorId): array
    {
        // Si pas de système de paiement, retourner des données vides
        return [
            'labels' => ['Semaine 1', 'Semaine 2', 'Semaine 3', 'Semaine 4'],
            'data' => [0, 0, 0, 0],
        ];
    }
}